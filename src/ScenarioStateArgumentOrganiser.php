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

use Behat\Testwork\Argument\ArgumentOrganiser;
use Doctrine\Common\Annotations\Reader;
use Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateArgument;
use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer;
use ReflectionFunctionAbstract;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class ScenarioStateArgumentOrganiser implements ArgumentOrganiser
{
    private $baseOrganiser;
    private $store;
    private $reader;

    public function __construct(ArgumentOrganiser $organiser, ScenarioStateInitializer $store, Reader $reader)
    {
        $this->baseOrganiser = $organiser;
        $this->store = $store;
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function organiseArguments(ReflectionFunctionAbstract $function, array $match)
    {
        $i = array_slice(array_keys($match), -1, 1)[0];
        $paramsKeys = array_map(function($element) {
            return $element->name;
        }, $function->getParameters());

        $store = $this->store->getStore();

        if (!($function instanceof \ReflectionMethod)) {
            return $this->baseOrganiser->organiseArguments($function, $match);
        }

        /** @var ScenarioStateArgument[] $annotations */
        $annotations = $this->reader->getMethodAnnotations($function);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof ScenarioStateArgument &&
                in_array($annotation->getArgument(), $paramsKeys) &&
                $store->hasStateFragment($annotation->getName())
            ) {
                $match[$annotation->getArgument()] = $store->getStateFragment($annotation->getName());
                $match[strval(++$i)] = $store->getStateFragment($annotation->getName());
            }
        }

        return $this->baseOrganiser->organiseArguments($function, $match);
    }
}
