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

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
class ArgumentsResolver
{
    /**
     * @var ScenarioStateInitializer
     */
    private $store;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param ScenarioStateInitializer $store
     * @param Reader                   $reader
     */
    public function __construct(ScenarioStateInitializer $store, Reader $reader)
    {
        $this->store = $store;
        $this->reader = $reader;
    }

    /**
     * @param \ReflectionMethod $function
     * @param array             $arguments
     *
     * @return array
     */
    public function resolve(\ReflectionMethod $function, array $arguments)
    {
        // No `@ScenarioStateArgument` annotation found
        if (null === $this->reader->getMethodAnnotation($function, ScenarioStateArgument::class)) {
            return $arguments;
        }

        $paramsKeys = array_map(function (\ReflectionParameter $element) {
            return $element->getName();
        }, $function->getParameters());
        $store = $this->store->getStore();

        // Prepare arguments from annotations
        /** @var ScenarioStateArgument[] $annotations */
        $annotations = $this->reader->getMethodAnnotations($function);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof ScenarioStateArgument &&
                in_array($annotation->getArgument(), $paramsKeys) &&
                $store->hasStateFragment($annotation->getName())
            ) {
                $arguments[$annotation->getArgument()] = $store->getStateFragment($annotation->getName());
            }
        }

        // Reorder arguments
        $params = [];
        foreach ($function->getParameters() as $parameter) {
            $name = $parameter->getName();
            $params[$name] = isset($arguments[$name]) ? $arguments[$name] : $arguments[$parameter->getPosition()];
        }

        return $params;
    }
}
