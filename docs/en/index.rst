moodle-tool_taskmonitor
=======================

**Cron and task health monitor for Moodle.**

Watches the Moodle cron and the scheduled and adhoc tasks, alerts administrators when something stops or keeps failing, and shows a task health report.

.. note::

   Version 0.1.0 (alpha). Follow the progress in the `issues <https://github.com/moodle-observability/moodle-tool_taskmonitor/issues>`_.

Features
--------

* **Cron not running**: alert when cron has not started for longer than a threshold (15 minutes by default). Because a stopped cron cannot report itself, web requests check it too, at most once per minute.
* **Failing scheduled tasks**: alert for enabled scheduled tasks with a fail delay.
* **Adhoc tasks**: alert for tasks failing repeatedly, tasks stuck as running and a delayed queue.
* **Alerts without spam**: a message when an alert is raised and another when it is resolved, never repeated while it stays active.
* **Delivery**: Moodle messaging (e-mail and notifications) to site administrators and users with ``tool/taskmonitor:receivealerts``, plus a hook for other channels (:doc:`extending`).
* **Report**: *Site administration > Reports > Task health*, with cron status, active alerts, failing scheduled tasks, the slowest scheduled tasks of the last 24 hours (from the core task log) and the adhoc queue by task.
* **Metrics**: with ``tool_prometheus`` installed, active alerts are exported as ``moodle_tool_taskmonitor_alerts_active``.

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
   extending
