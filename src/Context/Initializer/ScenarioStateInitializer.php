<?php

namespace Gorghoa\ScenarioStateBehatExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext;
use Gorghoa\ScenarioStateBehatExtension\ScenarioState;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ScenarioStateInitializer implements ContextInitializer, EventSubscriberInterface
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
