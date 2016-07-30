<?php

namespace Gorghoa\ScenarioStateBehatExtension\Context;

use Behat\Behat\Context\Context;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;

interface ScenarioStateAwareContext extends Context
{
    public function setScenarioState(ScenarioStateInterface $systemState);
}
