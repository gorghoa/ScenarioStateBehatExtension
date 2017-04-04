<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Hook\Tester;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Tester\HookableScenarioTester as BaseHookableScenarioTester;
use Behat\Behat\Tester\ScenarioTester;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface as Scenario;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Hook\Tester\Setup\HookedSetup;
use Behat\Testwork\Tester\Result\TestResult;
use Gorghoa\ScenarioStateBehatExtension\Hook\Dispatcher\ScenarioStateHookDispatcher;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
final class HookableScenarioTester implements ScenarioTester
{
    /**
     * @var BaseHookableScenarioTester
     */
    private $decoratedService;

    /**
     * @var ScenarioStateHookDispatcher
     */
    private $dispatcher;

    /**
     * @var ScenarioTester
     */
    private $baseTester;

    /**
     * {@inheritdoc}
     */
    public function setUp(Environment $env, FeatureNode $feature, Scenario $scenario, $skip)
    {
        $setup = $this->baseTester->setUp($env, $feature, $scenario, $skip);

        if ($skip) {
            return $setup;
        }

        $scope = new BeforeScenarioScope($env, $feature, $scenario);
        $hookCallResults = $this->dispatcher->dispatchScopeHooks($scope);

        return new HookedSetup($setup, $hookCallResults);
    }

    /**
     * {@inheritdoc}
     */
    public function test(Environment $env, FeatureNode $feature, Scenario $scenario, $skip)
    {
        return $this->decoratedService->test($env, $feature, $scenario, $skip);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(Environment $env, FeatureNode $feature, Scenario $scenario, $skip, TestResult $result)
    {
        return $this->tearDown($env, $feature, $scenario, $skip, $result);
    }
}
