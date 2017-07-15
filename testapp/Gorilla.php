<?php

namespace Gorghoa\ScenarioStateBehatExtension\TestApp;

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class Gorilla
{
    private $banana;
    private $name;
    private $male = false;

    /**
     * @param string $banana
     */
    public function setBanana($banana)
    {
        $this->banana = $banana;
    }

    /**
     * @return string
     */
    public function getBanana()
    {
        return $this->banana;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param bool $male
     */
    public function setMale($male)
    {
        $this->male = $male;
    }

    /**
     * @return bool
     */
    public function isMale()
    {
        return $this->male;
    }
}
