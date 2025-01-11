<?php

namespace rbwebdesigns\core;

use ReflectionClass;

/**
 * Service container.
 * 
 * Get services without having to new up classes each time.
 * This is a basic mapping between a name and a class name we dynamically
 * fetch the list of parameters a class constructor has.
 */
class ServiceContainer
{
    public function __construct(protected array $services) {}

    /**
     * Register a single service.
     */
    public function registerService(string $name, string $className) {
        $this->services[$name] = [
            'class' => $className
        ];
    }

    /**
     * Get a service instance by it's name.
     */
    public function get(string $serviceName): object {
        if (! key_exists($serviceName, $this->services)) {
            throw new \Exception("Unable to load service: $serviceName");
        }

        if (! key_exists('instance', $this->services[$serviceName])) {
           $this->initialiseService($serviceName);
        }

        return $this->services[$serviceName]['instance'];
    }

    /**
     * Get a service by it's class name.
     */
    public function getClass(string $className) {
        $arguments = $this->getArguments($className);

        return new $className(...$arguments);
    }

    protected function getServiceName(string $className) {
        $map = array_combine(
            array_column($this->services, 'class'),
            array_keys($this->services),
        );

        if (! isset($map[$className])) {
            throw new \Exception("Unable to find service with class: $className");
        }

        return $map[$className];
    }

    protected function initialiseService(string $serviceName) {
        $service = $this->services[$serviceName];

        if (! class_exists($service['class'])) {
            throw new \Exception("Unable to create service: Class {$service['class']} does not exist.");
        }

        $args = $this->getArguments($service['class']);

        $this->services[$serviceName]['instance'] = new $service['class'](...$args);
    }

    protected function getArguments(string $class) {
        $reflectionClass = new ReflectionClass($class);

        $parameters = $reflectionClass->getConstructor()?->getParameters();

        if (! is_array($parameters)) {
            return [];
        }

        $arguments = [];

        foreach ($parameters as $parameter) {
            $type = (string) $parameter->getType();

            if (class_exists($type)) {
                $parameterServiceName = $this->getServiceName($type);
                $arguments[] = $this->get($parameterServiceName);
                continue;
            }
        }

        return $arguments;
    }
}