<?php

namespace App\Factory;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static Todo|Proxy createOne(array $attributes = [])
 * @method static Todo[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static Todo|Proxy find($criteria)
 * @method static Todo|Proxy findOrCreate(array $attributes)
 * @method static Todo|Proxy first(string $sortedField = 'id')
 * @method static Todo|Proxy last(string $sortedField = 'id')
 * @method static Todo|Proxy random(array $attributes = [])
 * @method static Todo|Proxy randomOrCreate(array $attributes = [])
 * @method static Todo[]|Proxy[] all()
 * @method static Todo[]|Proxy[] findBy(array $attributes)
 * @method static Todo[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Todo[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TodoRepository|RepositoryProxy repository()
 * @method Todo|Proxy create($attributes = [])
 */
final class TodoFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
            'name' => self::faker()->text(),
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Todo $todo) {})
        ;
    }

    protected static function getClass(): string
    {
        return Todo::class;
    }
}
