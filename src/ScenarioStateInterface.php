<?php

namespace Gorghoa\ScenarioStateBehatExtension;

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
