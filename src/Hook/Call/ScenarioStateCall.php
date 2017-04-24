<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Hook\Call;

use Behat\Testwork\Environment\Call\EnvironmentCall;
use Behat\Testwork\Hook\Hook;
use Behat\Testwork\Hook\Scope\HookScope;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
final class ScenarioStateCall extends EnvironmentCall
{
    /**
     * @var HookScope
     */
    private $scope;

    /**
     * @param HookScope $scope
     * @param Hook      $hook
     * @param array     $arguments
     * @param null      $errorReportingLevel
     */
    public function __construct(HookScope $scope, Hook $hook, $arguments, $errorReportingLevel = null)
    {
        parent::__construct($scope->getEnvironment(), $hook, array($scope, $arguments), $errorReportingLevel);

        $this->scope = $scope;
    }

    /**
     * @return HookScope
     */
    public function getScope()
    {
        return $this->scope;
    }
}
