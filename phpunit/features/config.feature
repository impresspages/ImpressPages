Feature: Configuration
  In order to define website config
  As a developer
  I want to write it in ip_config.php

  Scenario: Setting website address
    Given I read default configuration
    And I set configuration "host" parameter to "localhost"
    When I load configuration
    Then website "homeAddress" should be "http://localhost/"