<?php

namespace App\Factory;

use App\Entity\Participants;
use App\Repository\ParticipantsRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Participants>
 *
 * @method        Participants|Proxy create(array|callable $attributes = [])
 * @method static Participants|Proxy createOne(array $attributes = [])
 * @method static Participants|Proxy find(object|array|mixed $criteria)
 * @method static Participants|Proxy findOrCreate(array $attributes)
 * @method static Participants|Proxy first(string $sortedField = 'id')
 * @method static Participants|Proxy last(string $sortedField = 'id')
 * @method static Participants|Proxy random(array $attributes = [])
 * @method static Participants|Proxy randomOrCreate(array $attributes = [])
 * @method static ParticipantsRepository|RepositoryProxy repository()
 * @method static Participants[]|Proxy[] all()
 * @method static Participants[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Participants[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Participants[]|Proxy[] findBy(array $attributes)
 * @method static Participants[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Participants[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class ParticipantsFactory extends ModelFactory
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
            'ageCategory' => self::faker()->text(255),
            'distance' => self::faker()->text(255),
            'finishTime' => self::faker()->text(255),
            'fullName' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Participants $participants): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Participants::class;
    }
}
