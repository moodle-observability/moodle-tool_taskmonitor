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

namespace tool_taskmonitor\task;

use tool_taskmonitor\local\alert_manager;
use tool_taskmonitor\local\monitor;

/**
 * Evaluates cron and task health every minute and notifies the changes.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class evaluate extends \core\task\scheduled_task {
    #[\Override]
    public function get_name(): string {
        return get_string('task_evaluate', 'tool_taskmonitor');
    }

    #[\Override]
    public function execute(): void {
        $changes = alert_manager::sync(monitor::evaluate());
        mtrace('Task monitor: ' . count(alert_manager::active()) . ' active alerts, ' . count($changes['raised'])
            . ' raised, ' . count($changes['resolved']) . ' resolved');
    }
}
