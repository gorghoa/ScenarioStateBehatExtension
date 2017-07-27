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

use Gorghoa\ScenarioStateBehatExtension\Exception\MissingStateException;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
interface ScenarioStateInterface
{
    /**
     * @param string $key
     * @param mixed  $value
     */
    public function provideStateFragment($key, $value);

    /**
     * @param string $key
     *
     * @throws MissingStateException
     *
     * @return mixed
     */
    public function getStateFragment($key);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasStateFragment($key);

    /**
     * @return array
     */
    public function getStateFragmentsKeys();
}
