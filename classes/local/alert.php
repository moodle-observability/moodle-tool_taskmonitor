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

/**
 * An alert condition detected by the monitor.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class alert {
    /** Critical: users are affected now (cron stopped). */
    public const CRITICAL = 'critical';

    /** Warning: something is degraded. */
    public const WARNING = 'warning';

    /** @var string Stable id, for example "cron" or "adhoc_failing:\core\task\x". */
    public readonly string $id;

    /** @var string Type: cron, scheduled_failing, adhoc_failing, adhoc_stuck or adhoc_waiting. */
    public readonly string $type;

    /** @var string Severity. */
    public readonly string $severity;

    /** @var string Human readable title. */
    public readonly string $title;

    /** @var array Structured details (task class, counts, durations). */
    public readonly array $details;

    /**
     * Constructor.
     *
     * @param string $id Stable id.
     * @param string $type Type.
     * @param string $severity Severity.
     * @param string $title Title.
     * @param array $details Details.
     */
    public function __construct(string $id, string $type, string $severity, string $title, array $details = []) {
        $this->id = $id;
        $this->type = $type;
        $this->severity = $severity;
        $this->title = $title;
        $this->details = $details;
    }

    /**
     * Plain array (stored state, hook and adhoc task data).
     *
     * @return array
     */
    public function to_array(): array {
        return ['id' => $this->id, 'type' => $this->type, 'severity' => $this->severity, 'title' => $this->title,
            'details' => $this->details];
    }
}
