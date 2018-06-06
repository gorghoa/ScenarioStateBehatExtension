Feature: Scenario shared state
  In order to statefully test a suite of calls to a stateless system, I need a way to store the scenario state

  Scenario: Scenario state should be shared between steps
    When I run "behat --no-colors features/monkey.feature"
    Then it should pass with:
    """
    1 scenario (1 passed)
    """

  Scenario: Scenario state should be reset between scenarios
    When I run "behat --no-colors features/donkeys.feature"
    Then it should fail with:
    """
    Can not find a matching value for an argument
    """

  Scenario: Scenario Outline should work properly
    When I run "behat --no-colors features/bandar-log.feature"
    Then it should pass with:
    """
    2 scenarios (2 passed)
    """
