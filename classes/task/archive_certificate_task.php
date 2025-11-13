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

namespace local_archive_certificate\task;

use core\task\adhoc_task;
use tool_certificate\certificate;
use tool_certificate\template;

defined('MOODLE_INTERNAL') || die();

class archive_certificate_task extends adhoc_task {

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('archivetask', 'local_archive_certificate');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        $data = $this->get_custom_data();

        // Get the certificate issue record
        $issue = $DB->get_record('tool_certificate_issues', ['id' => $data->issueid]);
        if (!$issue) {
            mtrace('Certificate issue not found: ' . $data->issueid);
            return;
        }

        // Get user information
        $user = $DB->get_record('user', ['id' => $data->userid], 'id,username,firstname,lastname');
        if (!$user) {
            mtrace('User not found: ' . $data->userid);
            return;
        }

        try {
            // Generate the certificate PDF if it doesn't exist
            $template = template::instance($issue->templateid);
            $file = $template->create_issue_file($issue, false);

            // Create archive directory if it doesn't exist
            $archivepath = $this->get_archive_path();
            if (!is_dir($archivepath)) {
                if (!mkdir($archivepath, 0755, true)) {
                    throw new \Exception('Could not create archive directory: ' . $archivepath);
                }
            }

            // Generate filename with date, username and names
            $date = date('Y-m-d', $issue->timecreated);
            $filename = sprintf('%s_%s_%s_%s.pdf',
                $date,
                $user->username,
                $user->firstname,
                $user->lastname
            );
            $tmpfilename = clean_filename($filename);
            $archivefilepath = $archivepath . '/' . iconv('UTF-8', 'ASCII//TRANSLIT', $tmpfilename);

            // Copy the certificate to archive
            if ($file->copy_content_to($archivefilepath)) {
                mtrace('Certificate archived successfully: ' . $archivefilepath);
            } else {
                throw new \Exception('Failed to copy certificate to archive: ' . $archivefilepath);
            }

        } catch (\Exception $e) {
            mtrace('Error archiving certificate: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get the archive path from settings.
     *
     * @return string
     */
    private function get_archive_path(): string {
        global $CFG;

        $path = get_config('archive_certificate', 'archivepath');

        // Use default if not set
        if (empty($path)) {
            $path = $CFG->dataroot . '/certarchive';
        }

        // Convert relative path to absolute if needed
        if (!is_absolute_path($path)) {
            $path = $CFG->dataroot . '/' . ltrim($path, '/');
        }

        return $path;
    }
}

/**
 * Helper function to check if path is absolute
 *
 * @param string $path
 * @return bool
 */
function is_absolute_path(string $path): bool {
    return str_starts_with($path, '/') || (PHP_OS_FAMILY === 'Windows' && preg_match('/^[A-Za-z]:/', $path));
}
