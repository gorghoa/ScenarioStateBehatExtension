Feature: Monkey gathering bananas

    Scenario: Monkey gives a banana to another monkey
        When the bonobo takes a banana
        And gives this banana to gorilla
        Then the gorilla has the banana
