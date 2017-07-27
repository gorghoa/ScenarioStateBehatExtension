<?php

/*
 * This file is part of the StepArgumentInjectorBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\StepArgumentInjectorBehatExtension\Argument;

use Behat\Testwork\Argument\ArgumentOrganiser as BehatArgumentOrganiser;
use Doctrine\Common\Annotations\Reader;
use Gorghoa\StepArgumentInjectorBehatExtension\Annotation\StepInjectorArgument;
// use Gorghoa\StepArgumentInjectorBehatExtension\Context\Initializer\StepArgumentInjectorInitializer;
use ReflectionFunctionAbstract;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class ArgumentOrganiser implements BehatArgumentOrganiser
{
    /**
     * @var BehatArgumentOrganiser
     */
    private $baseOrganiser;

    /**
     * @var StepArgumentHolder[]
     */
    private $stepArgumentHolders;

    /**
     * @var Reader
     */
    private $reader;

    public function __construct(BehatArgumentOrganiser $organiser, array $stepArgumentHolders, Reader $reader)
    {
        $this->baseOrganiser = $organiser;
        $this->stepArgumentHolders = $stepArgumentHolders;
        $this->reader = $reader;
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

        if (!$function instanceof \ReflectionMethod) {
            return $this->baseOrganiser->organiseArguments($function, $match);
        }

        $annotations = $this->reader->getMethodAnnotations($function);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof StepInjectorArgument &&
                in_array($argument = $annotation->getArgument(), $paramsKeys)
            ) {
                /* @var StepInjectorArgument $annotation */
                foreach ($this->stepArgumentHolders as $hooker) {
                    if ($hooker->doesHandleStepArgument($annotation)) {

                        $match[$argument]
                            = $match[strval(++$i)]
                            = $hooker->getStepArgumentValueFor($annotation)
                        ;
                    }
                }
            }
        }

        return $this->baseOrganiser->organiseArguments($function, $match);
    }
}
