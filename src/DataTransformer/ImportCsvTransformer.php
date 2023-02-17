<?php
namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\DTO\RaceMasterDTO;

class ImportCsvTransformer implements DataTransformerInterface
{
    public function transform($data, string $to, array $context = [])
    {
        // This is called when data needs to be transformed into the DTO class.
        // We don't need to implement this because we're only deserializing.

        return $data;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === RaceMasterDTO::class;
    }

    public function transformTo(object $data, string $to, array $context = [])
    {
        $racetitle = $data->get('racetitle');
        $racedate = $data->get('racedate');
        // $imagefile = $data->get('imagefile');

        $racemasterDto = new RaceMasterDTO();
        $racemasterDto->raceTitle = $racetitle;
        $racemasterDto->raceDate = $racedate;
        // $racemasterDto->imageFile = $imagefile;
     
        // dump($racemasterDto);
        return $racemasterDto;
    }
}