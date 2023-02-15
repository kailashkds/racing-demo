<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RaceDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RaceDetailsRepository::class)
 * @ApiResource()
 */
class RaceDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $full_name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $distance;

    /**
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $age_category;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $overall_placement;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $age_category_placement;

    /**
     * @ORM\ManyToOne(targetEntity=RaceMaster::class, inversedBy="raceDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $racemaster;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(string $full_name): self
    {
        $this->full_name = $full_name;

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
        return $this->age_category;
    }

    public function setAgeCategory(string $age_category): self
    {
        $this->age_category = $age_category;

        return $this;
    }

    public function getOverallPlacement(): ?int
    {
        return $this->overall_placement;
    }

    public function setOverallPlacement(?int $overall_placement): self
    {
        $this->overall_placement = $overall_placement;

        return $this;
    }

    public function getAgeCategoryPlacement(): ?int
    {
        return $this->age_category_placement;
    }

    public function setAgeCategoryPlacement(?int $age_category_placement): self
    {
        $this->age_category_placement = $age_category_placement;

        return $this;
    }

    public function getRacemaster(): ?racemaster
    {
        return $this->racemaster;
    }

    public function setRacemaster(?racemaster $racemaster): self
    {
        $this->racemaster = $racemaster;

        return $this;
    }
}
