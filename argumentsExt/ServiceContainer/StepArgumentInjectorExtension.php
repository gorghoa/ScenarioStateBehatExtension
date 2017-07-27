<?php

/*
 * This file is part of the StepArgumentInjectorBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\StepArgumentInjectorBehatExtension\ServiceContainer;

use Behat\Testwork\Argument\ServiceContainer\ArgumentExtension;
use Behat\Testwork\Call\ServiceContainer\CallExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Gorghoa\StepArgumentInjectorBehatExtension\Argument\ArgumentOrganiser;
use Gorghoa\StepArgumentInjectorBehatExtension\Call\Handler\RuntimeCallHandler;
use Gorghoa\StepArgumentInjectorBehatExtension\Resolver\ArgumentsResolver;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Behat store for Behat contexts.
 *
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class StepArgumentInjectorExtension implements ExtensionInterface
{
    const STEP_ARGUMENT_INJECTOR_ARGUMENT_ORGANISER_ID = 'argument.step_argument_injector.organiser';
    const STEP_ARGUMENT_INJECTOR_DISPATCHER_ID = 'hook.step_argument_injector.dispatcher';
    const STEP_ARGUMENT_INJECTOR_TESTER_ID = 'tester.step_argument_injector.wrapper';
    const STEP_ARGUMENT_INJECTOR_CALL_HANDLER_ID = 'call.step_argument_injector.call_handler';
    const STEP_ARGUMENT_INJECTOR_ARGUMENTS_RESOLVER_ID = 'step_argument_injector.arguments_resolver';
    const STEP_ARGUMENT_INJECTOR_STORE_ID = 'behatstore.context_initializer.store_aware';
    const STEP_ARGUMENT_INJECTOR_DOCTRINE_READER_ID = 'doctrine.reader.annotation';
    const STEP_ARGUMENT_INJECTOR_HOOK_TAG_ID = 'step_argument_injector.hook_tag_id';

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'stepargumentinjector';
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
        // Declare Doctrine annotation reader as service
        $container->register(self::STEP_ARGUMENT_INJECTOR_DOCTRINE_READER_ID, AnnotationReader::class)
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

        $taggedServices = array_map(function ($serviceId) {
            return new Reference($serviceId);
        }, array_keys($container->findTaggedServiceIds(self::STEP_ARGUMENT_INJECTOR_HOOK_TAG_ID)));

        // Arguments resolver: resolve StepArgumentInjector arguments from annotation
        $container->register(self::STEP_ARGUMENT_INJECTOR_ARGUMENTS_RESOLVER_ID, ArgumentsResolver::class)
            ->setArguments([
                $taggedServices,
                new Reference(self::STEP_ARGUMENT_INJECTOR_DOCTRINE_READER_ID),
            ]);

        // Argument organiser
        $container->register(self::STEP_ARGUMENT_INJECTOR_ARGUMENT_ORGANISER_ID, ArgumentOrganiser::class)
            ->setDecoratedService(ArgumentExtension::PREG_MATCH_ARGUMENT_ORGANISER_ID)
            ->setPublic(false)
            ->setArguments([
                new Reference(sprintf('%s.inner', self::STEP_ARGUMENT_INJECTOR_ARGUMENT_ORGANISER_ID)),
                $taggedServices,
                new Reference(self::STEP_ARGUMENT_INJECTOR_DOCTRINE_READER_ID),
                new Reference(self::STEP_ARGUMENT_INJECTOR_ARGUMENTS_RESOLVER_ID),
            ]);

        // Override calls process
        $container->register(self::STEP_ARGUMENT_INJECTOR_CALL_HANDLER_ID, RuntimeCallHandler::class)
            ->setDecoratedService(CallExtension::CALL_HANDLER_TAG.'.runtime')
            ->setArguments([
                new Reference(self::STEP_ARGUMENT_INJECTOR_CALL_HANDLER_ID.'.inner'),
                new Reference(self::STEP_ARGUMENT_INJECTOR_ARGUMENTS_RESOLVER_ID),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }
}
