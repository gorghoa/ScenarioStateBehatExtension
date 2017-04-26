# ScenarioStateBehatExtension

[![Build Status](https://travis-ci.org/gorghoa/ScenarioStateBehatExtension.svg?branch=master)](https://travis-ci.org/gorghoa/ScenarioStateBehatExtension)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gorghoa/ScenarioStateBehatExtension/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gorghoa/ScenarioStateBehatExtension/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/gorghoa/ScenarioStateBehatExtension/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/gorghoa/ScenarioStateBehatExtension/?branch=master)

## When to use

Behat scenarios are all about state. First you put the system under test
to a special state through the `Given` steps. Then you continue to manipulate
your system through `When` steps and finally testing the resulting state via
the `Then` steps.

When testing a system like a single page app or a stateful website, the resulting state of our steps is handled by the
system itself (either by the browser, or by the php session, etc.).

But, when you are testing a stateless system, chiefly an API, then the resulting state of our steps is handled by no
one. This is the case for this extension.

## Installation


```bash
composer require --dev gorghoa/scenariostate-behat-extension @RC
```

Then update your project's `behat.yml` config file by loading the extension:

```yaml
default:
    extensions:
        Gorghoa\ScenarioStateBehatExtension\ServiceContainer\ScenarioStateExtension: ~
```

## Usage

This behat extension will allow scenarios steps to provide and consume what I call "fragments" of the resulting state.

Each scenario get it's own isolated and unique state.

Let's say a feature like this:

```gherkin

    Feature: Monkey gathering bananas

        Scenario: Monkey gives a banana to another monkey
            When bonobo takes a banana
            And bonobo gives this banana to "gorilla"
```

See the "**this** banana"? What we want during the second step execution is a reference to the exact banana the bonobo
initially took. This behat extension will help us to propagate the banana refence amongst steps.


### Provide state fragment

To share a piece of state with all other scenario's steps, your contexts need to implement the
`Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext` interface.

This interface declares one method to implement: `public function setScenarioState(ScenarioStateInterface $scenarioState)`
which can be imported using `ScenarioStateAwareTrait`. This ScenarioState is responsible for storing your state.

```php
use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext;
use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareTrait;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;

class FeatureContext implements ScenarioStateAwareContext
{
    use ScenarioStateAwareTrait;
}
```

Then you can publish state fragment through the `ScenarioStateInterface::provideStateFragment(string $key, mixed $value)`
method.

```php
/**
 * @When bonobo takes a banana
 */
public function takeBanana()
{
    $banana = 'Yammy Banana';
    $bonobo = new Bonobo('Gerard');

    // Here, the banana `Yammy Banana` is shared amongst steps through the key "scenarioBanana"
    $this->scenarioState->provideStateFragment('scenarioBanana', $banana);

    // Here, the bonobo Gerard is shared amongst steps through the key "scenarioBonobo"
    $this->scenarioState->provideStateFragment('scenarioBonobo', $bonobo);
}
```

### Consuming state fragments

To consume state fragments provided to the scenario's state, you must add needed arguments to step's methods using
`ScenarioStateArgument` annotation. It can be used easily:

- inject argument from store with the exact same name: `@ScenarioStateArgument("scenarioBanana")` or `@ScenarioStateArgument(name="scenarioBanana")`
- inject argument from store changing its name: `@ScenarioStateArgument(name="scenarioBanana", argument="banana")`

```php
use Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateArgument;

/**
 * @When bonobo gives this banana to :monkey
 *
 * @ScenarioStateArgument("scenarioBanana")
 * @ScenarioStateArgument(name="scenarioBonobo", argument="bonobo")
 *
 * @param string $monkey
 * @param string $scenarioBanana
 * @param Bonobo $bonobo
 */
public function giveBananaToGorilla($monkey, $scenarioBanana, Bonobo $bonobo)
{
    // (note that PHPUnit is here only given as an example, feel free to use any asserter you want)
    \PHPUnit_Framework_Assert::assertEquals($monkey, 'gorilla');
    \PHPUnit_Framework_Assert::assertEquals($scenarioBanana, 'Yammy Banana');
    \PHPUnit_Framework_Assert::assertEquals($bonobo->getName(), 'Gerard');
}
```

### Using state fragments in Behat hook methods

It's also possible to consume state fragments in hook methods: `BeforeScenario` & `AfterScenario`. And much better,
the order is not important, you can set your arguments in any order you want:

```php
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateArgument;

/**
 * @BeforeScenario
 *
 * @ScenarioStateArgument("scenarioBanana")
 *
 * @param string              $scenarioBanana
 * @param BeforeScenarioScope $scope
 */
public function checkBananaBeforeScenario($scenarioBanana, BeforeScenarioScope $scope)
{
    // (note that PHPUnit is here only given as an example, feel free to use any asserter you want)
    \PHPUnit_Framework_Assert::assertEquals($scenarioBanana, 'Yammy Banana');
    \PHPUnit_Framework_Assert::assertNotNull($scope);
}

/**
 * @AfterScenario
 *
 * @ScenarioStateArgument("scenarioBanana")
 *
 * @param string             $scenarioBanana
 * @param AfterScenarioScope $scope
 */
public function checkBananaAfterScenario($scenarioBanana, AfterScenarioScope $scope)
{
    // (note that PHPUnit is here only given as an example, feel free to use any asserter you want)
    \PHPUnit_Framework_Assert::assertEquals($scenarioBanana, 'Yammy Banana');
    \PHPUnit_Framework_Assert::assertNotNull($scope);
}
```

## Why injecting state's fragments through method params

  1. Clear dependencies declaration for the step method
  2. Runtime checks by php: fail quickly if the argument is not present or does not match type hint
  3. The less verbose way of consuming shared scenario state
