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

use Gorghoa\StepArgumentInjectorBehatExtension\Annotation\StepInjectorArgument;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
interface StepArgumentHolder
{
    /**
     * Check if an annotation is handled by the service.
     *
     * @param StepInjectorArgument $annotation
     *
     * @return bool
     */
    public function doesHandleStepArgument(StepInjectorArgument $annotation);

    /**
     * Get value to inject for a step argument.
     *
     * @param StepInjectorArgument $annotation
     *
     * @return mixed
     */
    public function getStepArgumentValueFor(StepInjectorArgument $annotation);
}
