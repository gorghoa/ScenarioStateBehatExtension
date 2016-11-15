<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateArgument;
use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;
use Gorghoa\ScenarioStateBehatExtension\TestApp\Gorilla;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class FeatureContext implements ScenarioStateAwareContext
{
    /**
     * @beforeSuite
     */
    public static function setUpSuite()
    {
        require_once __DIR__.'/../../autoload.php';
    }

    /**
     * @var ScenarioStateInterface
     */
    private $scenarioState;

    /**
     * @param ScenarioStateInterface $scenarioState
     */
    public function setScenarioState(ScenarioStateInterface $scenarioState)
    {
        $this->scenarioState = $scenarioState;
    }

    /**
     * @When the bonobo takes a banana
     */
    public function takeBanana()
    {
        $this->scenarioState->provideStateFragment('scenarioBanana', 'Yammy Banana');
    }

    /**
     * @When gives this banana to gorilla
     *
     * @ScenarioStateArgument("scenarioBanana")
     *
     * @param string $scenarioBanana
     */
    public function giveBananaToGorilla($scenarioBanana)
    {
        \PHPUnit_Framework_Assert::assertEquals($scenarioBanana, 'Yammy Banana');
        $gorilla = new Gorilla();
        $gorilla->setBanana($scenarioBanana);
        $this->scenarioState->provideStateFragment('scenarioGorilla', $gorilla);
    }

    /**
     * @Then the gorilla has the banana
     *
     * @ScenarioStateArgument("scenarioBanana")
     * @ScenarioStateArgument(name="scenarioGorilla", argument="gorilla")
     *
     * @param string  $scenarioBanana
     * @param Gorilla $gorilla
     */
    public function gorillaHasBanana($scenarioBanana, Gorilla $gorilla)
    {
        \PHPUnit_Framework_Assert::assertEquals($scenarioBanana, 'Yammy Banana');
        \PHPUnit_Framework_Assert::assertEquals($gorilla->getBanana(), 'Yammy Banana');
    }
}
