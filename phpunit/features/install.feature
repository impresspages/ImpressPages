Feature: Installation
  In order to install website
  As a developer
  I want to execute it through ImpressPages architecture

  Scenario: Load application
    Given ImpressPages core is loaded
    And Ip\Config loads install/ip_config-template.php
    When ...
    Then I should see first installation page