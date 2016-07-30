<?php

class Gorilla
{
    private $banana = null;

    public function setBanana(array $banana)
    {
        $this->banana = $banana;
    }

    public function getBanana()
    {
        return $this->banana;
    }
}
