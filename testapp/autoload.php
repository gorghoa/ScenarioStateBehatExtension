<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/Gorilla.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerFile(__DIR__.'/../src/Annotation/ScenarioStateArgument.php');
