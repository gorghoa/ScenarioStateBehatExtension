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

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\ScenarioTester;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface as Scenario;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Hook\Tester\Setup\HookedSetup;
use Behat\Testwork\Hook\Tester\Setup\HookedTeardown;
use Behat\Testwork\Tester\Result\TestResult;
use Gorghoa\ScenarioStateBehatExtension\Hook\Dispatcher\ScenarioStateHookDispatcher;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
final class ScenarioStateHookableScenarioTester implements ScenarioTester
{
    /**
     * @var ScenarioTester
     */
    private $baseTester;

    /**
     * @var ScenarioStateHookDispatcher
     */
    private $dispatcher;

    /**
     * @param ScenarioTester      $baseTester
     * @param ScenarioStateHookDispatcher $dispatcher
     */
    public function __construct(ScenarioTester $baseTester, ScenarioStateHookDispatcher $dispatcher)
    {
        $this->baseTester = $baseTester;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(Environment $env, FeatureNode $feature, Scenario $scenario, $skip)
    {
        $setup = $this->baseTester->setUp($env, $feature, $scenario, true);

        if ($skip) {
            return $setup;
        }

        return new HookedSetup($setup, $this->dispatcher->dispatchScopeHooks(new BeforeScenarioScope($env, $feature, $scenario)));
    }

    /**
     * {@inheritdoc}
     */
    public function test(Environment $env, FeatureNode $feature, Scenario $scenario, $skip)
    {
        return $this->baseTester->test($env, $feature, $scenario, $skip);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(Environment $env, FeatureNode $feature, Scenario $scenario, $skip, TestResult $result)
    {
        $teardown = $this->baseTester->tearDown($env, $feature, $scenario, true, $result);

        if ($skip) {
            return $teardown;
        }

        return new HookedTeardown($teardown, $this->dispatcher->dispatchScopeHooks(new AfterScenarioScope($env, $feature, $scenario, $result)));
    }
}
