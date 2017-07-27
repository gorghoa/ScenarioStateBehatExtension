<?php

/*
 * This file is part of the StepArgumentInjectorBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\StepArgumentInjectorBehatExtension\Resolver;

use Doctrine\Common\Annotations\Reader;
use Gorghoa\StepArgumentInjectorBehatExtension\Annotation\StepInjectorArgument;
use Gorghoa\StepArgumentInjectorBehatExtension\Argument\StepArgumentHolder;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class ArgumentsResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $readerMock = $this->prophesize(Reader::class);
        $functionMock = $this->prophesize(\ReflectionMethod::class);
        $parameterMock = $this->prophesize(\ReflectionParameter::class);
        $annotationMock = $this->prophesize(StepInjectorArgument::class);

        $functionMock->getParameters()->willReturn([$parameterMock, $parameterMock])->shouldBeCalledTimes(2);
        $parameterMock->getName()->willReturn('lorem', 'foo', 'lorem', 'foo')->shouldBeCalledTimes(4);

        $holderMock1 = $this->prophesize(StepArgumentHolder::class);
        $holderMock2 = $this->prophesize(StepArgumentHolder::class);

        $readerMock->getMethodAnnotation($functionMock, StepInjectorArgument::class)->willReturn($annotationMock)->shouldBeCalledTimes(1);
        $readerMock->getMethodAnnotations($functionMock)->willReturn([$this->prophesize(\stdClass::class), $annotationMock])->shouldBeCalledTimes(1);
        $annotationMock->getArgument()->willReturn('lorem')->shouldBeCalledTimes(1);

        $holderMock1->doesHandleStepArgument($annotationMock)->willReturn(true)->shouldBeCalledTimes(1);
        $holderMock1->getStepArgumentValueFor($annotationMock)->willReturn(true)->shouldBeCalledTimes(1);

        $holderMock2->doesHandleStepArgument($annotationMock)->willReturn(false)->shouldBeCalledTimes(1);
        $holderMock2->getStepArgumentValueFor($annotationMock)->shouldNotBeCalled();

        $resolver = new ArgumentsResolver([$holderMock1->reveal(), $holderMock2->reveal()], $readerMock->reveal());
        $resolver->resolve($functionMock->reveal(), ['lorem' => 'pouet', 'foo' => 'bar']);
    }
}
