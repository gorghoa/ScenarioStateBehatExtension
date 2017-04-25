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

use Gorghoa\ScenarioStateBehatExtension\Exception\MissingStateException;

/**
 * @author Walter Dolce <walterdolce@gmail.com>
 */
class ScenarioStateTest extends \PHPUnit_Framework_TestCase
{
    public function testItThrowsExceptionWhenStateIsMissing()
    {
        $this->setExpectedException(MissingStateException::class);
        (new ScenarioState())->getStateFragment('not_existing_state');
    }
}
