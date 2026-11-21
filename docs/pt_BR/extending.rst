Extensão
========

Outros plugins recebem os alertas levantados e resolvidos pelo hook ``\tool_taskmonitor\hook\alert_raised``, por exemplo para entregá-los no Slack, Telegram, Teams, PagerDuty ou num webhook.

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
       $state = $hook->get_state(); // "raised" ou "resolved".
       // Envie para o canal externo.
   }

Como é entregue
---------------

* Cada mudança é entregue uma vez, depois da deduplicação.
* Os callbacks rodam numa adhoc task (*Deliver a task monitor alert to other plugins*), fora da tarefa de avaliação, para que um canal lento não atrase as outras tarefas. Alertas de cron são entregues na hora, porque uma adhoc task não rodaria com o cron parado.
* Cada callback roda isolado: uma exceção é informada com ``debugging()`` e não impede os demais nem as mensagens do Moodle.
* Mantenha os callbacks rápidos, sem efeitos colaterais no Moodle e sem dados pessoais.
