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

namespace tool_taskmonitor\output;

use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;
use tool_taskmonitor\local\alert_manager;
use tool_taskmonitor\local\monitor;

/**
 * Task health report.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class report implements renderable, templatable {
    #[\Override]
    public function export_for_template(renderer_base $output): array {
        global $DB;
        $now = time();

        $last = (int) get_config('tool_task', 'lastcronstart');
        $cronalert = monitor::cron($now);
        $cron = [
            'ok' => $cronalert === null,
            'text' => $last
                ? get_string('cronlastrun', 'tool_taskmonitor', ['time' => userdate($last), 'ago' => format_time($now - $last)])
                : get_string('cronnever', 'tool_taskmonitor'),
        ];

        $alerts = [];
        foreach (alert_manager::active() as $alert) {
            $alerts[] = [
                'title' => $alert['title'],
                'severity' => $alert['severity'],
                'critical' => $alert['severity'] === 'critical',
                'since' => userdate((int) $alert['since']),
            ];
        }

        $failing = [];
        foreach (
            $DB->get_records_select(
                'task_scheduled',
                'faildelay > 0',
                null,
                'faildelay DESC',
                'id, classname, faildelay, lastruntime',
                0,
                50
            ) as $task
        ) {
            $failing[] = [
                'classname' => $task->classname,
                'faildelay' => format_time((int) $task->faildelay),
                'lastruntime' => $task->lastruntime ? userdate((int) $task->lastruntime) : '-',
            ];
        }

        $slow = [];
        $sql = 'SELECT classname, COUNT(1) AS runs, SUM(result) AS failures, AVG(timeend - timestart) AS avgduration,
                       MAX(timeend - timestart) AS maxduration
                  FROM {task_log}
                 WHERE type = :type AND timestart >= :since
              GROUP BY classname
              ORDER BY MAX(timeend - timestart) DESC';
        $params = ['type' => \core\task\database_logger::TYPE_SCHEDULED, 'since' => $now - DAYSECS];
        foreach ($DB->get_records_sql($sql, $params, 0, 15) as $row) {
            $slow[] = [
                'classname' => $row->classname,
                'runs' => (int) $row->runs,
                'failures' => (int) $row->failures,
                'hasfailures' => (int) $row->failures > 0,
                'avgduration' => format_float((float) $row->avgduration, 2) . ' s',
                'maxduration' => format_float((float) $row->maxduration, 2) . ' s',
            ];
        }

        $queue = [];
        $sql = 'SELECT classname, COUNT(1) AS total,
                       SUM(CASE WHEN faildelay > 0 THEN 1 ELSE 0 END) AS failing,
                       SUM(CASE WHEN timestarted IS NOT NULL THEN 1 ELSE 0 END) AS running,
                       MIN(CASE WHEN timestarted IS NULL THEN timecreated ELSE NULL END) AS oldest
                  FROM {task_adhoc}
              GROUP BY classname
              ORDER BY COUNT(1) DESC';
        foreach ($DB->get_records_sql($sql, null, 0, 20) as $row) {
            $queue[] = [
                'classname' => $row->classname,
                'count' => (int) $row->total,
                'failing' => (int) $row->failing,
                'running' => (int) $row->running,
                'oldest' => $row->oldest ? format_time($now - (int) $row->oldest) : '-',
            ];
        }

        return [
            'cron' => $cron,
            'alerts' => $alerts,
            'hasalerts' => !empty($alerts),
            'failing' => $failing,
            'hasfailing' => !empty($failing),
            'slow' => $slow,
            'hasslow' => !empty($slow),
            'queue' => $queue,
            'hasqueue' => !empty($queue),
        ];
    }
}
