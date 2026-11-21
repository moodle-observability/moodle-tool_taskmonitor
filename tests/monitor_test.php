<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace tool_taskmonitor;

use PHPUnit\Framework\Attributes\CoversClass;
use tool_taskmonitor\local\alert;
use tool_taskmonitor\local\monitor;

/**
 * Tests of the alert conditions.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(monitor::class)]
#[CoversClass(alert::class)]
final class monitor_test extends \advanced_testcase {
    /**
     * Inserts an adhoc task record.
     *
     * @param array $fields Fields to override.
     * @return void
     */
    private function adhoc(array $fields): void {
        global $DB;
        $DB->insert_record('task_adhoc', $fields + ['component' => 'core', 'classname' => '\core\task\x_task',
            'nextruntime' => time() + 600, 'faildelay' => 0, 'customdata' => '', 'blocking' => 0, 'timecreated' => time()]);
    }

    /**
     * Cron: never run, recent and stale.
     */
    public function test_cron(): void {
        $this->resetAfterTest();
        $now = time();
        unset_config('lastcronstart', 'tool_task');
        $this->assertSame('cron', monitor::cron($now)->id);
        $this->assertSame(alert::CRITICAL, monitor::cron($now)->severity);

        set_config('lastcronstart', $now - 60, 'tool_task');
        $this->assertNull(monitor::cron($now));

        set_config('lastcronstart', $now - 16 * MINSECS, 'tool_task');
        $this->assertNotNull(monitor::cron($now));
        set_config('cronthreshold', HOURSECS, 'tool_taskmonitor');
        $this->assertNull(monitor::cron($now));
    }

    /**
     * Failing scheduled tasks (disabled ones are ignored).
     */
    public function test_scheduled_failing(): void {
        global $DB;
        $this->resetAfterTest();
        $DB->set_field('task_scheduled', 'faildelay', 0);
        $DB->set_field('task_scheduled', 'faildelay', 240, ['classname' => '\core\task\session_cleanup_task']);
        $DB->set_field('task_scheduled', 'faildelay', 240, ['classname' => '\core\task\backup_cleanup_task']);
        $DB->set_field('task_scheduled', 'disabled', 1, ['classname' => '\core\task\backup_cleanup_task']);
        $alerts = monitor::scheduled_failing();
        $this->assertCount(1, $alerts);
        $this->assertSame('scheduled_failing:\core\task\session_cleanup_task', $alerts[0]->id);
        $this->assertSame(240, $alerts[0]->details['faildelay']);
    }

    /**
     * Adhoc tasks: failing over the threshold, stuck and the delayed queue.
     */
    public function test_adhoc(): void {
        global $DB;
        $this->resetAfterTest();
        $DB->delete_records('task_adhoc');
        $now = time();
        $this->adhoc(['faildelay' => 120]);
        $this->adhoc(['faildelay' => 960]);
        $this->adhoc(['classname' => '\core\task\y_task', 'timestarted' => $now - 2 * HOURSECS, 'hostname' => 'h', 'pid' => 1]);
        $this->adhoc(['classname' => '\core\task\z_task', 'timestarted' => $now - 60, 'hostname' => 'h', 'pid' => 2]);

        $failing = monitor::adhoc_failing();
        $this->assertCount(1, $failing);
        $this->assertSame(1, $failing[0]->details['count']);
        $this->assertSame(960, $failing[0]->details['faildelay']);

        $stuck = monitor::adhoc_stuck($now);
        $this->assertCount(1, $stuck);
        $this->assertSame('adhoc_stuck:\core\task\y_task', $stuck[0]->id);

        $this->assertNull(monitor::adhoc_waiting($now));
        $this->adhoc(['nextruntime' => $now - 2 * HOURSECS]);
        $waiting = monitor::adhoc_waiting($now);
        $this->assertSame('adhoc_waiting', $waiting->id);
        $this->assertSame(1, $waiting->details['count']);

        set_config('lastcronstart', $now, 'tool_task');
        $this->assertSame(
            ['adhoc_failing:\core\task\x_task', 'adhoc_stuck:\core\task\y_task', 'adhoc_waiting'],
            array_keys(monitor::evaluate($now))
        );
    }
}
