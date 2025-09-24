<?php
// This file is part of the tool_certificate plugin for Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_archive_certificate', get_string('pluginname', 'local_archive_certificate'));

    // Archive path setting
    $settings->add(new admin_setting_configtext(
        'local_archive_certificate/archivepath',
        get_string('archivepath', 'local_archive_certificate'),
        get_string('archivepath_desc', 'local_archive_certificate'),
        'certarchive', // Default relative to dataroot
        PARAM_PATH
    ));

    $ADMIN->add('localplugins', $settings);
}
