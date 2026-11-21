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

namespace tool_taskmonitor\local;

use tool_taskmonitor\hook\alert_raised;

/**
 * Keeps the state of the alerts and notifies only when an alert is raised or resolved (never repeatedly).
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class alert_manager {
    /**
     * Stored active alerts, keyed by id, each with the "since" time.
     *
     * @return array<string, array>
     */
    public static function active(): array {
        $state = json_decode((string) get_config('tool_taskmonitor', 'alerts'), true);
        return is_array($state) ? $state : [];
    }

    /**
     * Synchronises the stored alerts with the current conditions and notifies the changes.
     *
     * @param alert[] $current Current alerts keyed by id.
     * @param string[]|null $scope Alert types evaluated now; alerts of other types are left untouched (null: all).
     * @param int|null $now Current time.
     * @return array{raised: array, resolved: array}
     */
    public static function sync(array $current, ?array $scope = null, ?int $now = null): array {
        $now = $now ?? time();
        $active = self::active();
        $raised = [];
        $resolved = [];
        foreach ($current as $id => $alert) {
            if (!isset($active[$id])) {
                $active[$id] = $alert->to_array() + ['since' => $now];
                $raised[$id] = $active[$id];
            } else {
                // Keep the original "since" and refresh the title and details.
                $active[$id] = $alert->to_array() + ['since' => $active[$id]['since']];
            }
        }
        foreach ($active as $id => $data) {
            if (!isset($current[$id]) && ($scope === null || in_array($data['type'], $scope, true))) {
                $resolved[$id] = $data;
                unset($active[$id]);
            }
        }
        if ($raised || $resolved || $current) {
            set_config('alerts', json_encode($active), 'tool_taskmonitor');
        }
        foreach ($raised as $data) {
            self::notify($data, alert_raised::RAISED);
        }
        foreach ($resolved as $data) {
            self::notify($data, alert_raised::RESOLVED);
        }
        return ['raised' => $raised, 'resolved' => $resolved];
    }

    /**
     * Sends the message and delivers the hook (in an adhoc task, except for cron alerts).
     *
     * @param array $data Alert data with "since".
     * @param string $state alert_raised::RAISED or alert_raised::RESOLVED.
     * @return void
     */
    public static function notify(array $data, string $state): void {
        try {
            notifier::send($data, $state);
        } catch (\Throwable $e) {
            debugging('tool_taskmonitor: could not send the alert message: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
        if ($data['type'] === 'cron') {
            // Cron is down: an adhoc task would never run.
            self::dispatch($data, $state);
        } else {
            $task = new \tool_taskmonitor\task\dispatch_alert();
            $task->set_custom_data(['alert' => $data, 'state' => $state]);
            \core\task\manager::queue_adhoc_task($task);
        }
    }

    /**
     * Dispatches the alert_raised hook; a failing callback never stops the others or the monitor.
     *
     * @param array $data Alert data.
     * @param string $state State.
     * @return void
     */
    public static function dispatch(array $data, string $state): void {
        $hook = new alert_raised($data, $state);
        // Each callback runs on its own: one failure must not stop the delivery to the others.
        foreach (\core\di::get(\core\hook\manager::class)->getListenersForEvent($hook) as $callback) {
            try {
                $callback($hook);
            } catch (\Throwable $e) {
                debugging('tool_taskmonitor: an alert_raised callback failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }
    }
}
