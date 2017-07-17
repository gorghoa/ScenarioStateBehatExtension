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
use Gorghoa\ScenarioStateBehatExtension\Call\Handler\RuntimeCallHandler;
use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer;
use Gorghoa\ScenarioStateBehatExtension\Hook\Dispatcher\ScenarioStateHookDispatcher;
use Gorghoa\ScenarioStateBehatExtension\Hook\Tester\ScenarioStateHookableScenarioTester;
use Gorghoa\ScenarioStateBehatExtension\Resolver\ArgumentsResolver;
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
    const SCENARIO_STATE_CALL_HANDLER_ID = 'call.scenario_state.call_handler';
    const SCENARIO_STATE_ARGUMENTS_RESOLVER_ID = 'scenario_state.arguments_resolver';
    const SCENARIO_STATE_STORE_ID = 'behatstore.context_initializer.store_aware';
    const SCENARIO_STATE_DOCTRINE_READER_ID = 'doctrine.reader.annotation';

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
            ->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);

        // Declare Doctrine annotation reader as service
        $container->register(self::SCENARIO_STATE_DOCTRINE_READER_ID, AnnotationReader::class)
            // Ignore Behat annotations in reader
            ->addMethodCall('addGlobalIgnoredName', ['Given'])
            ->addMethodCall('addGlobalIgnoredName', ['When'])
            ->addMethodCall('addGlobalIgnoredName', ['Then'])
            ->addMethodCall('addGlobalIgnoredName', ['Transform'])
            ->addMethodCall('addGlobalIgnoredName', ['BeforeStep'])
            ->addMethodCall('addGlobalIgnoredName', ['BeforeScenario'])
            ->addMethodCall('addGlobalIgnoredName', ['BeforeFeature'])
            ->addMethodCall('addGlobalIgnoredName', ['BeforeSuite'])
            ->addMethodCall('addGlobalIgnoredName', ['AfterStep'])
            ->addMethodCall('addGlobalIgnoredName', ['AfterScenario'])
            ->addMethodCall('addGlobalIgnoredName', ['AfterFeature'])
            ->addMethodCall('addGlobalIgnoredName', ['AfterSuite']);

        // Arguments resolver: resolve ScenarioState arguments from annotation
        $container->register(self::SCENARIO_STATE_ARGUMENTS_RESOLVER_ID, ArgumentsResolver::class)
            ->setArguments([
                new Reference(self::SCENARIO_STATE_STORE_ID),
                new Reference(self::SCENARIO_STATE_DOCTRINE_READER_ID),
            ]);

        // Argument organiser
        $container->register(self::SCENARIO_STATE_ARGUMENT_ORGANISER_ID, ScenarioStateArgumentOrganiser::class)
            ->setDecoratedService(ArgumentExtension::PREG_MATCH_ARGUMENT_ORGANISER_ID)
            ->setPublic(false)
            ->setArguments([
                new Reference(sprintf('%s.inner', self::SCENARIO_STATE_ARGUMENT_ORGANISER_ID)),
                new Reference(self::SCENARIO_STATE_STORE_ID),
                new Reference(self::SCENARIO_STATE_DOCTRINE_READER_ID),
                new Reference(self::SCENARIO_STATE_ARGUMENTS_RESOLVER_ID),
            ]);

        // Override calls process
        $container->register(self::SCENARIO_STATE_CALL_HANDLER_ID, RuntimeCallHandler::class)
            ->setDecoratedService(CallExtension::CALL_HANDLER_TAG.'.runtime')
            ->setArguments([
                new Reference(self::SCENARIO_STATE_CALL_HANDLER_ID.'.inner'),
                new Reference(self::SCENARIO_STATE_ARGUMENTS_RESOLVER_ID),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }
}
