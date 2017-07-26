<?php

/*
 * This file is part of the StepArgumentInjectorBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\StepArgumentInjectorBehatExtension\Resolver;

use Doctrine\Common\Annotations\Reader;
use Gorghoa\StepArgumentInjectorBehatExtension\Annotation\StepInjectorArgument;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
class ArgumentsResolver
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var array
     */
    private $hookers;

    /**
     * @param Reader $reader
     */
    public function __construct(array $hookers, Reader $reader)
    {
        $this->hookers = $hookers;
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
        // No `@StepArgumentInjectorArgument` annotation found
        if (null === $this->reader->getMethodAnnotation($function, StepInjectorArgument::class)) {
            return $arguments;
        }

        $paramsKeys = array_map(function (\ReflectionParameter $element) {
            return $element->getName();
        }, $function->getParameters());

        // Prepare arguments from annotations
        /** @var StepArgumentInjectorArgument[] $annotations */
        $annotations = $this->reader->getMethodAnnotations($function);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof StepInjectorArgument &&
                in_array($annotation->getArgument(), $paramsKeys)
            ) {
                foreach ($this->hookers as $hooker) {
                    if ($hooker->hasStepArgumentFor($annotation->getName())) {
                        $arguments[$annotation->getArgument()] = $hooker->getStepArgumentFor($annotation->getName());
                    }
                }
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
