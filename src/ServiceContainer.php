<?php

namespace rbwebdesigns\core;

class ServiceContainer
{
    /**
     * @var array<string,array<string,mixed>>
     */
    protected $services = [
        'database' => [
            'class' => ObjectDatabase::class,
            'args' => [],
        ],
    ];

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

    protected function initialiseService(string $serviceName) {
        $service = $this->services[$serviceName];
        $args = [];

        if (! class_exists($service['class'])) {
            throw new \Exception("Unable to create service: Class {$service['class']} does not exist.");
        }

        foreach ($service['args'] as $argument) {
            $args[] = $this->get($argument);
        }

        $this->services[$serviceName]['instance'] = new $service['class'](...$args);
    }
}