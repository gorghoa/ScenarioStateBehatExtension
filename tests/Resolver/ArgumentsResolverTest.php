<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Resolver;

use Doctrine\Common\Annotations\Reader;
use Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateArgument;
use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
class ArgumentsResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $initializerMock = $this->prophesize(ScenarioStateInitializer::class);
        $storeMock = $this->prophesize(ScenarioStateInterface::class);
        $readerMock = $this->prophesize(Reader::class);
        $functionMock = $this->prophesize(\ReflectionMethod::class);
        $parameterMock = $this->prophesize(\ReflectionParameter::class);
        $annotationMock = $this->prophesize(ScenarioStateArgument::class);

        $functionMock->getParameters()->willReturn([$parameterMock, $parameterMock])->shouldBeCalledTimes(2);
        $parameterMock->getName()->willReturn('lorem', 'foo', 'lorem', 'lorem', 'foo', 'foo')->shouldBeCalledTimes(6);
        $initializerMock->getStore()->willReturn($storeMock)->shouldBeCalledTimes(1);
        $readerMock->getMethodAnnotation($functionMock, ScenarioStateArgument::class)->willReturn($annotationMock)->shouldBeCalledTimes(1);
        $readerMock->getMethodAnnotations($functionMock)->willReturn([$this->prophesize(\stdClass::class), $annotationMock])->shouldBeCalledTimes(1);
        $annotationMock->getArgument()->willReturn('lorem')->shouldBeCalledTimes(2);
        $annotationMock->getName()->willReturn('ipsum')->shouldBeCalledTimes(2);
        $storeMock->hasStateFragment('ipsum')->willReturn(true)->shouldBeCalledTimes(1);
        $storeMock->getStateFragment('ipsum')->willReturn('pouet')->shouldBeCalledTimes(1);

        $resolver = new ArgumentsResolver($initializerMock->reveal(), $readerMock->reveal());
        $resolver->resolve($functionMock->reveal(), ['lorem' => 'pouet', 'foo' => 'bar']);
    }
}
