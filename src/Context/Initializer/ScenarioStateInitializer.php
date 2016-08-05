<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext;
use Gorghoa\ScenarioStateBehatExtension\ScenarioState;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class ScenarioStateInitializer implements ContextInitializer, EventSubscriberInterface
{
    /**
     * @var ScenarioStateInterface
     */
    private $store;

    public function __construct()
    {
        $this->clearStore();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScenarioTested::AFTER => ['clearStore'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function initializeContext(Context $context)
    {
        if (!$context instanceof ScenarioStateAwareContext) {
            return;
        }

        $context->setScenarioState($this->store);
    }

    public function clearStore()
    {
        $this->store = new ScenarioState();
    }

    /**
     * @return ScenarioStateInterface
     */
    public function getStore()
    {
        return $this->store;
    }
}
