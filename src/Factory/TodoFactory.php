<?php

namespace App\Factory;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

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
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected static function getClass(): string
    {
        return Todo::class;
    }

    protected function getDefaults(): array
    {
        $users = $this->userRepository->findAll();
        return [
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
            'name' => self::faker()->text(maxNbChars: 100),
            'author' => self::faker()->randomElement($users),
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this// ->afterInstantiate(function(Todo $todo) {})
            ;
    }
}
