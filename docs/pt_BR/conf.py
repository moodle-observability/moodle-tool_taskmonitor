import moodle_docs_theme

project = "moodle-tool_taskmonitor"
copyright = "2026, Contribuidores da Moodle Observability"
author = "Kelson da Costa Medeiros"
release = "0.1.0-dev"

extensions = [
    "sphinx.ext.githubpages",
    "moodle_docs_theme",
]

templates_path = []
exclude_patterns = ["_build", "Thumbs.db", ".DS_Store"]

language = "pt_BR"

html_theme = "moodle_docs_theme"
html_theme_path = [moodle_docs_theme.get_html_theme_path()]

html_theme_options = {
    "primary_color": "#ea580c",
    "secondary_color": "#0f172a",
    "project_name": "moodle-tool_taskmonitor",
    "tagline": "Monitor de cron e tarefas para o Moodle",
    "github_url": "https://github.com/moodle-observability/moodle-tool_taskmonitor",
    "github_repo": "moodle-observability/moodle-tool_taskmonitor",
    "github_version": "main",
    "doc_path": "docs/pt_BR/",
    "show_edit_on_github": True,
    "enable_dark_mode": True,
    "navigation_links": "Início|index, Instalação|installation, Configuração|configuration, Uso|usage",
}

html_static_path = []

# Logo e favicon do plugin (fonte em docs/logo/; logo quadrada, sem cantos arredondados).
html_logo = "../logo/logo.svg"
html_favicon = "../logo/favicon.ico"
