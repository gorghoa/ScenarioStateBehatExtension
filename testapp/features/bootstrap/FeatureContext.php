<?php

require_once __DIR__.'/../../Gorilla.php';

use Behat\Behat\Context\Context;
use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;

/**
 * Behat context class.
 */
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

    /**
     * @When the bonobo takes a banana
     */
    public function takeBanana()
    {
        $this->scenarioState->provideStateFragment('scenarioBanana', ['Yammy Banana']);
    }

    /**
     * @When gives this banana to gorilla
     */
    public function giveBananaToGorilla($scenarioBanana)
    {
        PHPUnit_Framework_Assert::assertEquals($scenarioBanana, ['Yammy Banana']);
        $gorilla = new Gorilla();
        $gorilla->setBanana($scenarioBanana);
        $this->scenarioState->provideStateFragment('scenarioGorilla', $gorilla);
    }

    /**
     * @Then the gorilla has the banana
     */
    public function gorillaHasBanana($scenarioBanana, $scenarioGorilla)
    {
        PHPUnit_Framework_Assert::assertEquals($scenarioBanana, ['Yammy Banana']);
        PHPUnit_Framework_Assert::assertEquals($scenarioGorilla->getBanana(), ['Yammy Banana']);
    }
}
