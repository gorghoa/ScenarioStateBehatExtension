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
use Gorghoa\ScenarioStateBehatExtension\Hook\Tester\ScenarioStateHookableScenarioTester;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Behat store for Behat contexts.
 *
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ScenarioStateExtension implements ExtensionInterface
{
    const SCENARIO_STATE_ARGUMENT_ORGANISER_ID = 'argument.scenario_state.organiser';
    const SCENARIO_STATE_DISPATCHER_ID = 'hook.scenario_state.dispatcher';
    const SCENARIO_STATE_TESTER_ID = 'tester.scenario_state.wrapper';

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
        $container->register('behatstore.context_initializer.store_aware', ScenarioStateInitializer::class)
            ->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0])
            ->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);
    }

    private function loadOrganiser(ContainerBuilder $container)
    {
        // Declare Doctrine annotation reader as service
        $container->register('doctrine.reader.annotation', AnnotationReader::class)
            // Ignore Behat annotations in reader
            ->addMethodCall('addGlobalIgnoredName', ['Given'])
            ->addMethodCall('addGlobalIgnoredName', ['When'])
            ->addMethodCall('addGlobalIgnoredName', ['Then'])
            ->addMethodCall('addGlobalIgnoredName', ['Transform'])
            ->addMethodCall('addGlobalIgnoredName', ['BeforeScenario'])
            ->addMethodCall('addGlobalIgnoredName', ['AfterScenario']);

        $container->register(self::SCENARIO_STATE_ARGUMENT_ORGANISER_ID, ScenarioStateArgumentOrganiser::class)
            ->setDecoratedService(ArgumentExtension::PREG_MATCH_ARGUMENT_ORGANISER_ID)
            ->setPublic(false)
            ->setArguments([
                new Reference(sprintf('%s.inner', self::SCENARIO_STATE_ARGUMENT_ORGANISER_ID)),
                new Reference('behatstore.context_initializer.store_aware'),
                new Reference('doctrine.reader.annotation'),
            ]);

        // Override hook process
        $container->register(self::SCENARIO_STATE_DISPATCHER_ID, ScenarioStateHookDispatcher::class)
            ->setPublic(false)
            ->setArguments([
                new Reference(HookExtension::REPOSITORY_ID),
                new Reference(CallExtension::CALL_CENTER_ID),
                new Reference('behatstore.context_initializer.store_aware'),
                new Reference('doctrine.reader.annotation'),
            ]);
        $container->register(self::SCENARIO_STATE_TESTER_ID, ScenarioStateHookableScenarioTester::class)
            ->setDecoratedService(TesterExtension::SCENARIO_TESTER_WRAPPER_TAG.'.hookable')
            ->setArguments([
                new Reference(self::SCENARIO_STATE_TESTER_ID.'.inner'),
                new Reference(self::SCENARIO_STATE_DISPATCHER_ID),
            ]);
    }
}
