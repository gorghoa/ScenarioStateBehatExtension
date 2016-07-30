<?php

namespace Gorghoa\ScenarioStateBehatExtension;

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
