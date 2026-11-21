# Changelog

## 0.1.0 - 2026-11-21

First release (alpha).

- Alerts for a stopped cron (also checked on web requests), failing scheduled tasks, adhoc tasks failing
  repeatedly or stuck, and a delayed adhoc queue, with configurable thresholds.
- One message when an alert is raised and one when it is resolved (Moodle messaging), to site administrators and
  users with `tool/taskmonitor:receivealerts`.
- Task health report (cron, active alerts, failing scheduled tasks, slowest scheduled tasks from the core task log,
  adhoc queue).
- `alert_raised` hook for other delivery channels; `moodle_tool_taskmonitor_alerts_active` metric for
  tool_prometheus.
