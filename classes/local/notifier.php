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
 * Sends alerts through the Moodle messaging API.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class notifier {
    /**
     * Recipients: site administrators and users with tool/taskmonitor:receivealerts.
     *
     * @return \stdClass[] Users keyed by id.
     */
    public static function recipients(): array {
        $users = get_admins();
        $context = \context_system::instance();
        foreach (get_users_by_capability($context, 'tool/taskmonitor:receivealerts', 'u.*') as $user) {
            $users[$user->id] = $user;
        }
        return array_filter($users, fn($user) => empty($user->deleted) && empty($user->suspended));
    }

    /**
     * Sends a raised or resolved alert to every recipient.
     *
     * @param array $data Alert data with "since".
     * @param string $state alert_raised::RAISED or alert_raised::RESOLVED.
     * @return int Number of messages sent.
     */
    public static function send(array $data, string $state): int {
        global $SITE;
        $url = new \moodle_url('/admin/tool/taskmonitor/index.php');
        $a = [
            'site' => format_string($SITE->shortname),
            'title' => $data['title'],
            'since' => userdate((int) $data['since']),
            'url' => $url->out(false),
        ];
        $resolved = $state === alert_raised::RESOLVED;
        $subject = get_string($resolved ? 'alertresolved_subject' : 'alertraised_subject', 'tool_taskmonitor', $a);
        $body = get_string($resolved ? 'alertresolvedmessage' : 'alertmessage', 'tool_taskmonitor', $a);
        $sent = 0;
        foreach (self::recipients() as $user) {
            $message = new \core\message\message();
            $message->component = 'tool_taskmonitor';
            $message->name = 'alert';
            $message->userfrom = \core_user::get_noreply_user();
            $message->userto = $user;
            $message->subject = $subject;
            $message->fullmessage = $body;
            $message->fullmessageformat = FORMAT_PLAIN;
            $message->fullmessagehtml = nl2br(s($body));
            $message->smallmessage = $subject;
            $message->notification = 1;
            $message->contexturl = $url->out(false);
            $message->contexturlname = get_string('report', 'tool_taskmonitor');
            if (message_send($message)) {
                $sent++;
            }
        }
        return $sent;
    }
}
