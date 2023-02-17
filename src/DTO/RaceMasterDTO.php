<?php
namespace App\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class RaceMasterDTO
{
    /**
     * @Groups({"read", "write"})
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    public $raceTitle;

    /**
     * @Groups({"read", "write"})
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    public $raceDate;

    /**
     * @Groups({"write"})
     * @Assert\Image(
     *     mimeTypes={"text/csv"},
     * )
     */
    public $imageFile;

    // public function __construct(string $RaceTitle, string $RaceDate, ?UploadedFile $imageFile = null)
    // {
    //     $this->racetitle = $RaceTitle;
    //     $this->racedate = $RaceDate;
    //     $this->imageFile = $imageFile;
    // }
}
