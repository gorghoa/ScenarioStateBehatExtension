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

use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
trait ScenarioStateAwareTrait
{
    /**
     * @var ScenarioStateInterface
     */
    private $scenarioState;

    /**
     * @param ScenarioStateInterface $scenarioState
     */
    public function setScenarioState(ScenarioStateInterface $scenarioState)
    {
        $this->scenarioState = $scenarioState;
    }
}
