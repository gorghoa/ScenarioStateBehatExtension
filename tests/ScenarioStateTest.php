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

use Gorghoa\ScenarioStateBehatExtension\ScenarioState\Exception\MissingStateException;

/**
 * Class ScenarioStateTest
 * @package Gorghoa\ScenarioStateBehatExtension
 */
class ScenarioStateTest extends \PHPUnit_Framework_TestCase
{
    public function testItThrowsExceptionWhenStateIsMissing()
    {
        $this->setExpectedException(MissingStateException::class);
        $scenarioState = new ScenarioState();
        $scenarioState->getStateFragment('not_existing_state');
    }
}
