<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Dto\ImportDtoClass;
use App\Entity\RaceMaster;
use App\Message\CsvImport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class RacingController extends AbstractController
{
    private $messageBus;

    public function __construct(
        MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(Request $request, $id)
    {
        // $uploadedFile = $request->files->get('csv')->getContent();
        $uploadedFile = $request->files->get('csv');
        
        if (!$uploadedFile) {
            throw new BadRequestHttpException('CSV File is required');
        }

        if ($uploadedFile->getClientmimeType() !== 'text/csv') {
            return new JsonResponse(['error' => 'The uploaded file is not a CSV file']);
        }

        $contents = file_get_contents($uploadedFile->getRealPath());
        $filePath = $uploadedFile->getRealPath();
        $fileHandle = fopen($filePath, 'r');

        $header = fgetcsv($fileHandle);

        $csvHeader = ["fullName","distance","time","ageCategory"];

        // print_r($header[0]);die;
        if(count($header) !== 4) {
            return new JsonResponse(['error' => 'Column name is not equal to CSV Header']);
        }

        foreach($header as $key=>$value) {
            if (!in_array($value, $csvHeader, true)) {
                return new JsonResponse(['error' => 'CSV Header title is not matched!']);
            }
        }

        $racedata = ['raceMasterId' => $id ];
        while(($data = fgetcsv($fileHandle)) !== false) {
            
            foreach($header as $key=>$value) {
                $racedata[$value] = $data[$key];
            }

            $csvImportMessage = new CsvImport($racedata);
            $this->messageBus->dispatch($csvImportMessage);
        }

        $finalMessage = new CsvImport(['raceMasterId' => $id ]);
        $this->messageBus->dispatch($finalMessage);
    }

}