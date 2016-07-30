<?php

namespace Gorghoa\ScenarioStateBehatExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Gorghoa\ScenarioStateBehatExtension\ScenarioState;
use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext;

final class ScenarioStateInitializer implements ContextInitializer, EventSubscriberInterface
{
    private $store;

    /**
     * Initializes initializer.
     *
     * @param KernelInterface $kernel
     */
    public function __construct()
    {
        $this->clearStore();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScenarioTested::AFTER => array('clearStore'),
        );
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

    private function getNewStore()
    {
        return new ScenarioState();
    }

    public function clearStore()
    {
        $this->store = $this->getNewStore();
    }

    public function getStore()
    {
        return $this->store;
    }
}
