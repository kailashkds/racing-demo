<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\RaceMasterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\RacingController;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ApiFilter(OrderFilter::class, properties={"id": "DESC","raceTitle": "DESC","raceDate": "DESC","avgTimeMediumDistance": "DESC","avgTimeLongDistance": "DESC"})
 * @ApiFilter(SearchFilter::class, properties={"raceTitle": "partial"})
 * @ORM\Entity(repositoryClass=RaceMasterRepository::class)
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}},
 *     collectionOperations={
 *          "get",
 *          "post"
 *     },
 *      itemOperations={
 *          "get",
 *          "put" = {
 *              "method"="put",
 *              "path"= "race_masters/{id}/importcsv",
 *              "controller"=RacingController::class,
 *              "deserialize" = false,
 *              "status"=201,
 *              "openapi_context" = {
 *                  "requestBody" = {
 *                      "content" = {
 *                          "multipart/form-data" = {
 *                              "schema" = {
 *                                  "type" = "object",
 *                                      "properties" = {
 *                                          
 *                                          "csv" = {
 *                                              "type" = "string",
 *                                              "format" = "binary",
 *                                              "description" = "Upload a CSV File",
 *                                            },
 *                                       },
 *                                 },
 *                          },
 *                      },
 *                  },
 *              },
 *          },
 *      },
 * )
 */
class RaceMaster
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "write"})
     */
    private $raceTitle;

    /**
     * @Assert\Date()
     * @ORM\Column(type="date")
     * @Groups({"read", "write"})
     */
    private $raceDate;


    /**
     * @ORM\OneToMany(targetEntity=RaceDetails::class, mappedBy="RaceMaster")
     */
    private $raceDetails;

    /**
     * @Groups({"read"})
     * @ORM\Column(type="time", nullable=true)
     */
    private $avgTimeMediumDistance;

    /**
     * @Groups({"read"})
     * @ORM\Column(type="time", nullable=true)
     */
    private $avgTimeLongDistance;

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
        return $this->raceTitle;
    }

    public function setRaceTitle(string $raceTitle): self
    {
        $this->raceTitle = $raceTitle;

        return $this;
    }

    public function getRaceDate(): ?\DateTimeInterface
    {
        return $this->raceDate;
    }

    public function setRaceDate(\DateTimeInterface $raceDate): self
    {
        $this->raceDate = $raceDate;

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

    public function getAvgTimeMediumDistance(): ?\DateTimeInterface
    {
        return $this->avgTimeMediumDistance;
    }

    public function setAvgTimeMediumDistance(?\DateTimeInterface $avgTimeMediumDistance): self
    {
        $this->avgTimeMediumDistance = $avgTimeMediumDistance;

        return $this;
    }

    public function getAvgTimeLongDistance(): ?\DateTimeInterface
    {
        return $this->avgTimeLongDistance;
    }

    public function setAvgTimeLongDistance(?\DateTimeInterface $avgTimeLongDistance): self
    {
        $this->avgTimeLongDistance = $avgTimeLongDistance;

        return $this;
    }
}
