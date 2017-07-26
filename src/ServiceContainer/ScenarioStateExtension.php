<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer;
use Gorghoa\StepArgumentInjectorBehatExtension\ServiceContainer\StepArgumentInjectorExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Behat store for Behat contexts.
 *
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ScenarioStateExtension implements ExtensionInterface
{
    const SCENARIO_STATE_STORE_ID = 'behatstore.context_initializer.store_aware';
    const SCENARIO_STATE_ARGUMENT_INJECTOR_STORE_ID = 'behatstore.context_initializer.store_aware';

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'scenariostate';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        AnnotationRegistry::registerFile(__DIR__.'/../Annotation/ScenarioStateArgument.php');
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        // Load ScenarioState store
        $container->register(self::SCENARIO_STATE_STORE_ID, ScenarioStateInitializer::class)
            ->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0])
            ->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0])
            ->addTag(StepArgumentInjectorExtension::STEP_ARGUMENT_INJECTOR_HOOK_TAG_ID, ['priority' => 0])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }
}
