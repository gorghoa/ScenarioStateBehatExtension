<?php

/*
 * This file is part of the StepArgumentInjectorBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\StepArgumentInjectorBehatExtension\Annotation;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
interface StepInjectorArgument
{
    /**
     * The argument name this annotation is targeting to inject.
     *
     * @return string
     */
    public function getArgument();
}
