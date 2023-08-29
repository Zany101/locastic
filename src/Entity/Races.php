<?php

namespace App\Entity;

use ArrayObject;
use Model\RequestBody;
use App\State\RaceProvider;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use App\Controller\ImportCsvAction;
use App\Repository\RacesRepository;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use PHPUnit\Framework\MockObject\Rule\Parameters;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: RacesRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            provider: RaceProvider::class,
            normalizationContext: [
                'groups' => ['races:read'],
            ],
        ),
        new Post(
            denormalizationContext: [
                'groups' => ['races:put']
            ],
            controller: ImportCsvAction::class,
            deserialize: false,
            validationContext: ['groups' => ['Default']],
            openapi: new Model\Operation(
                parameters: [
                    [
                        'name' => 'title',
                        'description' => 'Race title',
                        'required' => 'true',
                        'type' => 'string',
                        'format' => 'binary'
                    ],
                    [
                        'name' => 'date',
                        'description' => 'Race date',
                        'required' => 'true',
                        'type' => 'date',
                        'format' => 'binary'
                    ]
                ],
                requestBody: new Model\RequestBody(
                    content: new ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'required' => 'true',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ])
                )
            )
        )
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'title' => 'exact',
    ]
)]
#[ApiFilter(OrderFilter::class, properties: ['title', 'date', 'avgTimeMediumDistance', 'avgTimeLongDistance'], arguments: ['orderParameterName' => 'orderBy'])]
class Races
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[SerializedName("Race title")]
    #[Groups(['races:read', 'races:put'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[SerializedName("Race date")]
    #[Groups(['races:read', 'races:put'])]
    private ?string $date = null;

    #[SerializedName("Average finish time for medium distance")]
    #[Groups(['races:read'])]
    #[ORM\Column(length: 255)]
    private ?string $averageFinishTimeForMediumDistance = "1";

    #[SerializedName("Average finish time for long distance")]
    #[Groups(['races:read'])]
    #[ORM\Column(length: 255)]
    private ?string $averageFinishTimeForLongDistance = "1";

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'races', targetEntity: Participants::class, cascade: ['persist'])]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getAverageFinishTimeForMediumDistance(): ?string
    {
        return $this->averageFinishTimeForMediumDistance;
    }

    public function setAverageFinishTimeForMediumDistance(string $averageFinishTimeForMediumDistance): static
    {
        $this->averageFinishTimeForMediumDistance = $averageFinishTimeForMediumDistance;

        return $this;
    }

    public function getAverageFinishTimeForLongDistance(): ?string
    {
        return $this->averageFinishTimeForLongDistance;
    }

    public function setAverageFinishTimeForLongDistance(string $averageFinishTimeForLongDistance): static
    {
        $this->averageFinishTimeForLongDistance = $averageFinishTimeForLongDistance;

        return $this;
    }

    /**
     * @return Collection<int, Participants>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participants $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setRaces($this);
        }

        return $this;
    }

    public function addParticipants(array $participants): static
    {
        foreach ($participants as $participant) {
            $this->addParticipant($participant);
        }

        return $this;
    }

    public function removeParticipant(Participants $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getRaces() === $this) {
                $participant->setRaces(null);
            }
        }

        return $this;
    }
}
