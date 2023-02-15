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
    private $race_title;

    /**
     * @ORM\Column(type="date")
     */
    private $race_date;

    /**
     * @ORM\OneToMany(targetEntity=RaceDetails::class, mappedBy="racemaster")
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
        return $this->race_title;
    }

    public function setRaceTitle(string $race_title): self
    {
        $this->race_title = $race_title;

        return $this;
    }

    public function getRaceDate(): ?\DateTimeInterface
    {
        return $this->race_date;
    }

    public function setRaceDate(\DateTimeInterface $race_date): self
    {
        $this->race_date = $race_date;

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
            $raceDetail->setRacemaster($this);
        }

        return $this;
    }

    public function removeRaceDetail(RaceDetails $raceDetail): self
    {
        if ($this->raceDetails->removeElement($raceDetail)) {
            // set the owning side to null (unless already changed)
            if ($raceDetail->getRacemaster() === $this) {
                $raceDetail->setRacemaster(null);
            }
        }

        return $this;
    }
}
