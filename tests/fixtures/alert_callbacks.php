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

namespace tool_taskmonitor\fixtures;

use tool_taskmonitor\hook\alert_raised;

/**
 * Records the alerts received by the alert_raised hook in the tests.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class alert_callbacks {
    /** @var array Received alerts: [state, alert id]. */
    public static array $received = [];

    /**
     * Records an alert.
     *
     * @param alert_raised $hook Hook.
     */
    public static function record(alert_raised $hook): void {
        self::$received[] = [$hook->get_state(), $hook->get_alert()['id']];
    }

    /**
     * Always fails.
     *
     * @param alert_raised $hook Hook.
     */
    public static function fail(alert_raised $hook): void {
        throw new \moodle_exception('error');
    }
}
