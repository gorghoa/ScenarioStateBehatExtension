Feature: Scenario shared state
  In order to statefully test a
  suite of calls to a stateless
  system, I need a way to store the
  scenario state

  Scenario: Scenario state should be shared between steps
    When I run "behat --no-colors features/monkey.feature"
    Then it should pass with:
        """
        1 scenario (1 passed)
        """

  Scenario: Scenario state should be reseted between scenarios
    When I run "behat --no-colors features/donkeys.feature"
    Then it should fail with:
        """
        [Behat\Testwork\Argument\Exception\UnknownParameterValueException]
        """
