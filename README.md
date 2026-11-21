<img src="docs/logo/logo.svg" alt="moodle-tool_taskmonitor logo" width="96" align="right" />

# moodle-tool_taskmonitor

[![Documentation](https://github.com/moodle-observability/moodle-tool_taskmonitor/actions/workflows/docs.yml/badge.svg)](https://github.com/moodle-observability/moodle-tool_taskmonitor/actions/workflows/docs.yml)

Cron and task health monitor for Moodle (`tool_taskmonitor`). Watches the Moodle cron and the scheduled and adhoc tasks, keeps a history of durations and failures, and alerts administrators when something stops or keeps failing.

**Status:** 0.1.0 (alpha). Download it from the [releases](https://github.com/moodle-observability/moodle-tool_taskmonitor/releases).

Documentation (English and Brazilian Portuguese): https://moodle-observability.github.io/moodle-tool_taskmonitor/

Requirements: Moodle 5.3 or later.

Quick start: install the plugin in `public/admin/tool/taskmonitor`, upgrade Moodle, check that Moodle can send
e-mail and open *Site administration > Reports > Task health*. Thresholds are in *Site administration > Plugins >
Admin tools > Task monitor*.

Part of [Moodle Observability](https://github.com/moodle-observability). Contributions: see the [contributing guide](https://github.com/moodle-observability/.github/blob/main/CONTRIBUTING.md).

License: [GPL-3.0](LICENSE).
