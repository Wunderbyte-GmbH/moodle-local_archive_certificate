
# Certificate Archive Plugin

This local Moodle plugin automatically creates an archive of certificates issued through the tool_certificate plugin.

## Features

- Automatically archives certificates when they are issued
- Configurable archive path (default: dataroot/certarchive)
- Files are named with pattern: YYYY-MM-DD_username_firstname_lastname.pdf
- Uses adhoc tasks for reliable background processing
- Supports both absolute and relative paths

## Installation

1. Copy the plugin to `local/certarchive/`
2. Visit the site administration to complete installation
3. Configure the archive path in Site Administration > Plugins > Local plugins > Certificate Archive

## Configuration

The archive path can be configured in the plugin settings:
- **Archive Path**: Path where certificates will be stored (default: certarchive)
  - Relative paths are relative to Moodle's dataroot
  - Absolute paths can also be used

## Requirements

- Moodle 4.1 or higher
- tool_certificate plugin installed and configured

## How it works

1. When a certificate is issued, the `certificate_issued` event is triggered
2. The plugin's event observer catches this event
3. An adhoc task is queued to handle the archiving
4. The task generates/retrieves the certificate PDF and copies it to the archive directory
5. Files are named according to the pattern: YYYY-MM-DD_username_firstname_lastname.pdf

## File Structure

```
local/certarchive/
├── version.php              # Plugin version and metadata
├── settings.php             # Admin settings page
├── db/
│   ├── events.php          # Event observer registration
│   └── install.php         # Installation procedures
├── classes/
│   ├── observer/
│   │   └── certificate_observer.php  # Event observer class
│   └── task/
│       └── archive_certificate_task.php  # Adhoc task class
├── lang/
│   ├── en/
│   │   └── archive_certificate.php  # English language strings
│   └── de/
│       └── archive_certificate.php  # German language strings
└── README.md               # This file
