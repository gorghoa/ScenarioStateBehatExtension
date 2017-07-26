<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Annotation;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ScenarioStateArgumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getArguments
     *
     * @param array  $arguments
     * @param string $name
     * @param string $argument
     */
    public function testWithValue(array $arguments, $name, $argument)
    {
        $annotation = new ScenarioStateArgument($arguments);
        $this->assertEquals($name, $annotation->name);
        $this->assertEquals($argument, $annotation->argument);
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return [
            [
                ['value' => 'foo'],
                'foo',
                'foo',
            ],
            [
                ['name' => 'foo'],
                'foo',
                'foo',
            ],
            [
                ['name' => 'foo', 'argument' => 'bar'],
                'foo',
                'bar',
            ],
        ];
    }
}
