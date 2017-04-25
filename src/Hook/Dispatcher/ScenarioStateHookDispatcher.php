<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Hook\Dispatcher;

use Behat\Testwork\Call\CallCenter;
use Behat\Testwork\Call\CallResults;
use Behat\Testwork\Environment\Call\EnvironmentCall;
use Behat\Testwork\Hook\Call\HookCall;
use Behat\Testwork\Hook\HookRepository;
use Behat\Testwork\Hook\Scope\HookScope;
use Doctrine\Common\Annotations\Reader;
use Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateArgument;
use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
class ScenarioStateHookDispatcher
{
    /**
     * @var HookRepository
     */
    private $repository;

    /**
     * @var CallCenter
     */
    private $callCenter;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ScenarioStateInitializer
     */
    private $store;

    /**
     * Initializes scenario state hook dispatcher.
     *
     * @param HookRepository           $repository
     * @param CallCenter               $callCenter
     * @param ScenarioStateInitializer $store
     * @param Reader                   $reader
     */
    public function __construct(HookRepository $repository, CallCenter $callCenter, ScenarioStateInitializer $store, Reader $reader)
    {
        $this->repository = $repository;
        $this->callCenter = $callCenter;
        $this->reader = $reader;
        $this->store = $store;
    }

    /**
     * Dispatches hooks for a specified event.
     *
     * @param HookScope $scope
     *
     * @return CallResults
     */
    public function dispatchScopeHooks(HookScope $scope)
    {
        $results = array();
        foreach ($this->repository->getScopeHooks($scope) as $hook) {
            /** @var \ReflectionMethod $function */
            $function = $hook->getReflection();

            // No `@ScenarioStateArgument` annotation found
            if (null === $this->reader->getMethodAnnotation($function, ScenarioStateArgument::class)) {
                $results[] = $this->callCenter->makeCall(new HookCall($scope, $hook));
                continue;
            }

            $paramsKeys = array_map(function ($element) {
                return $element->name;
            }, $function->getParameters());
            $store = $this->store->getStore();
            $params = $arguments = [];

            // Prepare arguments from annotations
            /** @var ScenarioStateArgument[] $annotations */
            $annotations = $this->reader->getMethodAnnotations($function);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof ScenarioStateArgument &&
                    in_array($annotation->getArgument(), $paramsKeys) &&
                    $store->hasStateFragment($annotation->getName())
                ) {
                    $params[$annotation->getArgument()] = $store->getStateFragment($annotation->getName());
                }
            }

            // Manage `scope` argument
            foreach ($function->getParameters() as $parameter) {
                if (null !== $parameter->getClass() && get_class($scope) === $parameter->getClass()->getName()) {
                    $arguments[$parameter->getName()] = $scope;
                } elseif (isset($params[$parameter->getName()])) {
                    $arguments[$parameter->getName()] = $params[$parameter->getName()];
                }
            }

            $results[] = $this->callCenter->makeCall(new EnvironmentCall($scope->getEnvironment(), $hook, $arguments));
        }

        return new CallResults($results);
    }
}
