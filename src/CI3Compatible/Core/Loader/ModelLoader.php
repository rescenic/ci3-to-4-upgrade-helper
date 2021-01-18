<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Core\Loader;

use Kenjis\CI3Compatible\Core\Loader\ClassResolver\ModelResolver;

use function is_array;
use function is_int;

class ModelLoader
{
    /** @var ModelResolver */
    private $modelResolver;

    /** @var ControllerPropertyInjector */
    private $injector;

    public function __construct(ControllerPropertyInjector $injector)
    {
        $this->injector = $injector;

        $this->modelResolver = new ModelResolver();
    }

    /**
     * @param mixed $model
     * @param bool  $db_conn @TODO not implemented
     */
    public function load($model, string $name = '', bool $db_conn = false): void
    {
        if (empty($model)) {
            return;
        }

        if (is_array($model)) {
            $this->loadModels($model, $db_conn);

            return;
        }

        $this->loadModel($model, $name, $db_conn);
    }

    private function loadModel(string $model, string $name, bool $db_conn): void
    {
        $classname = $this->modelResolver->resolve($model);
        $property = $this->getPropertyName($model, $name);
        $instance = $this->createInstance($classname);

        $this->injector->inject($property, $instance);
    }

    private function loadModels(array $models, bool $db_conn): void
    {
        foreach ($models as $key => $value) {
            is_int($key)
                ? $this->load($value, '', $db_conn)
                : $this->load($key, $value, $db_conn);
        }
    }

    private function getPropertyName(string $model, string $name): string
    {
        if ($name === '') {
            return $model;
        }

        return $name;
    }

    private function createInstance(string $classname): object
    {
        return model($classname);
    }
}
