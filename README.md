# ScenarioStateBehatExtension

[![Build Status](https://travis-ci.org/gorghoa/ScenarioStateBehatExtension.svg?branch=master)](https://travis-ci.org/gorghoa/ScenarioStateBehatExtension)

## When to use

Behat scenarios are all about state. First you put the system under test
to a special state through the `Given` steps. Then you continue to manipulate
your system through `When` steps and finally testing the resulting state via
the `Then` steps.

When testing a system like a single page app or a statefull website, the resulting state of our steps is handled by the system itself (either by the browser, or by the php session, etc.).

But, when you are testing a stateless system, chiefly an API, then the resulting state of our steps is handled by no one. This is the case for this extension.

## Installation


```bash
composer require gorghoa/scenariostate-behat-extension @RC
```

Then update your project’s `behat.yml` config file by loading the extension:

```yaml
default:
    extensions:
        Gorghoa\ScenarioStateBehatExtension\ServiceContainer\ScenarioStateExtension: ~
```

## Usage

This behat extension will allow scenarios steps to provide and consume what I call “fragments” of the resulting state.

Each scenario get it’s own isolated and unique state.

Let’s say a feature like this:

```gherkin

    Feature: Monkey gathering bananas

        Scenario: Monkey gives a banana to another monkey
            When bonobo takes a banana
            And bonobo gives this banana to "gorilla"

```

See the “**this** banana”? What we want during the second step execution is a reference to the exact banana the bonobo initially took. This behat extension will help us to propagate the banana refence amongst steps.


### Provide state fragment

To share a piece of state with all other scenario’s steps, your contexts need to implement the `Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext` interface.

This interface declare one method to implement: `public function setScenarioState(ScenarioStateInterface $scenarioState)`. This ScenarioState is responsible for storing your state.

```php

    use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext;
    use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;

    class FeatureContext implements ScenarioStateAwareContext
    {
        /**
         * @var Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface
         */
        private $scenarioState;

        public function setScenarioState(ScenarioStateInterface $scenarioState)
        {
            $this->scenarioState = $scenarioState;
        }

    }
```


Then you can publish state fragment through the `ScenarioStateInterface::provideStateFragment(string $key, mixed $value)` method.


```php

    /**
     * @When bonobo takes a banana
     */
    public function takeBanana()
    {

        $banana = 'Yammy Banana';
        $bonobo = new Bonobo('Gerard');

        // Here, the banana `Yammy Banana` is shared amongst steps through the key “scenarioBanana”
        $this->scenarioState->provideStateFragment('scenarioBanana', $banana);

        // Here, the bonobo Gerard is shared amongst steps through the key “scenarioBonobo”
        $this->scenarioState->provideStateFragment('scenarioBonobo', $bonobo);
    }

```

### Consuming state fragments

The easiest way to consume state fragments provided to the scenario’s state, is to add to step’s methods the needed arguments whose names are matching keys of provided state fragments:

```php

    /**
     * @Param string $monkey
     * @Param string $scenarioBanana
     * @Param Bonobo $scenarioBonobo
     *
     * @When bonobo gives this banana to :monkey
     */
    public function giveBananaToGorilla($monkey, $scenarioBanana, Bonobo $scenarioBonobo)
     {
        // (note that PHPUnit is here only given as an example, feel free to use any asserter you want)
        PHPUnit_Framework_Assert::assertEquals($monkey, 'gorilla');
        PHPUnit_Framework_Assert::assertEquals($scenarioBanana, 'Yammy Banana');
        PHPUnit_Framework_Assert::assertEquals($scenarioBonobo->getName(), 'Gerard');
     }

```

> *Note*: The argument name of the function **must match** the key used when using `provideStateFragment`.
> If no corresponding key in the scenario state is found, a `Behat\Testwork\Argument\Exception\UnknownParameterValueException` is thrown.

## Why injecting state’s fragment through method params

  1. Clear dependencies declaration for the step method
  2. Runtime checks by php: fail quickly if the argument is not present
  3. The less verbose way of consuming shared scenario state
