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
 * Admin settings and report page.
 *
 * @package    tool_taskmonitor
 * @copyright  2026 Moodle Observability contributors
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('reports', new admin_externalpage(
        'tool_taskmonitor_report',
        get_string('report', 'tool_taskmonitor'),
        new moodle_url('/admin/tool/taskmonitor/index.php'),
        'tool/taskmonitor:view'
    ));

    $settings = new admin_settingpage('tool_taskmonitor', get_string('pluginname', 'tool_taskmonitor'));
    $ADMIN->add('tools', $settings);
    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_configduration(
            'tool_taskmonitor/cronthreshold',
            get_string('cronthreshold', 'tool_taskmonitor'),
            get_string('cronthreshold_desc', 'tool_taskmonitor'),
            15 * MINSECS,
            MINSECS
        ));
        $settings->add(new admin_setting_configduration(
            'tool_taskmonitor/adhocwaiting',
            get_string('adhocwaiting', 'tool_taskmonitor'),
            get_string('adhocwaiting_desc', 'tool_taskmonitor'),
            HOURSECS,
            MINSECS
        ));
        $settings->add(new admin_setting_configduration(
            'tool_taskmonitor/adhocrunning',
            get_string('adhocrunning', 'tool_taskmonitor'),
            get_string('adhocrunning_desc', 'tool_taskmonitor'),
            HOURSECS,
            MINSECS
        ));
        $settings->add(new admin_setting_configduration(
            'tool_taskmonitor/adhocfaildelay',
            get_string('adhocfaildelay', 'tool_taskmonitor'),
            get_string('adhocfaildelay_desc', 'tool_taskmonitor'),
            10 * MINSECS,
            MINSECS
        ));
    }
}
