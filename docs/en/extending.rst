Extending
=========

Other plugins receive the raised and resolved alerts through the ``\tool_taskmonitor\hook\alert_raised`` hook, for example to deliver them to Slack, Telegram, Teams, PagerDuty or a webhook.

``db/hooks.php``:

.. code-block:: php

   $callbacks = [
       [
           'hook' => \tool_taskmonitor\hook\alert_raised::class,
           'callback' => [\local_slackalerts\callbacks::class, 'alert'],
       ],
   ];

Callback:

.. code-block:: php

   public static function alert(\tool_taskmonitor\hook\alert_raised $hook): void {
       $alert = $hook->get_alert(); // id, type, severity, title, details, since.
       $state = $hook->get_state(); // "raised" or "resolved".
       // Send it to the external channel.
   }

How it is delivered
-------------------

* Each change is delivered once, after the deduplication.
* Callbacks run in an adhoc task (*Deliver a task monitor alert to other plugins*), outside the evaluation task, so a slow channel does not delay the other tasks. Cron alerts are delivered immediately, because an adhoc task would not run while cron is down.
* Each callback runs on its own: an exception is reported with ``debugging()`` and does not stop the others or the Moodle messages.
* Keep callbacks fast, without side effects in Moodle and without personal data.
