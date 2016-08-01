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

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class ScenarioState implements ScenarioStateInterface
{
    private $store = [];

    /**
     * {@inheritdoc}
     */
    public function hasStateFragment($key)
    {
        return isset($this->store[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function getStateFragment($key)
    {
        return $this->store[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function provideStateFragment($key, $value)
    {
        $this->store[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getStateFragmentsKeys()
    {
        return array_keys($this->store);
    }
}
