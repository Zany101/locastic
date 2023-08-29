<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\State\RaceProvider;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ParticipantsRepository;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\State\UpdateParticipant;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: ParticipantsRepository::class)]
#[ApiResource(
    normalizationContext: [
        'groups' => ['participant:read'],
    ],
    denormalizationContext: [
        'groups' => ['participant:put']
    ],
    operations: [
        new GetCollection(
            uriTemplate: "races/{id}",
            uriVariables: [
                'id' =>  new Link(
                    fromClass: Races::class,
                    identifiers: [
                        'id'
                    ]
                )
            ],
            provider: RaceProvider::class,
        ),
        new Put(
            processor: UpdateParticipant::class
        ),
        new Patch()
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'fullName' => 'exact',
    'distance' => 'exact',
    'ageCategory' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['fullName', 'distance', 'ageCategory', 'ageCategoryPlacement', 'overallPlacement'], arguments: ['orderParameterName' => 'orderBy'])]
class Participants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['participant:read', 'participant:put'])]
    private ?string $fullName = null;

    #[Groups(['participant:read', 'participant:put'])]
    #[ORM\Column(length: 255)]
    private ?string $finishTime = null;

    #[Groups(['participant:read', 'participant:put'])]
    #[ORM\Column(length: 255)]
    #[Assert\Choice(['medium', 'long'])]
    private ?string $distance = null;

    #[Groups(['participant:read', 'participant:put'])]
    #[ORM\Column(length: 255)]
    private ?string $ageCategory = null;

    #[Groups(['participant:read'])]
    #[ORM\Column(nullable: true)]
    private int|null $overallPlacement = null;

    #[Groups(['participant:read'])]
    #[ORM\Column(nullable: true)]
    private int|null $ageCategoryPlacement = null;

    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'participants')]
    private ?Races $races = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getFinishTime(): ?string
    {
        return $this->finishTime;
    }

    public function setFinishTime(string $finishTime): static
    {
        $this->finishTime = $finishTime;

        return $this;
    }

    public function getDistance(): ?string
    {
        return $this->distance;
    }

    public function setDistance(string $distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    public function getAgeCategory(): ?string
    {
        return $this->ageCategory;
    }

    public function setAgeCategory(string $ageCategory): static
    {
        $this->ageCategory = $ageCategory;

        return $this;
    }

    public function getOverallPlacement(): int|null
    {
        return $this->overallPlacement;
    }

    public function setOverallPlacement(int|null $overallPlacement): static
    {
        $this->overallPlacement = $overallPlacement;

        return $this;
    }

    public function getAgeCategoryPlacement(): int|null
    {
        return $this->ageCategoryPlacement;
    }

    public function setAgeCategoryPlacement(int|null $ageCategoryPlacement): static
    {
        $this->ageCategoryPlacement = $ageCategoryPlacement;

        return $this;
    }

    public function getRaces(): ?Races
    {
        return $this->races;
    }

    public function setRaces(?Races $races): static
    {
        $this->races = $races;

        return $this;
    }
}
