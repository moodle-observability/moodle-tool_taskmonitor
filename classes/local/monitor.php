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
 * Detects the alert conditions from the task tables and the cron state. Read only.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class monitor {
    /** Maximum number of task alerts of each type (one per task class). */
    public const MAX_PER_TYPE = 20;

    /**
     * Configured threshold, in seconds.
     *
     * @param string $name Setting name.
     * @param int $default Default value.
     * @return int
     */
    public static function threshold(string $name, int $default): int {
        $value = (int) get_config('tool_taskmonitor', $name);
        return $value > 0 ? $value : $default;
    }

    /**
     * Cron alert, if cron is not running.
     *
     * @param int|null $now Current time.
     * @return alert|null
     */
    public static function cron(?int $now = null): ?alert {
        $now = $now ?? time();
        $last = (int) get_config('tool_task', 'lastcronstart');
        if ($last === 0) {
            return new alert('cron', 'cron', alert::CRITICAL, get_string('alert:cron_never', 'tool_taskmonitor'));
        }
        if ($now - $last > self::threshold('cronthreshold', 15 * MINSECS)) {
            $title = get_string('alert:cron', 'tool_taskmonitor', ['since' => userdate($last)]);
            return new alert('cron', 'cron', alert::CRITICAL, $title, ['lastcronstart' => $last]);
        }
        return null;
    }

    /**
     * Every active alert condition.
     *
     * @param int|null $now Current time.
     * @return alert[] Keyed by alert id.
     */
    public static function evaluate(?int $now = null): array {
        $now = $now ?? time();
        $alerts = [];
        foreach (array_filter([self::cron($now)]) as $alert) {
            $alerts[$alert->id] = $alert;
        }
        foreach (array_merge(self::scheduled_failing(), self::adhoc_failing(), self::adhoc_stuck($now)) as $alert) {
            $alerts[$alert->id] = $alert;
        }
        if ($waiting = self::adhoc_waiting($now)) {
            $alerts[$waiting->id] = $waiting;
        }
        return $alerts;
    }

    /**
     * Enabled scheduled tasks with a fail delay.
     *
     * @return alert[]
     */
    public static function scheduled_failing(): array {
        global $DB;
        $records = $DB->get_records_select(
            'task_scheduled',
            'faildelay > 0 AND disabled = 0',
            null,
            'faildelay DESC',
            'id, classname, faildelay',
            0,
            self::MAX_PER_TYPE
        );
        $alerts = [];
        foreach ($records as $record) {
            $a = ['classname' => $record->classname, 'faildelay' => format_time((int) $record->faildelay)];
            $alerts[] = new alert(
                'scheduled_failing:' . $record->classname,
                'scheduled_failing',
                alert::WARNING,
                get_string('alert:scheduled_failing', 'tool_taskmonitor', $a),
                ['classname' => $record->classname, 'faildelay' => (int) $record->faildelay]
            );
        }
        return $alerts;
    }

    /**
     * Adhoc task classes with tasks failing repeatedly (fail delay above the threshold).
     *
     * @return alert[]
     */
    public static function adhoc_failing(): array {
        global $DB;
        $sql = 'SELECT classname, COUNT(1) AS total, MAX(faildelay) AS maxdelay
                  FROM {task_adhoc}
                 WHERE faildelay >= :threshold
              GROUP BY classname
              ORDER BY MAX(faildelay) DESC';
        $records = $DB->get_records_sql(
            $sql,
            ['threshold' => self::threshold('adhocfaildelay', 10 * MINSECS)],
            0,
            self::MAX_PER_TYPE
        );
        $alerts = [];
        foreach ($records as $record) {
            $a = ['classname' => $record->classname, 'count' => (int) $record->total,
                'faildelay' => format_time((int) $record->maxdelay)];
            $alerts[] = new alert(
                'adhoc_failing:' . $record->classname,
                'adhoc_failing',
                alert::WARNING,
                get_string('alert:adhoc_failing', 'tool_taskmonitor', $a),
                ['classname' => $record->classname, 'count' => (int) $record->total, 'faildelay' => (int) $record->maxdelay]
            );
        }
        return $alerts;
    }

    /**
     * Adhoc task classes with tasks marked as running for longer than the threshold.
     *
     * @param int $now Current time.
     * @return alert[]
     */
    public static function adhoc_stuck(int $now): array {
        global $DB;
        $sql = 'SELECT classname, COUNT(1) AS total, MIN(timestarted) AS started
                  FROM {task_adhoc}
                 WHERE timestarted IS NOT NULL AND timestarted < :limit
              GROUP BY classname
              ORDER BY MIN(timestarted)';
        $records = $DB->get_records_sql(
            $sql,
            ['limit' => $now - self::threshold('adhocrunning', HOURSECS)],
            0,
            self::MAX_PER_TYPE
        );
        $alerts = [];
        foreach ($records as $record) {
            $duration = $now - (int) $record->started;
            $a = ['classname' => $record->classname, 'count' => (int) $record->total, 'duration' => format_time($duration)];
            $alerts[] = new alert(
                'adhoc_stuck:' . $record->classname,
                'adhoc_stuck',
                alert::WARNING,
                get_string('alert:adhoc_stuck', 'tool_taskmonitor', $a),
                ['classname' => $record->classname, 'count' => (int) $record->total, 'running' => $duration]
            );
        }
        return $alerts;
    }

    /**
     * Whole queue delayed: the oldest due adhoc task waits longer than the threshold.
     *
     * @param int $now Current time.
     * @return alert|null
     */
    public static function adhoc_waiting(int $now): ?alert {
        global $DB;
        $record = $DB->get_record_sql('SELECT COUNT(1) AS total, MIN(nextruntime) AS oldest
                                         FROM {task_adhoc}
                                        WHERE timestarted IS NULL AND nextruntime <= :now', ['now' => $now]);
        if (!$record || !$record->total || $now - (int) $record->oldest <= self::threshold('adhocwaiting', HOURSECS)) {
            return null;
        }
        $waiting = $now - (int) $record->oldest;
        $a = ['count' => (int) $record->total, 'duration' => format_time($waiting)];
        return new alert(
            'adhoc_waiting',
            'adhoc_waiting',
            alert::WARNING,
            get_string('alert:adhoc_waiting', 'tool_taskmonitor', $a),
            ['count' => (int) $record->total, 'waiting' => $waiting]
        );
    }
}
