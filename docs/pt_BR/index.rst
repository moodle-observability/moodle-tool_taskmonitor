moodle-tool_taskmonitor
=======================

**Monitor de cron e tarefas para o Moodle.**

Acompanha o cron do Moodle e as tarefas agendadas e adhoc, avisa os administradores quando algo para ou falha repetidamente e mostra um relatório de saúde das tarefas.

.. note::

   Versão 0.1.0 (alfa). Acompanhe o andamento nas `issues <https://github.com/moodle-observability/moodle-tool_taskmonitor/issues>`_.

Funcionalidades
---------------

* **Cron parado**: alerta quando o cron não inicia há mais tempo que o limite (15 minutos por padrão). Como um cron parado não consegue avisar, as requisições web também verificam, no máximo uma vez por minuto.
* **Tarefas agendadas com falha**: alerta para tarefas agendadas habilitadas com fail delay.
* **Adhoc tasks**: alerta para tarefas falhando repetidamente, tarefas presas em execução e fila atrasada.
* **Alertas sem spam**: uma mensagem quando o alerta surge e outra quando é resolvido, nunca repetida enquanto continua ativo.
* **Entrega**: mensagens do Moodle (e-mail e notificações) para os administradores do site e usuários com ``tool/taskmonitor:receivealerts``, além de um hook para outros canais (:doc:`extending`).
* **Relatório**: *Administração do site > Relatórios > Task health*, com o estado do cron, alertas ativos, tarefas agendadas com falha, as tarefas agendadas mais lentas das últimas 24 horas (do log de tarefas do core) e a fila de adhoc tasks por tarefa.
* **Métricas**: com o ``tool_prometheus`` instalado, os alertas ativos são exportados como ``moodle_tool_taskmonitor_alerts_active``.

Requisitos
----------

* Moodle 5.3 ou posterior.
* Versão do PHP suportada pela sua versão do Moodle.
* PostgreSQL ou MariaDB.

Links
-----

* `Código-fonte <https://github.com/moodle-observability/moodle-tool_taskmonitor>`_
* `Issues <https://github.com/moodle-observability/moodle-tool_taskmonitor/issues>`_
* `Moodle Observability <https://moodle-observability.github.io/>`_

.. toctree::
   :hidden:
   :maxdepth: 1

   installation
   configuration
   usage
   extending
