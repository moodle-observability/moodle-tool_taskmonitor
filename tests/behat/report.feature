@tool @tool_taskmonitor
Feature: Task health report
  In order to know whether cron and the tasks are healthy
  As an administrator
  I need to see the task health report

  Scenario: The administrator opens the task health report
    Given I log in as "admin"
    When I navigate to "Reports > Task health" in site administration
    Then I should see "Task health"
    And I should see "Active alerts"
    And I should see "Adhoc task queue"
