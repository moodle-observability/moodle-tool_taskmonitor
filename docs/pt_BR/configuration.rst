Configuração
============

*Administração do site > Plugins > Ferramentas de administração > Task monitor*:

.. list-table::
   :header-rows: 1

   * - Configuração
     - Padrão
     - Alerta quando
   * - Cron threshold
     - 15 minutos
     - o cron não inicia há mais tempo que isso.
   * - Adhoc queue delay threshold
     - 1 hora
     - a adhoc task vencida mais antiga espera há mais tempo que isso.
   * - Stuck adhoc task threshold
     - 1 hora
     - uma adhoc task está marcada como em execução há mais tempo que isso.
   * - Failing adhoc task threshold
     - 10 minutos
     - uma adhoc task tem fail delay de pelo menos esse valor (o Moodle dobra o valor a cada falha, então 10 minutos são cerca de quatro falhas seguidas).

Destinatários
-------------

Os administradores do site sempre recebem os alertas. Para incluir outras pessoas, dê a elas a capability ``tool/taskmonitor:receivealerts`` no contexto do sistema (por exemplo, por um papel de sistema). Cada destinatário escolhe os canais (e-mail, notificações web) em *Preferências > Preferências de notificação > Task monitor alerts*.

O relatório exige ``tool/taskmonitor:view`` (gerentes têm por padrão).

Tarefa agendada
---------------

*Evaluate cron and task health* roda a cada minuto. Mantenha-a habilitada: ela levanta e resolve os alertas de tarefas. Os alertas de cron também são avaliados nas requisições web.
