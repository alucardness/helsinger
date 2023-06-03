<?php


namespace App\Foundation;


use App\Foundation\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $instances = [];

    public function get(string $id)
    {
        if ($this->has($id) === false) {
            throw new NotFoundException("Class $id is not bound.");
        }

        $entry = $this->instances[$id];

        return $entry($this);
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    public function set(string $id, callable $concrete): void
    {
        $this->instances[$id] = $concrete;
    }
}