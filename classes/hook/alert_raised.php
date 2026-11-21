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

namespace tool_taskmonitor\hook;

/**
 * Dispatched when an alert is raised or resolved, after the deduplication: each change is delivered once.
 *
 * Use it to deliver alerts to other channels (Slack, Telegram, Teams, PagerDuty, webhooks). Callbacks run in an
 * adhoc task (immediately for cron alerts, because cron is down); keep them fast, without side effects on Moodle
 * and without personal data. A callback that throws does not stop the messages or the other callbacks.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins to receive the alerts raised and resolved by tool_taskmonitor.')]
#[\core\attribute\tags('alerts', 'task')]
final class alert_raised {
    /** The alert started. */
    public const RAISED = 'raised';

    /** The alert ended. */
    public const RESOLVED = 'resolved';

    /** @var array Alert data: id, type, severity, title, details, since. */
    private array $alert;

    /** @var string State. */
    private string $state;

    /**
     * Constructor.
     *
     * @param array $alert Alert data.
     * @param string $state self::RAISED or self::RESOLVED.
     */
    public function __construct(array $alert, string $state) {
        $this->alert = $alert;
        $this->state = $state;
    }

    /**
     * Alert data: id, type (cron, scheduled_failing, adhoc_failing, adhoc_stuck, adhoc_waiting), severity
     * (critical or warning), title, details and since (Unix time).
     *
     * @return array
     */
    public function get_alert(): array {
        return $this->alert;
    }

    /**
     * State: self::RAISED or self::RESOLVED.
     *
     * @return string
     */
    public function get_state(): string {
        return $this->state;
    }
}
