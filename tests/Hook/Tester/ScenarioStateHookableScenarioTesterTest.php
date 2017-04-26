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

use Behat\Behat\Tester\ScenarioTester;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Setup\Setup;
use Behat\Testwork\Tester\Setup\Teardown;
use Gorghoa\ScenarioStateBehatExtension\Hook\Dispatcher\ScenarioStateHookDispatcher;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
class ScenarioStateHookableScenarioTesterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ScenarioStateHookableScenarioTester
     */
    private $tester;

    /**
     * @var ScenarioTester|ObjectProphecy
     */
    private $baseTesterMock;

    /**
     * @var ScenarioStateHookDispatcher|ObjectProphecy
     */
    private $dispatcherMock;

    /**
     * @var Environment|ObjectProphecy
     */
    private $environmentMock;

    /**
     * @var FeatureNode|ObjectProphecy
     */
    private $featureNodeMock;

    /**
     * @var ScenarioInterface|ObjectProphecy
     */
    private $scenarioMock;

    protected function setUp()
    {
        $this->baseTesterMock = $this->prophesize(ScenarioTester::class);
        $this->dispatcherMock = $this->prophesize(ScenarioStateHookDispatcher::class);
        $this->environmentMock = $this->prophesize(Environment::class);
        $this->featureNodeMock = $this->prophesize(FeatureNode::class);
        $this->scenarioMock = $this->prophesize(ScenarioInterface::class);

        $this->tester = new ScenarioStateHookableScenarioTester($this->baseTesterMock->reveal(), $this->dispatcherMock->reveal());
    }

    public function testSetUpSkip()
    {
        $setupMock = $this->prophesize(Setup::class);
        $this->baseTesterMock->setUp($this->environmentMock, $this->featureNodeMock, $this->scenarioMock, true)
            ->willReturn($setupMock)
            ->shouldBeCalledTimes(1);
        $this->assertEquals($setupMock->reveal(), $this->tester->setUp($this->environmentMock->reveal(), $this->featureNodeMock->reveal(), $this->scenarioMock->reveal(), true));
    }

    public function testTearDownSkip()
    {
        $tearDownMock = $this->prophesize(Teardown::class);
        $testResultMock = $this->prophesize(TestResult::class);
        $this->baseTesterMock->tearDown($this->environmentMock, $this->featureNodeMock, $this->scenarioMock, true, $testResultMock)
            ->willReturn($tearDownMock)
            ->shouldBeCalledTimes(1);
        $this->assertEquals($tearDownMock->reveal(), $this->tester->tearDown($this->environmentMock->reveal(), $this->featureNodeMock->reveal(), $this->scenarioMock->reveal(), true, $testResultMock->reveal()));
    }

    public function testTest()
    {
        $testResultMock = $this->prophesize(TestResult::class);
        $this->baseTesterMock->test($this->environmentMock, $this->featureNodeMock, $this->scenarioMock, false)
            ->willReturn($testResultMock)
            ->shouldBeCalledTimes(1);
        $this->assertEquals($testResultMock->reveal(), $this->tester->test($this->environmentMock->reveal(), $this->featureNodeMock->reveal(), $this->scenarioMock->reveal(), false));
    }
}
