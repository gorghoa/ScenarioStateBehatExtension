<?php

namespace Gorghoa\ScenarioStateBehatExtension\Context;

use Behat\Behat\Context\Context;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;

interface ScenarioStateAwareContext extends Context
{
    /**
     * @param ScenarioStateInterface $systemState
     */
    public function setScenarioState(ScenarioStateInterface $systemState);
}
