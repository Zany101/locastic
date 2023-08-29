<?php

namespace App\Factory;

use App\Entity\Races;
use App\Repository\RacesRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Races>
 *
 * @method        Races|Proxy create(array|callable $attributes = [])
 * @method static Races|Proxy createOne(array $attributes = [])
 * @method static Races|Proxy find(object|array|mixed $criteria)
 * @method static Races|Proxy findOrCreate(array $attributes)
 * @method static Races|Proxy first(string $sortedField = 'id')
 * @method static Races|Proxy last(string $sortedField = 'id')
 * @method static Races|Proxy random(array $attributes = [])
 * @method static Races|Proxy randomOrCreate(array $attributes = [])
 * @method static RacesRepository|RepositoryProxy repository()
 * @method static Races[]|Proxy[] all()
 * @method static Races[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Races[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Races[]|Proxy[] findBy(array $attributes)
 * @method static Races[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Races[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class RacesFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'averageFinishTimeForLongDistance' => self::faker()->text(255),
            'averageFinishTimeForMediumDistance' => self::faker()->text(255),
            'date' => self::faker()->text(255),
            'title' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Races $races): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Races::class;
    }
}
