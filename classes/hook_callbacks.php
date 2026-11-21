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

use tool_taskmonitor\local\alert_manager;
use tool_taskmonitor\local\monitor;

/**
 * Hook callbacks.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class hook_callbacks {
    /**
     * Checks cron on web requests, at most once per minute across the web servers.
     *
     * @param \core\hook\after_config $hook Hook.
     * @return void
     */
    public static function after_config(\core\hook\after_config $hook): void {
        global $CFG;
        // Test sites never run cron: checking it there would only raise alerts.
        if (
            CLI_SCRIPT || during_initial_install() || !empty($CFG->upgraderunning) || defined('ABORT_AFTER_CONFIG')
                || defined('BEHAT_SITE_RUNNING')
        ) {
            return;
        }
        try {
            $cache = \cache::make('tool_taskmonitor', 'throttle');
            if ($cache->get('croncheck')) {
                return;
            }
            $cache->set('croncheck', 1);
            $alert = monitor::cron();
            alert_manager::sync($alert ? [$alert->id => $alert] : [], ['cron']);
        } catch (\Throwable $e) {
            // Never break a page because of the monitor.
            debugging('tool_taskmonitor: cron check failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Active alerts as metrics of tool_prometheus.
     *
     * @param \tool_prometheus\hook\collect_metrics $hook Hook.
     * @return void
     */
    public static function collect_metrics(\tool_prometheus\hook\collect_metrics $hook): void {
        $counts = array_fill_keys(['cron', 'scheduled_failing', 'adhoc_failing', 'adhoc_stuck', 'adhoc_waiting'], 0);
        foreach (alert_manager::active() as $alert) {
            $counts[$alert['type']] = ($counts[$alert['type']] ?? 0) + 1;
        }
        foreach ($counts as $type => $count) {
            $hook->add_gauge(
                'tool_taskmonitor',
                'moodle_tool_taskmonitor_alerts_active',
                'Active task monitor alerts by type.',
                $count,
                ['type' => $type]
            );
        }
    }
}
