<?php

namespace Gorghoa\ScenarioStateBehatExtension;

interface ScenarioStateInterface
{
    /**
     * @var string
     * @var mixed  $value
     */
    public function provideStateFragment($key, $value);

    /**
     * @var string
     *
     * @return mixed
     */
    public function getStateFragment($key);

    /**
     * @var string
     *
     * @return bool
     */
    public function hasStateFragment($key);

    /**
     * @return array
     */
    public function getStateFragmentsKeys();
}
