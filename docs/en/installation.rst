Installation
============

1. Download ``tool_taskmonitor-<version>.zip`` from the `releases <https://github.com/moodle-observability/moodle-tool_taskmonitor/releases>`_, or clone the repository into ``public/admin/tool/taskmonitor``:

   .. code-block:: bash

      git clone https://github.com/moodle-observability/moodle-tool_taskmonitor.git public/admin/tool/taskmonitor

2. Run ``php admin/cli/upgrade.php`` (or open *Site administration > Notifications*).
3. Check that Moodle can send e-mail: alerts use the Moodle messaging system.

Uninstall: *Site administration > Plugins > Plugins overview*, ``tool_taskmonitor``, *Uninstall*.
