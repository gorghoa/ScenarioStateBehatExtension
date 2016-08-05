<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension;

use Behat\Testwork\Argument\ArgumentOrganiser;
use Doctrine\Common\Annotations\Reader;
use Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateArgument;
use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ScenarioStateArgumentOrganiserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ScenarioStateArgumentOrganiser
     */
    private $organiser;

    /**
     * @var ObjectProphecy|ArgumentOrganiser
     */
    private $organiserMock;

    /**
     * @var ObjectProphecy|ScenarioStateInitializer
     */
    private $initializerMock;

    /**
     * @var ObjectProphecy|ScenarioStateInterface
     */
    private $storeMock;

    /**
     * @var ObjectProphecy|\ReflectionMethod
     */
    private $functionMock;

    /**
     * @var ObjectProphecy|Reader
     */
    private $readerMock;

    /**
     * @var ObjectProphecy|ScenarioStateArgument
     */
    private $annotationMock;

    protected function setUp()
    {
        $this->organiserMock = $this->prophesize(ArgumentOrganiser::class);
        $this->initializerMock = $this->prophesize(ScenarioStateInitializer::class);
        $this->storeMock = $this->prophesize(ScenarioStateInterface::class);
        $this->functionMock = $this->prophesize(\ReflectionMethod::class);
        $this->readerMock = $this->prophesize(Reader::class);
        $this->annotationMock = $this->prophesize(ScenarioStateArgument::class);

        $this->organiser = new ScenarioStateArgumentOrganiser(
            $this->organiserMock->reveal(),
            $this->initializerMock->reveal(),
            $this->readerMock->reveal()
        );
    }

    public function testOrganiseArguments()
    {
        $this->functionMock->getParameters()->willReturn([
            (object) ['name' => 'scenarioBanana'],
            (object) ['name' => 'gorilla'],
            (object) ['name' => 'foo'],
        ])->shouldBeCalledTimes(1);

        $this->initializerMock->getStore()->willReturn($this->storeMock->reveal())->shouldBeCalledTimes(1);
        $this->readerMock->getMethodAnnotations($this->functionMock->reveal())->willReturn([
            $this->annotationMock->reveal(),
            $this->annotationMock->reveal(),
        ])->shouldBeCalledTimes(1);
        $this->annotationMock->getArgument()
            ->willReturn('scenarioBanana', 'scenarioBanana', 'gorilla', 'gorilla')
            ->shouldBeCalled();
        $this->annotationMock->getName()
            ->willReturn('scenarioBanana', 'scenarioBanana', 'scenarioBanana', 'scenarioGorilla', 'scenarioGorilla', 'scenarioGorilla')
            ->shouldBeCalled();
        $this->storeMock->hasStateFragment('scenarioBanana')->willReturn(true)->shouldBeCalledTimes(1);
        $this->storeMock->hasStateFragment('scenarioGorilla')->willReturn(true)->shouldBeCalledTimes(1);
        $this->storeMock->hasStateFragment('foo')->shouldNotBeCalled();
        $this->storeMock->getStateFragment('scenarioBanana')->willReturn('Yummy banana!')->shouldBeCalledTimes(2);
        $this->storeMock->getStateFragment('scenarioGorilla')->willReturn('Bonobo')->shouldBeCalledTimes(2);
        $this->storeMock->getStateFragment('foo')->shouldNotBeCalled();

        $this->organiserMock->organiseArguments($this->functionMock->reveal(), [
            0 => 'scenarioBanana',
            1 => 'gorilla',
            'scenarioBanana' => 'Yummy banana!',
            2 => 'Yummy banana!',
            'gorilla' => 'Bonobo',
            3 => 'Bonobo',
        ])->shouldBeCalledTimes(1);

        $this->organiser->organiseArguments($this->functionMock->reveal(), ['scenarioBanana', 'gorilla']);
    }
}
