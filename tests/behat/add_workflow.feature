@tool @tool_lifecycle
Feature: Add a workflow definition

  Scenario: Add a new workflow definition without steps
  For displaying the additional trigger settings the "Save changes" button is used.
    Given I log in as "admin"
    And I navigate to "Workflow Settings" node in "Site administration > Life Cycle"
    And I press "Add Workflow"
    And I set the following fields to these values:
      | Title                      | My Workflow                               |
      | Displayed workflow title   | Teachers view on workflow                 |
    When I press "Save changes"
    Then I should see "Trigger for workflow 'My Workflow'"
    When I set the following fields to these values:
      | Instance Name              | My Trigger                                |
      | Subplugin Name             | Manual trigger                            |
    And I press "Save changes"
    # The manual trigger requires additional settings. For that reason the form reloads with some more fields.
    Then I should see "Required"
    And I should see "Specific settings of the trigger type"
    When I set the following fields to these values:
      | Icon                       | t/delete                                  |
      | Action name                | Delete course                             |
      | Capability                 | moodle/course:manageactivities            |
    And I press "Save changes"
    Then I should see "Workflow Steps"
    And I should see "Manual trigger"
    When I press "Back"
    Then I should see "My Workflow"

  Scenario: Add a new workflow definition with steps
  For displaying the additional trigger settings the "reload" button is used.
    Given I log in as "admin"
    And I navigate to "Workflow Settings" node in "Site administration > Life Cycle"
    And I press "Add Workflow"
    And I set the following fields to these values:
      | Title                      | My Workflow                               |
      | Displayed workflow title   | Teachers view on workflow                 |
    When I press "Save changes"
    Then I should see "Trigger for workflow 'My Workflow'"
    When I set the following fields to these values:
      | Instance Name              | My Trigger                                |
      | Subplugin Name             | Start date delay trigger                  |
    And I press "reload"
    Then I should see "Specific settings of the trigger type"
    When I set the following fields to these values:
      | delay[number]    | 2                          |
      | delay[timeunit]  | days                       |
    And I press "Save changes"
    Then I should see "Workflow Steps"
    And I should see "Start date delay trigger"
    When I select "Delete Course Step" from the "subpluginname" singleselect
    And I set the field "Instance Name" to "Delete Course"
    And I press "Save changes"
    Then I should see "Workflow Steps"
    And I should see "Delete Course"

  Scenario: Add a new workflow definition and alter trigger
    Given I log in as "admin"
    And I navigate to "Workflow Settings" node in "Site administration > Life Cycle"
    And I press "Add Workflow"
    And I set the following fields to these values:
      | Title                      | My Workflow                               |
      | Displayed workflow title   | Teachers view on workflow                 |
    When I press "Save changes"
    Then I should see "Trigger for workflow 'My Workflow'"
    When I set the following fields to these values:
      | Instance Name              | My Trigger                                |
      | Subplugin Name             | Start date delay trigger                  |
    And I press "reload"
    Then I should see "Specific settings of the trigger type"
    When I set the following fields to these values:
      | delay[number]    | 2                          |
      | delay[timeunit]  | days                       |
    And I press "Save changes"
    Then I should see "Workflow Steps"
    And I should see "Start date delay trigger"
    When I click on the tool "Edit" in the "Trigger" row of the "tool_lifecycle_workflows" table
    Then the following fields match these values:
      | Instance Name              | My Trigger                                |
      | Subplugin Name             | Start date delay trigger                  |
      | delay[number]              | 2                                         |
      | delay[timeunit]            | days                                      |
    When I set the following fields to these values:
      | Subplugin Name             | Manual trigger                            |
    And I press "reload"
    And I set the following fields to these values:
      | Instance Name              | My updated Trigger                        |
      | Icon                       | t/delete                                  |
      | Action name                | Delete course                             |
      | Capability                 | moodle/course:manageactivities            |
    And I press "Save changes"
    Then I should see "Manual trigger"
    When I click on the tool "Edit" in the "Trigger" row of the "tool_lifecycle_workflows" table
    Then the following fields match these values:
      | Instance Name              | My updated Trigger                        |
      | Subplugin Name             | Manual trigger                            |
      | Icon                       | t/delete                                  |
      | Action name                | Delete course                             |
      | Capability                 | moodle/course:manageactivities            |

  Scenario: Add a new workflow definition with steps and rearange
    Given I log in as "admin"
    And I navigate to "Workflow Settings" node in "Site administration > Life Cycle"
    And I press "Add Workflow"
    And I set the following fields to these values:
      | Title                      | My Workflow                               |
      | Displayed workflow title   | Teachers view on workflow                 |
    When I press "Save changes"
    Then I should see "Trigger for workflow 'My Workflow'"
    When I set the following fields to these values:
      | Instance Name              | My Trigger                                |
      | Subplugin Name             | Start date delay trigger                  |
    And I press "reload"
    Then I should see "Specific settings of the trigger type"
    When I set the following fields to these values:
      | delay[number]    | 2                          |
      | delay[timeunit]  | days                       |
    And I press "Save changes"
    Then I should see "Workflow Steps"
    And I should see "Start date delay trigger"
    When I select "Delete Course Step" from the "subpluginname" singleselect
    And I set the field "Instance Name" to "Delete Course 1"
    And I press "Save changes"
    And I select "Delete Course Step" from the "subpluginname" singleselect
    And I set the field "Instance Name" to "Delete Course 2"
    And I press "Save changes"
    And I select "Create Backup Step" from the "subpluginname" singleselect
    And I set the field "Instance Name" to "Create Backup Step"
    And I press "Save changes"
    Then the step "Delete Course 1" should be at the 1 position
    And the step "Delete Course 2" should be at the 2 position
    And the step "Create Backup Step" should be at the 3 position
    And I click on the tool "Down" in the "Delete Course 1" row of the "tool_lifecycle_workflows" table
    Then the step "Delete Course 1" should be at the 2 position
    And the step "Delete Course 2" should be at the 1 position
    And the step "Create Backup Step" should be at the 3 position
    And I click on the tool "Up" in the "Create Backup Step" row of the "tool_lifecycle_workflows" table
    Then the step "Delete Course 1" should be at the 3 position
    And the step "Delete Course 2" should be at the 1 position
    And the step "Create Backup Step" should be at the 2 position
