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

/**
 * English strings.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activealerts'] = 'Active alerts';
$string['adhocfaildelay'] = 'Failing adhoc task threshold';
$string['adhocfaildelay_desc'] = 'Alert when an adhoc task has a fail delay of at least this value (Moodle doubles the delay after each failure: 60 s, 120 s, 240 s...). The default, 10 minutes, means about four failures in a row.';
$string['adhocqueue'] = 'Adhoc task queue';
$string['adhocrunning'] = 'Stuck adhoc task threshold';
$string['adhocrunning_desc'] = 'Alert when an adhoc task has been marked as running for longer than this.';
$string['adhocwaiting'] = 'Adhoc queue delay threshold';
$string['adhocwaiting_desc'] = 'Alert when the oldest adhoc task that is due has been waiting for longer than this.';
$string['alert'] = 'Alert';
$string['alert:adhoc_failing'] = 'Adhoc task {$a->classname} is failing repeatedly ({$a->count} queued with a fail delay of up to {$a->faildelay})';
$string['alert:adhoc_stuck'] = 'Adhoc task {$a->classname} has been running for {$a->duration} ({$a->count} tasks)';
$string['alert:adhoc_waiting'] = 'The adhoc task queue is delayed: the oldest due task has been waiting for {$a->duration} ({$a->count} due)';
$string['alert:cron'] = 'Cron is not running: last start {$a->since}';
$string['alert:cron_never'] = 'Cron has never run on this site';
$string['alert:scheduled_failing'] = 'Scheduled task {$a->classname} is failing (fail delay {$a->faildelay})';
$string['alertmessage'] = '{$a->title}

Since: {$a->since}

Task health report: {$a->url}';
$string['alertraised_subject'] = '[{$a->site}] Task monitor alert: {$a->title}';
$string['alertresolved_subject'] = '[{$a->site}] Task monitor alert resolved: {$a->title}';
$string['alertresolvedmessage'] = 'Resolved: {$a->title}

It was active since {$a->since}.

Task health report: {$a->url}';
$string['avgduration'] = 'Average duration';
$string['classname'] = 'Task';
$string['count'] = 'Count';
$string['cron'] = 'Cron';
$string['cronlastrun'] = 'Last cron start: {$a->time} ({$a->ago} ago)';
$string['cronnever'] = 'Cron has never run on this site.';
$string['cronthreshold'] = 'Cron threshold';
$string['cronthreshold_desc'] = 'Alert when cron has not started for longer than this.';
$string['faildelay'] = 'Fail delay';
$string['failing'] = 'Failing';
$string['failures'] = 'Failures';
$string['lastruntime'] = 'Last run';
$string['maxduration'] = 'Maximum duration';
$string['messageprovider:alert'] = 'Task monitor alerts (cron and tasks)';
$string['noalerts'] = 'No active alerts.';
$string['nofailingtasks'] = 'No failing scheduled tasks.';
$string['noqueue'] = 'The adhoc task queue is empty.';
$string['notasklog'] = 'No scheduled task runs logged in the last 24 hours.';
$string['oldestage'] = 'Oldest waiting';
$string['pluginname'] = 'Task monitor';
$string['privacy:metadata'] = 'The task monitor does not store personal data; alerts are delivered through the Moodle messaging system.';
$string['report'] = 'Task health';
$string['running'] = 'Running';
$string['runs'] = 'Runs';
$string['scheduledfailing'] = 'Failing scheduled tasks';
$string['severity'] = 'Severity';
$string['since'] = 'Since';
$string['slowtasks'] = 'Scheduled tasks in the last 24 hours (slowest first)';
$string['task_dispatch_alert'] = 'Deliver a task monitor alert to other plugins';
$string['task_evaluate'] = 'Evaluate cron and task health';
$string['taskmonitor:receivealerts'] = 'Receive task monitor alerts';
$string['taskmonitor:view'] = 'View the task health report';
