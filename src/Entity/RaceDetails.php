<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RaceDetailsRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\MandatoryFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiFilter(SearchFilter::class, properties={"raceMaster":"exact","fullName": "partial", "distance" :"exact", "ageCategory": "partial"})
 * @ApiFilter(OrderFilter::class, properties={"id": "DESC","fullName": "DESC","distance": "DESC","ageCategory": "DESC","overallPlacement": "DESC","ageCategoryPlacement": "DESC"})
 * @ORM\Entity(repositoryClass=RaceDetailsRepository::class)
 * @ApiResource(
 *     attributes={
 *         "filters"={
 *             MandatoryFilter::class
 *         }
 *     },
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}},
 *     collectionOperations={
 *         "get"
 *      },
 *      itemOperations={
 *          "get",
 *          "put",
 *      },
 *  )
 */
class RaceDetails
{
    /**
     * @Groups({"read"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"read"})
     * @ORM\ManyToOne(targetEntity=RaceMaster::class, inversedBy="raceDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $raceMaster;

    /**
     * @Assert\NotBlank()
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255)
     */
    private $fullName;

    /**
     * @Assert\Choice(choices={"long", "medium"}, multiple=false)
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=50)
     */
    private $distance;

    /**
     * @Assert\Time()
     * @Groups({"read", "write"})
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @Assert\NotBlank()
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=50)
     */
    private $ageCategory;

    /**
     * @Groups({"read"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $overallPlacement;

    /**
     * @Groups({"read"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ageCategoryPlacement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaceMaster(): ?RaceMaster
    {
        return $this->raceMaster;
    }

    public function setRaceMaster(?RaceMaster $raceMaster): self
    {
        $this->raceMaster = $raceMaster;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getDistance(): ?string
    {
        return $this->distance;
    }

    public function setDistance(string $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getAgeCategory(): ?string
    {
        return $this->ageCategory;
    }

    public function setAgeCategory(string $ageCategory): self
    {
        $this->ageCategory = $ageCategory;

        return $this;
    }

    public function getOverallPlacement(): ?int
    {
        return $this->overallPlacement;
    }

    public function setOverallPlacement(?int $overallPlacement): self
    {
        $this->overallPlacement = $overallPlacement;

        return $this;
    }

    public function getAgeCategoryPlacement(): ?int
    {
        return $this->ageCategoryPlacement;
    }

    public function setAgeCategoryPlacement(?int $ageCategoryPlacement): self
    {
        $this->ageCategoryPlacement = $ageCategoryPlacement;

        return $this;
    }
}
