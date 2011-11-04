Feature: Add a project
  As an authenticated user
  I should be able to create a project

  Scenario: add a project
    Given I am on "/index_test.php"
    When I follow "New project"
    And I fill in "Name" with "Dominate world"
    And I select "random" from "Category"
    And I fill in "Description" with "Do you see any Teletubbies in here? Do you see a slender plastic tag clipped to my shirt with my name printed on it? Do you see a little Asian child with a blank expression on his face sitting outside on a mechanical helicopter that shakes when you put quarters in it? No? Well, that's what you see at a toy store. And you must think you're in a toy store, because you're here shopping for an infant named Jeb."
    And I press "Create project"
    Then I should see "Dominate world"
