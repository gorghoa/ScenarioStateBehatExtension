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
use Behat\Behat\Tester\ServiceContainer\TesterExtension;
use Behat\Testwork\Argument\ServiceContainer\ArgumentExtension;
use Behat\Testwork\Call\ServiceContainer\CallExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\Hook\ServiceContainer\HookExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Gorghoa\ScenarioStateBehatExtension\Argument\ScenarioStateArgumentOrganiser;
use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer;
use Gorghoa\ScenarioStateBehatExtension\Hook\Dispatcher\ScenarioStateHookDispatcher;
use Gorghoa\ScenarioStateBehatExtension\Hook\Tester\HookableScenarioTester;
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
    const SCENARIO_STATE_DISPATCHER_ID = 'hook.scenario_state_dispatcher';
    const SCENARIO_STATE_TESTER_ID = 'tester.scenario_state_scenario';

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
        // Declare Doctrine annotation reader as service
        $container->register('doctrine.reader.annotation', AnnotationReader::class)
            // Ignore Behat annotations in reader
            ->addMethodCall('addGlobalIgnoredName', ['Given'])
            ->addMethodCall('addGlobalIgnoredName', ['When'])
            ->addMethodCall('addGlobalIgnoredName', ['Then']);

        $container->register(self::SCENARIO_STATE_ARGUMENT_ORGANISER_ID, ScenarioStateArgumentOrganiser::class)
            ->setDecoratedService(ArgumentExtension::PREG_MATCH_ARGUMENT_ORGANISER_ID)
            ->setPublic(false)
            ->setArguments([
                new Reference(sprintf('%s.inner', self::SCENARIO_STATE_ARGUMENT_ORGANISER_ID)),
                new Reference('behatstore.context_initializer.store_aware'),
                new Reference('doctrine.reader.annotation'),
            ]);
    }
}
