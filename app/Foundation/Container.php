<?php


namespace App\Foundation;


use App\Foundation\Exceptions\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

class Container implements ContainerInterface
{
    private array $instances = [];

    /**
     * @param string $id The key or identifier of the instance to retrieve.
     * @return mixed|object|string|null The resolved instance if found,
     * or null if it doesn't exist in the container.
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function get(string $id): mixed
    {
        if ($this->has($id)) {
            $entry = $this->instances[$id];

            return $entry($this);
        }

        return $this->resolve($id);
    }

    /**
     * Check if the container has an instance with the specified key.
     *
     * @param string $id The key or identifier to check.
     * @return bool True if the instance exists in the container, false otherwise.
     */
    public function has(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    /**
     * Set an instance into the container.
     *
     * @param string $id The key or identifier of the instance.
     * @param mixed $concrete The instance to be stored in the container.
     * @return void
     */
    public function set(string $id, callable $concrete): void
    {
        $this->instances[$id] = $concrete;
    }

    /**
     * Dependency Resolution Logic
     *
     * This method is responsible for resolving dependencies when instantiating a class from the container.
     * It follows these steps:
     *  - Inspects the class that we are trying to get from the container.
     *  - Inspects the constructor of the class.
     *  - Inspects the constructor parameters (dependencies).
     *  - If the constructor parameter is a class, it attempts to resolve that class using the container.
     *
     * @param string $id
     * @return object
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function resolve(string $id): object
    {
        $reflectionClass = new \ReflectionClass($id);

        if ($reflectionClass->isInstantiable() === false) {
            throw new ContainerException("Class '$id' is not instantiable.");
        }

        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return new $id;
        }

        $parameters = $constructor->getParameters();
        if (!$parameters) {
            return new $id;
        }

        $dependencies = array_map(function (\ReflectionParameter $param) use ($id) {
            $name = $param->getName();
            $type = $param->getType();

            if ($type === false) {
                throw new ContainerException(
                    "Failed to resolve class '$id', because param '$name' is missing type hint."
                );
            }

            if ($type instanceof \ReflectionNamedType && $type->isBuiltin() === false) {
                return $this->get($type->getName());
            }

            throw new ContainerException(
                "Failed to resolve class '$id', because invalid param '$param'."
            );
        }, $parameters);

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}