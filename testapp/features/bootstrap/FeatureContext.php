<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../../Gorilla.php';

use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class FeatureContext implements ScenarioStateAwareContext
{
    /**
     * @var ScenarioStateInterface
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
     *
     * @param string $scenarioBanana
     */
    public function giveBananaToGorilla($scenarioBanana)
    {
        \PHPUnit_Framework_Assert::assertEquals($scenarioBanana, ['Yammy Banana']);
        $gorilla = new Gorilla();
        $gorilla->setBanana($scenarioBanana);
        $this->scenarioState->provideStateFragment('scenarioGorilla', $gorilla);
    }

    /**
     * @Then the gorilla has the banana
     *
     * @param string  $scenarioBanana
     * @param Gorilla $scenarioGorilla
     */
    public function gorillaHasBanana($scenarioBanana, Gorilla $scenarioGorilla)
    {
        \PHPUnit_Framework_Assert::assertEquals($scenarioBanana, ['Yammy Banana']);
        \PHPUnit_Framework_Assert::assertEquals($scenarioGorilla->getBanana(), ['Yammy Banana']);
    }
}
