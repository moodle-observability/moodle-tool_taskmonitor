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

/**
 * Tests of the hook callbacks and of the report.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(hook_callbacks::class)]
#[CoversClass(output\report::class)]
final class hook_callbacks_test extends \advanced_testcase {
    /**
     * The cron check of web requests is skipped in CLI scripts.
     */
    public function test_after_config_skips_cli(): void {
        $this->resetAfterTest();
        unset_config('lastcronstart', 'tool_task');
        hook_callbacks::after_config(new \core\hook\after_config());
        $this->assertSame([], local\alert_manager::active());
    }

    /**
     * Alert counts as tool_prometheus metrics, when tool_prometheus is installed.
     */
    public function test_collect_metrics(): void {
        if (!class_exists(\tool_prometheus\hook\collect_metrics::class)) {
            $this->markTestSkipped('tool_prometheus is not installed');
        }
        $this->resetAfterTest();
        set_config('alerts', json_encode(['cron' => ['type' => 'cron']]), 'tool_taskmonitor');
        $hook = new \tool_prometheus\hook\collect_metrics();
        hook_callbacks::collect_metrics($hook);
        $samples = $hook->get_metrics()[0]->get_samples();
        $values = array_column(array_map(fn($s) => [$s['labels']['type'], $s['value']], $samples), 1, 0);
        $this->assertSame(1, $values['cron']);
        $this->assertSame(0, $values['adhoc_failing']);
    }

    /**
     * The report exports every section.
     */
    public function test_report(): void {
        global $PAGE;
        $this->resetAfterTest();
        set_config('lastcronstart', time() - 60, 'tool_task');
        $data = (new output\report())->export_for_template($PAGE->get_renderer('core'));
        $this->assertTrue($data['cron']['ok']);
        foreach (['alerts', 'failing', 'slow', 'queue'] as $section) {
            $this->assertArrayHasKey($section, $data);
        }
    }
}
