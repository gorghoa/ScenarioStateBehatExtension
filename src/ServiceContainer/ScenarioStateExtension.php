<?php

namespace Gorghoa\ScenarioStateBehatExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\Argument\PregMatchArgumentOrganiser;
use Behat\Testwork\Argument\ServiceContainer\ArgumentExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Gorghoa\ScenarioStateBehatExtension\ScenarioStateArgumentOrganiser;
use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer;

/**
 * Behat store for Behat class.
 *
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 */
class ScenarioStateExtension implements ExtensionInterface
{
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
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 0));
        $container->setDefinition('behatstore.context_initializer.store_aware', $definition);
    }

    private function loadOrganiser(ContainerBuilder $container)
    {
        $definition = new Definition(PregMatchArgumentOrganiser::class, array(
            new Reference(ArgumentExtension::MIXED_ARGUMENT_ORGANISER_ID),
        ));
        $container->setDefinition(ArgumentExtension::PREG_MATCH_ARGUMENT_ORGANISER_ID.'.overriden', $definition);

        $definition = new Definition(ScenarioStateArgumentOrganiser::class, array(
            new Reference(ArgumentExtension::PREG_MATCH_ARGUMENT_ORGANISER_ID.'.overriden'),
            new Reference('behatstore.context_initializer.store_aware'),
        ));
        $container->setDefinition(ArgumentExtension::PREG_MATCH_ARGUMENT_ORGANISER_ID, $definition);
    }
}
