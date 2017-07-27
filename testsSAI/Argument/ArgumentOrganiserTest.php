<?php

/*
 * This file is part of the StepArgumentInjectorBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\StepArgumentInjectorBehatExtension\Argument;

use Doctrine\Common\Annotations\Reader;
use Gorghoa\StepArgumentInjectorBehatExtension\Annotation\StepInjectorArgument;
use Prophecy\Prophecy\ObjectProphecy;
use Behat\Testwork\Argument\ArgumentOrganiser as BehatArgumentOrganiser;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class ArgumentOrganiserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArgumentOrganiser
     */
    private $organiser;

    /**
     * @var ObjectProphecy|ArgumentOrganiser
     */
    private $organiserMock;

    /**
     * @var ObjectProphecy
     */
    private $initializerMock;

    /**
     * @var ObjectProphecy|\ReflectionMethod
     */
    private $functionMock;

    /**
     * @var ObjectProphecy|Reader
     */
    private $readerMock;

    /**
     * @var StepArgumentHolder
     */
    private $holderMock;

    protected function setUp()
    {
        $this->organiserMock = $this->prophesize(BehatArgumentOrganiser::class);
        $this->functionMock = $this->prophesize(\ReflectionMethod::class);
        $this->readerMock = $this->prophesize(Reader::class);
        $this->holderMock = $this->prophesize(StepArgumentHolder::class);

        $this->organiser = new ArgumentOrganiser(
            $this->organiserMock->reveal(),
            [$this->holderMock->reveal()],
            $this->readerMock->reveal()
        );
    }

    /**
     * @return ObjectProphecy
     */
    private function annotationMockFactory()
    {
        return $this->prophesize(StepInjectorArgument::class);
    }

    public function testOrganiseArguments()
    {
        $this->functionMock->getParameters()->willReturn([
            (object) ['name' => 'scenarioBanana'], // argument with injector annotation and **a service hold** value
            (object) ['name' => 'gorilla'], // argument with injector annotation but **no service hold** value
            (object) ['name' => 'foo'], // argument not handled by this extension
        ])->shouldBeCalledTimes(1);

        $annot1 = $this->annotationMockFactory();
        $annot1->getArgument()->willReturn('scenarioBanana')->shouldBeCalledTimes(1);
        $annot1->reveal();

        $annot2 = $this->annotationMockFactory();
        $annot2->getArgument()->willReturn('gorilla')->shouldBeCalledTimes(1);
        $annot2->reveal();

        $this->readerMock->getMethodAnnotations($this->functionMock->reveal())->willReturn([
            $annot1,
            $annot2,
        ])->shouldBeCalledTimes(1);

        $this->holderMock->doesHandleStepArgument($annot1)->willReturn(true);
        $this->holderMock->doesHandleStepArgument($annot2)->willReturn(false);

        $this->holderMock->getStepArgumentValueFor($annot1)->willReturn('yammyBanana')->shouldBeCalledTimes(1);
        $this->holderMock->getStepArgumentValueFor($annot2)->shouldNotBeCalled();

        $this->holderMock->getStepArgumentValueFor($annot2);

        $this->organiserMock->organiseArguments($this->functionMock->reveal(), [
            0 => 'scenarioBanana',
            1 => 'gorilla',
            'scenarioBanana' => 'yammyBanana',
            2 => 'yammyBanana',
        ])->shouldBeCalledTimes(1);

        $this->organiser->organiseArguments($this->functionMock->reveal(), ['scenarioBanana', 'gorilla']);
    }
}
