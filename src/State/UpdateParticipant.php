<?php

namespace App\State;

use App\Service\ImportHandler;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\ParticipantsRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class UpdateParticipant implements ProcessorInterface
{
    private $repository;

    private $em;

    public function __construct(ParticipantsRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        # This should have a NotifyPropertyChanged and check if the time has been changed
        # Recalculate, Incase someone updates the time.
        $race = $data->getRaces();
        $allParticipants = $race->getParticipants();

        if ($data->getDistance() == 'medium') {
            $data->setOverallPlacement(null);
            $data->setAgeCategoryPlacement(null);
        }

        $serializer = new Serializer([new ObjectNormalizer(), new ArrayDenormalizer()]);
        $data = $serializer->normalize(
            $allParticipants,
            null,
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['races']]
        );
        
        # Causes IRI issue
        foreach ($allParticipants as $entity) {
            $this->em->remove($entity);
            $this->em->flush();
        }

        $importHandler = new ImportHandler($data);
        $result = $importHandler->process();


        $race->setAverageFinishTimeForMediumDistance($importHandler->averageFinishTimeForMediumDistance);
        $race->setAverageFinishTimeForLongDistance($importHandler->averageFinishTimeForLongDistance);
        $race->addParticipants($result);



        $this->em->persist($race);
        $this->em->flush();
    }
}
