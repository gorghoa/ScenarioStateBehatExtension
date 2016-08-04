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
use Behat\Testwork\Argument\ServiceContainer\ArgumentExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateArgumentOrganiser;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Behat store for Behat contexts.
 *
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ScenarioStateExtension implements ExtensionInterface
{
    const SCENARIO_STATE_ARGUMENT_ORGANISER_ID = 'argument.scenario_state_organiser';

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
        $this->loadContextInitializer($container);
        $this->loadOrganiser($container);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition(ScenarioStateInitializer::class, []);
        $definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);

        $container->setDefinition('behatstore.context_initializer.store_aware', $definition);
    }

    private function loadOrganiser(ContainerBuilder $container)
    {
        $container->register(self::SCENARIO_STATE_ARGUMENT_ORGANISER_ID, ScenarioStateArgumentOrganiser::class)
            ->setDecoratedService(ArgumentExtension::PREG_MATCH_ARGUMENT_ORGANISER_ID)
            ->setPublic(false)
            ->setArguments([
                new Reference(sprintf('%s.inner', self::SCENARIO_STATE_ARGUMENT_ORGANISER_ID)),
                new Reference('behatstore.context_initializer.store_aware'),
            ]);
    }
}
