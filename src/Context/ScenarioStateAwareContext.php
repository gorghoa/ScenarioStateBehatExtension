<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Context;

use Behat\Behat\Context\Context;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
interface ScenarioStateAwareContext extends Context
{
    /**
     * @param ScenarioStateInterface $systemState
     */
    public function setScenarioState(ScenarioStateInterface $systemState);
}
