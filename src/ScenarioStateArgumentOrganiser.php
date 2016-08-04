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
use Doctrine\Common\Annotations\AnnotationReader;
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
        $i = array_slice(array_keys($match), -1, 1)[0];
        $paramsKeys = array_map(function ($element) {
            return $element->name;
        }, $function->getParameters());

        $store = $this->store->getStore();
        $reader = new AnnotationReader();

        // Ignore Behat annotations
        $reader::addGlobalIgnoredName('Given');
        $reader::addGlobalIgnoredName('When');
        $reader::addGlobalIgnoredName('Then');

        if (null !== ($annotation = $reader->getMethodAnnotation($function, ScenarioStateArgument::class))) {
            foreach ($paramsKeys as $key) {
                if (in_array($key, $annotation->mapping)) {
                    $key = array_search($key, $annotation->mapping);
                    if ($store->hasStateFragment($key)) {
                        $match[$key] = $store->getStateFragment($key);
                        $match[strval(++$i)] = $store->getStateFragment($key);
                    }
                }
            }
        }

        return $this->baseOrganiser->organiseArguments($function, $match);
    }
}
