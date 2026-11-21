Uso
===

Alertas
-------

Cada alerta tem um tipo, uma severidade e o horário em que começou:

.. list-table::
   :header-rows: 1

   * - Tipo
     - Severidade
     - Condição
   * - ``cron``
     - critical
     - o cron nunca rodou, ou não inicia há mais tempo que o limite.
   * - ``scheduled_failing``
     - warning
     - uma tarefa agendada habilitada tem fail delay (um alerta por tarefa).
   * - ``adhoc_failing``
     - warning
     - adhoc tasks de uma classe têm fail delay acima do limite.
   * - ``adhoc_stuck``
     - warning
     - adhoc tasks de uma classe estão marcadas como em execução há mais tempo que o limite.
   * - ``adhoc_waiting``
     - warning
     - a adhoc task vencida mais antiga espera há mais tempo que o limite.

Uma mensagem é enviada quando o alerta surge e outra quando é resolvido. Enquanto a condição continua, nada é reenviado.

Relatório
---------

*Administração do site > Relatórios > Task health* mostra o estado do cron, os alertas ativos, as tarefas agendadas com falha, as tarefas agendadas mais lentas das últimas 24 horas com suas falhas (do log de tarefas do core, ``{task_log}``, mantido conforme a *retenção do log de tarefas*) e a fila de adhoc tasks agrupada por tarefa.

Prometheus
----------

Com o ``tool_prometheus``, o gauge ``moodle_tool_taskmonitor_alerts_active{type="..."}`` conta os alertas ativos de cada tipo.
