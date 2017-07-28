<?php

/*
 * This file is part of the StepArgumentInjectorBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\StepArgumentInjectorBehatExtension\Event;

use Gorghoa\StepArgumentInjectorBehatExtension\Annotation\StepInjectorArgument;
use Symfony\Component\EventDispatcher\Event;


/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
final class ArgumentResolutionEvent extends Event
{
    /**
     * @var StepInjectorArgument
     */
    private $annotation;

}