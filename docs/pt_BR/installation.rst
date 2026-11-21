Instalação
==========

1. Baixe ``tool_taskmonitor-<versão>.zip`` em `releases <https://github.com/moodle-observability/moodle-tool_taskmonitor/releases>`_, ou clone o repositório em ``public/admin/tool/taskmonitor``:

   .. code-block:: bash

      git clone https://github.com/moodle-observability/moodle-tool_taskmonitor.git public/admin/tool/taskmonitor

2. Rode ``php admin/cli/upgrade.php`` (ou abra *Administração do site > Notificações*).
3. Confira se o Moodle consegue enviar e-mail: os alertas usam o sistema de mensagens do Moodle.

Desinstalação: *Administração do site > Plugins > Visão geral dos plugins*, ``tool_taskmonitor``, *Desinstalar*.
