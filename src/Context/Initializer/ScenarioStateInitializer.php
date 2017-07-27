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
use Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateArgument;
use Gorghoa\ScenarioStateBehatExtension\Context\ScenarioStateAwareContext;
use Gorghoa\ScenarioStateBehatExtension\ScenarioState;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateInterface;
use Gorghoa\StepArgumentInjectorBehatExtension\Annotation\StepInjectorArgument;
use Gorghoa\StepArgumentInjectorBehatExtension\Argument\StepArgumentHolder;
use Gorghoa\StepArgumentInjectorBehatExtension\Exception\RejectedAnnotationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class ScenarioStateInitializer implements ContextInitializer, EventSubscriberInterface, StepArgumentHolder
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

    /**
     * Test if this service should handle specific argument injection.
     *
     * @param StepInjectorArgument $annotation
     *
     * @return bool
     */
    public function doesHandleStepArgument(StepInjectorArgument $annotation)
    {
        return $annotation instanceof ScenarioStateArgument && $this->store->hasStateFragment($annotation->getName());
    }

    /**
     * Get an argument value to inject.
     *
     * @param StepInjectorArgument $annotation
     *
     * @throws RejectedAnnotationException
     *
     * @return mixed
     */
    public function getStepArgumentValueFor(StepInjectorArgument $annotation)
    {
        if (!($annotation instanceof ScenarioStateArgument)) {
            $class = get_class($annotation);
            throw new RejectedAnnotationException("$class not handled by ScenarioStateBehatExtension");
        }

        return $this->store->getStateFragment($annotation->getName());
    }
}
