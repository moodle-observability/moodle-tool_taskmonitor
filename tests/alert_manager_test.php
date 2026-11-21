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
use tool_taskmonitor\hook\alert_raised;
use tool_taskmonitor\local\alert;
use tool_taskmonitor\local\alert_manager;
use tool_taskmonitor\local\notifier;

/**
 * Tests of the alert state, the messages and the hook.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(alert_manager::class)]
#[CoversClass(notifier::class)]
#[CoversClass(alert_raised::class)]
#[CoversClass(task\dispatch_alert::class)]
#[CoversClass(task\evaluate::class)]
final class alert_manager_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        require_once(__DIR__ . '/fixtures/alert_callbacks.php');
    }

    #[\Override]
    protected function setUp(): void {
        parent::setUp();
        fixtures\alert_callbacks::$received = [];
        $file = make_request_directory() . '/hooks.php';
        $callbacks = [
            ['hook' => alert_raised::class, 'callback' => [fixtures\alert_callbacks::class, 'fail'], 'priority' => 200],
            ['hook' => alert_raised::class, 'callback' => [fixtures\alert_callbacks::class, 'record']],
        ];
        file_put_contents($file, '<?php $callbacks = ' . var_export($callbacks, true) . ';');
        \core\di::set(\core\hook\manager::class, \core\hook\manager::phpunit_get_instance(['local_alerts' => $file]));
    }

    /**
     * An alert.
     *
     * @param string $id Id.
     * @param string $type Type.
     * @return alert
     */
    private function alert(string $id, string $type = 'adhoc_failing'): alert {
        return new alert($id, $type, alert::WARNING, "Title {$id}");
    }

    /**
     * Raised once, kept without repetition, resolved once; messages to admins and capability holders.
     */
    public function test_sync_and_messages(): void {
        $this->resetAfterTest();
        $this->preventResetByRollback();
        $user = $this->getDataGenerator()->create_user();
        $roleid = create_role('Alert receiver', 'alertreceiver', '');
        assign_capability('tool/taskmonitor:receivealerts', CAP_ALLOW, $roleid, \context_system::instance());
        role_assign($roleid, $user->id, \context_system::instance());
        $sink = $this->redirectMessages();

        $first = alert_manager::sync(['a' => $this->alert('a')], null, 1000);
        $this->assertSame(['a'], array_keys($first['raised']));
        // Admin and the user with the capability.
        $this->assertCount(2, $sink->get_messages());
        $this->assertStringContainsString('Title a', $sink->get_messages()[0]->subject);

        $second = alert_manager::sync(['a' => $this->alert('a')], null, 2000);
        $this->assertSame([], $second['raised']);
        $this->assertSame(1000, alert_manager::active()['a']['since']);
        $this->assertCount(2, $sink->get_messages());

        $third = alert_manager::sync([], null, 3000);
        $this->assertSame(['a'], array_keys($third['resolved']));
        $this->assertSame([], alert_manager::active());
        $this->assertCount(4, $sink->get_messages());
        $this->assertStringContainsString('resolved', $sink->get_messages()[3]->subject);
        $sink->close();
        // The failing callback was reported and the recording callback still ran (in the adhoc tasks).
        $this->assertSame([], fixtures\alert_callbacks::$received);
        $this->runAdhocTasks(task\dispatch_alert::class);
        $this->assertSame([['raised', 'a'], ['resolved', 'a']], fixtures\alert_callbacks::$received);
        $this->assertDebuggingCalledCount(2);
    }

    /**
     * Alerts outside the scope are not resolved; cron alerts reach the hook immediately.
     */
    public function test_scope_and_cron(): void {
        $this->resetAfterTest();
        $sink = $this->redirectMessages();
        alert_manager::sync(['a' => $this->alert('a'), 'cron' => $this->alert('cron', 'cron')], null, 1000);
        $this->assertDebuggingCalled();
        $this->assertSame([['raised', 'cron']], fixtures\alert_callbacks::$received);

        // Only cron evaluated: the adhoc alert stays active.
        $changes = alert_manager::sync([], ['cron'], 2000);
        $this->assertDebuggingCalled();
        $this->assertSame(['cron'], array_keys($changes['resolved']));
        $this->assertSame(['a'], array_keys(alert_manager::active()));
        $sink->close();
    }

    /**
     * The scheduled task evaluates and reports.
     */
    public function test_evaluate_task(): void {
        $this->resetAfterTest();
        set_config('lastcronstart', time(), 'tool_task');
        $sink = $this->redirectMessages();
        ob_start();
        (new task\evaluate())->execute();
        $output = ob_get_clean();
        $sink->close();
        $this->assertStringContainsString('Task monitor:', $output);
    }
}
