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

namespace local_archive_certificate\observer;

use core\task\manager;
use local_archive_certificate\task\archive_certificate_task;

defined('MOODLE_INTERNAL') || die();

class certificate_observer {

    /**
     * Observer for certificate issued event.
     *
     * @param \tool_certificate\event\certificate_issued $event
     */
    public static function certificate_issued(\tool_certificate\event\certificate_issued $event): void {
        // Create adhoc task to archive the certificate
        $task = new archive_certificate_task();
        $task->set_custom_data([
            'issueid' => $event->objectid,
            'userid' => $event->relateduserid,
            'code' => $event->other['code'],
            'courseid' => $event->courseid ?? 0,
            'timecreated' => $event->timecreated
        ]);
        $task->set_next_run_time(time() + 3 * DAYSECS);

        manager::queue_adhoc_task($task);
    }
}
