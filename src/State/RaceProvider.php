<?php

namespace App\State;

use App\Service\ApplyExtraFields;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Persistence\ManagerRegistry;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class RaceProvider implements ProviderInterface
{
    private $itemProvider;
    private $managerRegistry;
    private $collectionExtensions;
    private $applyExtraFields;

    public function __construct(
        ProviderInterface $itemProvider,
        ManagerRegistry $managerRegistry,
        #[TaggedIterator('api_platform.doctrine.orm.query_extension.collection')]
        iterable $collectionExtensions,
    ) {
        $this->itemProvider = $itemProvider;
        $this->managerRegistry = $managerRegistry;
        $this->collectionExtensions = $collectionExtensions;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $entityClass = $operation->getClass();

        $manager = $this->managerRegistry->getManagerForClass($entityClass);

        $repository = $manager->getRepository($entityClass);

        if (empty($uriVariables)) {
            $queryBuilder = $repository->findRaces();
        } else {
            $queryBuilder = $repository->findParticipants($uriVariables);
        }

        $queryNameGenerator = new QueryNameGenerator();

        # Injecting query extensions, Not exactly the nicest aproach but it sure is conveniant
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection(
                $queryBuilder,
                $queryNameGenerator,
                $entityClass,
                $operation,
                $context
            );
        }

        $results = $queryBuilder->getQuery()->getResult();

        return $results;
    }
}
