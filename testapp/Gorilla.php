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

    /**
     * @param string $banana
     */
    public function setBanana($banana)
    {
        $this->banana = $banana;
    }

    public function getBanana()
    {
        return $this->banana;
    }
}
