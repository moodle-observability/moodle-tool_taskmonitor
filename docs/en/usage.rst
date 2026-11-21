Usage
=====

Alerts
------

Each alert has a type, a severity and the time it started:

.. list-table::
   :header-rows: 1

   * - Type
     - Severity
     - Condition
   * - ``cron``
     - critical
     - cron has never run, or has not started for longer than the threshold.
   * - ``scheduled_failing``
     - warning
     - an enabled scheduled task has a fail delay (one alert per task).
   * - ``adhoc_failing``
     - warning
     - adhoc tasks of one class have a fail delay over the threshold.
   * - ``adhoc_stuck``
     - warning
     - adhoc tasks of one class are marked as running for longer than the threshold.
   * - ``adhoc_waiting``
     - warning
     - the oldest due adhoc task is waiting for longer than the threshold.

A message is sent when the alert is raised and when it is resolved. While the condition lasts, nothing is sent again.

Report
------

*Site administration > Reports > Task health* shows the cron status, the active alerts, the failing scheduled tasks, the slowest scheduled tasks of the last 24 hours with their failures (from the core task log, ``{task_log}``, kept according to *Task log retention*) and the adhoc queue grouped by task.

Prometheus
----------

With ``tool_prometheus``, the gauge ``moodle_tool_taskmonitor_alerts_active{type="..."}`` counts the active alerts of each type.
