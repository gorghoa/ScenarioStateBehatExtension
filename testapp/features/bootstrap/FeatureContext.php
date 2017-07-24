<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
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
     * @BeforeSuite
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
     * @BeforeScenario
     */
    public function initBananas()
    {
        $this->scenarioState->provideStateFragment('bananas', ['foo', 'bar']);
        $this->scenarioState->provideStateFragment('gorillaIsMale', true);
    }

    /**
     * @BeforeScenario
     *
     * @ScenarioStateArgument("bananas")
     *
     * @param array $bananas
     */
    public function saveBananasWithoutScopeBeforeScenario(array $bananas)
    {
        \PHPUnit_Framework_Assert::assertEquals(['foo', 'bar'], $bananas);
    }

    /**
     * @BeforeScenario
     *
     * @ScenarioStateArgument("bananas")
     *
     * @param BeforeScenarioScope $scope
     * @param array               $bananas
     */
    public function saveBananasWithScopeBeforeScenario(BeforeScenarioScope $scope, array $bananas)
    {
        \PHPUnit_Framework_Assert::assertNotNull($scope);
        \PHPUnit_Framework_Assert::assertEquals(['foo', 'bar'], $bananas);
    }

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function initApplesBeforeScenario(BeforeScenarioScope $scope)
    {
        \PHPUnit_Framework_Assert::assertNotNull($scope);
    }

    /**
     * @AfterScenario
     *
     * @ScenarioStateArgument("bananas")
     *
     * @param array $bananas
     */
    public function saveBananasWithoutScopeAfterScenario(array $bananas)
    {
        \PHPUnit_Framework_Assert::assertEquals(['foo', 'bar'], $bananas);
    }

    /**
     * @AfterScenario
     *
     * @ScenarioStateArgument("bananas")
     *
     * @param array              $bananas
     * @param AfterScenarioScope $scope
     */
    public function saveBananasWithScopeAfterScenario(array $bananas, AfterScenarioScope $scope)
    {
        \PHPUnit_Framework_Assert::assertNotNull($scope);
        \PHPUnit_Framework_Assert::assertEquals(['foo', 'bar'], $bananas);
    }

    /**
     * @AfterScenario
     *
     * @param AfterScenarioScope $scope
     */
    public function initApplesAfterScenario(AfterScenarioScope $scope)
    {
        \PHPUnit_Framework_Assert::assertNotNull($scope);
    }

    /**
     * @When the bonobo takes a banana
     */
    public function takeBanana()
    {
        $this->scenarioState->provideStateFragment('scenarioBanana', 'Yammy Banana');
    }

    /**
     * @Transform :gorilla
     *
     * @ScenarioStateArgument(name="gorillaIsMale", argument="isMale")
     *
     * @param string $gorilla
     * @param bool   $isMale
     *
     * @return Gorilla
     */
    public function transformGorilla($gorilla, $isMale)
    {
        $monkey = new Gorilla();
        $monkey->setName($gorilla);
        $monkey->setMale($isMale);

        return $monkey;
    }

    /**
     * @When gives this banana to :gorilla
     *
     * @ScenarioStateArgument("scenarioBanana")
     *
     * @param string  $scenarioBanana
     * @param Gorilla $gorilla
     */
    public function giveBananaToGorilla($scenarioBanana, Gorilla $gorilla)
    {
        \PHPUnit_Framework_Assert::assertEquals('Yammy Banana', $scenarioBanana);
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
        \PHPUnit_Framework_Assert::assertEquals('Yammy Banana', $scenarioBanana);
        \PHPUnit_Framework_Assert::assertEquals('Yammy Banana', $gorilla->getBanana());
    }

    /**
     * @Then the gorilla is named :name
     *
     * @ScenarioStateArgument(name="scenarioGorilla", argument="gorilla")
     *
     * @param string  $name
     * @param Gorilla $gorilla
     */
    public function gorillaIsCorrectlyNamed($name, Gorilla $gorilla)
    {
        \PHPUnit_Framework_Assert::assertEquals($name, $gorilla->getName());
    }
}
