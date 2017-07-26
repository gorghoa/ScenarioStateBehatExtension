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
use Gorghoa\StepArgumentInjectorBehatExtension\Annotation\StepArgumentInjectorArgument;
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
     * @var StepArgumentInjectorInitializer
     */
    private $hookers;

    /**
     * @var Reader
     */
    private $reader;

    public function __construct(BehatArgumentOrganiser $organiser, array $hookers, Reader $reader)
    {
        $this->baseOrganiser = $organiser;
        $this->hookers = $hookers;
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

        /** @var StepArgumentInjectorArgument[] $annotations */
        $annotations = $this->reader->getMethodAnnotations($function);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof StepInjectorArgument &&
                in_array($annotation->getArgument(), $paramsKeys)
            ) {
                foreach ($this->hookers as $hooker) {
                    if ($hooker->hasStepArgumentFor($annotation->getName())) {
                        $match[$annotation->getArgument()]
                            = $match[strval(++$i)]
                            = $hooker->getStepArgumentFor($annotation->getName())
                        ;
                    }
                }
            }
        }

        return $this->baseOrganiser->organiseArguments($function, $match);
    }
}
