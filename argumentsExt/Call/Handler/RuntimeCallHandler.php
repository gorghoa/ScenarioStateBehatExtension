<?php

/*
 * This file is part of the StepArgumentInjectorBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\StepArgumentInjectorBehatExtension\Call\Handler;

use Behat\Behat\Transformation\Call\TransformationCall;
use Behat\Testwork\Call\Call;
use Behat\Testwork\Call\Handler\CallHandler;
use Behat\Testwork\Environment\Call\EnvironmentCall;
use Behat\Testwork\Hook\Call\HookCall;
use Gorghoa\StepArgumentInjectorBehatExtension\Resolver\ArgumentsResolver;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
final class RuntimeCallHandler implements CallHandler
{
    /**
     * @var CallHandler
     */
    private $decorated;

    /**
     * @var ArgumentsResolver
     */
    private $argumentsResolver;

    /**
     * @param CallHandler       $decorated
     * @param ArgumentsResolver $argumentsResolver
     */
    public function __construct(CallHandler $decorated, ArgumentsResolver $argumentsResolver)
    {
        $this->decorated = $decorated;
        $this->argumentsResolver = $argumentsResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsCall(Call $call)
    {
        return $this->decorated->supportsCall($call);
    }

    /**
     * {@inheritdoc}
     */
    public function handleCall(Call $call)
    {
        /** @var \ReflectionMethod $function */
        $function = $call->getCallee()->getReflection();
        $arguments = $call->getArguments();

        if ($call instanceof HookCall) {
            $scope = $call->getScope();

            // Manage `scope` argument
            foreach ($function->getParameters() as $parameter) {
                if (null !== $parameter->getClass() && get_class($scope) === $parameter->getClass()->getName()) {
                    $arguments[$parameter->getName()] = $scope;
                    break;
                }
            }
        }

        $arguments = $this->argumentsResolver->resolve($function, $arguments);

        if ($call instanceof TransformationCall) {
            $call = new TransformationCall($call->getEnvironment(), $call->getDefinition(), $call->getCallee(), $arguments);
        } elseif ($call instanceof HookCall) {
            $call = new EnvironmentCall($call->getScope()->getEnvironment(), $call->getCallee(), $arguments);
        }

        return $this->decorated->handleCall($call);
    }
}
