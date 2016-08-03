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
use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer;
use ReflectionFunctionAbstract;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class ScenarioStateArgumentOrganiser implements ArgumentOrganiser
{
    private $baseOrganiser;
    private $store;

    public function __construct(ArgumentOrganiser $organiser, ScenarioStateInitializer $store)
    {
        $this->baseOrganiser = $organiser;
        $this->store = $store;
    }

    /**
     * {@inheritdoc}
     */
    public function organiseArguments(ReflectionFunctionAbstract $function, array $match)
    {
        $store = $this->store->getStore();

        //@todo, be more defensive
        $i = array_slice(array_keys($match), -1, 1)[0];

        $parameters = $function->getParameters();

        $paramsKeys = array_map(function ($element) {
            return $element->name;
        }, $parameters);

        foreach ($paramsKeys as $key) {
            if ($store->hasStateFragment($key)) {
                $match[$key] = $store->getStateFragment($key);
                $match[strval(++$i)] = $store->getStateFragment($key);
            }
        }

        return $this->baseOrganiser->organiseArguments($function, $match);
    }
}
