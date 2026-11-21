moodle-tool_taskmonitor
=======================

**Monitor de cron e tarefas para o Moodle.**

Acompanha o cron do Moodle e as tarefas agendadas e adhoc, guarda o histórico de duração e falhas e avisa os administradores quando algo para ou falha repetidamente.

.. note::

   Em desenvolvimento, com foco no milestone ``v0.1.0 – MVP``. Ainda não há versão publicada.
   Acompanhe o andamento nas `issues <https://github.com/moodle-observability/moodle-tool_taskmonitor/issues>`_.

Funcionalidades previstas
-------------------------

* Detecta cron parado (limite configurável).
* Registra duração e falhas das tarefas agendadas, com retenção.
* Detecta adhoc tasks presas ou falhando repetidamente.
* Página de relatório com a visão geral da saúde das tarefas.
* Alertas pela API de mensagens do Moodle, sem repetição excessiva.

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
