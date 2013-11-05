Feature: Translation
  In order to make theme translatable
  As a developer
  I want to use __() function to denote strings that can be translated

  Scenario: Creating a theme that can be translatable
    Given current locale is "lt"
    And current theme is "Blank"
    And theme file "languages\lt_LT.php" contains
    """
    <?php return array(
      'title' => 'Pavadinimas'
    );
    """
    When I do nothing
    Then __("title", "myTheme") should return "Pavadinimas"

  Scenario: ImpressPages messages
    Given I want to translate ImpressPages
    When
    Then messages should be translated using xliff

