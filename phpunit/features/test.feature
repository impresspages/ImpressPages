Feature: Test environment
  In order to test features under different environments
  As a developer
  I want to load different application environments into test cases

  Scenario: Loading default test environment
    Given I am in a test
    When I load test environment
    Then Default test constants should be set