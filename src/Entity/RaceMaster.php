<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RaceMasterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use App\DTO\RaceMasterDTO;
use ApiPlatform\Core\Action\NotFoundAction;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RaceMasterRepository::class)
 * @ApiResource(
 *      collectionOperations={
 *          "get",
 *          "importCsv" = {
 *              "method"="post",
 *              "input"=RaceMasterDTO::class,
 *              "deserialize" = false,
 *              "status"=201,
 *              "defaults"={"_api_receive"=true},
 *              "controller"=NotFoundAction::class,
 *              "openapi_context" = {
 *                  "requestBody" = {
 *                      "content" = {
 *                          "multipart/form-data" = {
 *                              "schema" = {
 *                                  "type" = "object",
 *                                      "properties" = {
 *                                          "racetitle" = {
 *                                              "description" = "Race Title",
 *                                              "type" = "string",
 *                                             },
 *                                          "racedate" = {
 *                                              "description" = "Race Date",
 *                                              "type" = "string",
 *                                             },
 *                                          "imagefile" = {
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
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "write"})
     */
    private $raceTitle;

    /**
     * @ORM\Column(type="date")
     * @Groups({"read", "write"})
     */
    private $raceDate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read"})
     */
    private $imageName;

    /**
     * @Groups({"write"})
     * @Assert\File(
     *     maxSize="2M",
     * )
     */
    private $imageFile;

    /**
     * @ORM\OneToMany(targetEntity=RaceDetails::class, mappedBy="RaceMaster")
     */
    private $raceDetails;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $avgTimeMediumDistance;

    /**
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

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
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
