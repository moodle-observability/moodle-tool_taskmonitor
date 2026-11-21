Configuration
=============

*Site administration > Plugins > Admin tools > Task monitor*:

.. list-table::
   :header-rows: 1

   * - Setting
     - Default
     - Alert when
   * - Cron threshold
     - 15 minutes
     - cron has not started for longer than this.
   * - Adhoc queue delay threshold
     - 1 hour
     - the oldest due adhoc task has been waiting for longer than this.
   * - Stuck adhoc task threshold
     - 1 hour
     - an adhoc task has been marked as running for longer than this.
   * - Failing adhoc task threshold
     - 10 minutes
     - an adhoc task has a fail delay of at least this value (Moodle doubles it after each failure, so 10 minutes is about four failures in a row).

Recipients
----------

Site administrators always receive the alerts. To add other people, give them the capability ``tool/taskmonitor:receivealerts`` in the system context (for example through a system role). Each recipient chooses the channels (e-mail, web notifications) in *Preferences > Notification preferences > Task monitor alerts*.

The report requires ``tool/taskmonitor:view`` (managers have it by default).

Scheduled task
--------------

*Evaluate cron and task health* runs every minute. Keep it enabled: it raises and resolves the task alerts. Cron alerts are also evaluated on web requests.
