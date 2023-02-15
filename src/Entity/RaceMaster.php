<?php

namespace App\Entity;

use App\Repository\RaceMasterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RaceMasterRepository::class)
 */
class RaceMaster
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
    private $RaceTitle;

    /**
     * @ORM\Column(type="date")
     */
    private $RaceDate;

    /**
     * @ORM\OneToMany(targetEntity=RaceDetails::class, mappedBy="RaceMaster")
     */
    private $raceDetails;

    public function __construct()
    {
        $this->raceDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaceTitle(): ?string
    {
        return $this->RaceTitle;
    }

    public function setRaceTitle(string $RaceTitle): self
    {
        $this->RaceTitle = $RaceTitle;

        return $this;
    }

    public function getRaceDate(): ?\DateTimeInterface
    {
        return $this->RaceDate;
    }

    public function setRaceDate(\DateTimeInterface $RaceDate): self
    {
        $this->RaceDate = $RaceDate;

        return $this;
    }

    /**
     * @return Collection<int, RaceDetails>
     */
    public function getRaceDetails(): Collection
    {
        return $this->raceDetails;
    }

    public function addRaceDetail(RaceDetails $raceDetail): self
    {
        if (!$this->raceDetails->contains($raceDetail)) {
            $this->raceDetails[] = $raceDetail;
            $raceDetail->setRaceMaster($this);
        }

        return $this;
    }

    public function removeRaceDetail(RaceDetails $raceDetail): self
    {
        if ($this->raceDetails->removeElement($raceDetail)) {
            // set the owning side to null (unless already changed)
            if ($raceDetail->getRaceMaster() === $this) {
                $raceDetail->setRaceMaster(null);
            }
        }

        return $this;
    }
}
