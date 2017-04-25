<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Context\Initializer;

use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext;
use Gorghoa\ScenarioStateBehatExtension\ScenarioState;
use Prophecy\Argument;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
class ScenarioStateInitializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ScenarioStateInitializer
     */
    private $initializer;

    protected function setUp()
    {
        $this->initializer = new ScenarioStateInitializer();
        $this->assertNotNull($this->initializer->getStore());
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals([
            ScenarioTested::AFTER => ['clearStore'],
        ], ScenarioStateInitializer::getSubscribedEvents());
    }

    public function testInitializeContext()
    {
        $contextMock = $this->prophesize(ScenarioStateAwareContext::class);
        $contextMock->setScenarioState(Argument::type(ScenarioState::class))->shouldBeCalledTimes(1);
        $this->initializer->initializeContext($contextMock->reveal());
    }
}
