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

use Gorghoa\ScenarioStateBehatExtension\ScenarioState\Exception\MissingStateException;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class ScenarioState implements ScenarioStateInterface
{
    /**
     * @var array
     */
    private $store = [];

    /**
     * {@inheritdoc}
     */
    public function hasStateFragment($key)
    {
        return array_key_exists($key, $this->store);
    }

    /**
     * {@inheritdoc}
     */
    public function getStateFragment($key)
    {
        if (!$this->hasStateFragment($key)) {
            throw new MissingStateException("Missing {$key} state fragment was requested from store.");
        }

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
