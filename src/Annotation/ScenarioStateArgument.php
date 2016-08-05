<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Annotation;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * @Annotation
 * @Target("METHOD")
 */
class ScenarioStateArgument
{
    /**
     * Argument name in store.
     *
     * @var string
     */
    public $name;

    /**
     * Argument name.
     *
     * @var string
     */
    public $argument;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (isset($options['value'])) {
            $options['name'] = $options['value'];
            unset($options['value']);
        }
        if (!isset($options['name']) || empty(trim($options['name']))) {
            throw new \InvalidArgumentException(
                'You must provide the store argument name in ScenarioStateArgument annotation'
            );
        }
        $this->name = $options['name'];
        $this->argument = isset($options['argument']) ? $options['argument'] : $this->name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getArgument()
    {
        return $this->argument;
    }
}
