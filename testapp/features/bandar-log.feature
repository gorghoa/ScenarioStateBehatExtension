Feature: Monkey gathering bananas

  Scenario Outline: Monkey gives a banana to another monkey
    When the bonobo takes a banana
    And gives this banana to <gorilla>
    Then the gorilla is named <gorilla>

    Examples:
      | gorilla |
      | Harambe |
      | Max     |
