Feature: Event
  In order to extend/modify core ImpressPages functionality
  As a developer
  I want to use events for that

  Scenario: Loading default test environment
    Given I am in a test
    When I load my test
    Then Default test constants should be set
