Feature: Bootstrap
  In order to use application
  As a developer
  I want to load it through bootstrap

  Scenario: Loading application from test
    Given I set up default test environment
    When I load bootstrap
    Then I can get configuration information

  Scenario: Loading application from request object
    Given I set up default test environment
    And create get '/' request object
    When I load bootstrap
    Then I can get configuration information

