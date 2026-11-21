moodle-tool_taskmonitor
=======================

**Cron and task health monitor for Moodle.**

Watches the Moodle cron and the scheduled and adhoc tasks, keeps a history of durations and failures, and alerts administrators when something stops or keeps failing.

.. note::

   Under development, targeting the ``v0.1.0 – MVP`` milestone. There is no release yet.
   Follow the progress in the `issues <https://github.com/moodle-observability/moodle-tool_taskmonitor/issues>`_.

Planned features
----------------

* Detects a cron that stopped running (configurable threshold).
* Records scheduled task duration and failures, with retention.
* Detects adhoc tasks that are stuck or failing repeatedly.
* Admin report page with a task health overview.
* Alerts through the Moodle messaging API, without repeated spam.

Requirements
------------

* Moodle 5.3 or later.
* PHP version supported by your Moodle release.
* PostgreSQL or MariaDB.

Links
-----

* `Source code <https://github.com/moodle-observability/moodle-tool_taskmonitor>`_
* `Issues <https://github.com/moodle-observability/moodle-tool_taskmonitor/issues>`_
* `Moodle Observability <https://moodle-observability.github.io/>`_

.. toctree::
   :hidden:
   :maxdepth: 1

   installation
   configuration
   usage
