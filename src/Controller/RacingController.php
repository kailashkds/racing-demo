<?php
namespace App\Controller;

use App\Entity\RaceMaster;
use App\Dto\ImportDtoClass;
use App\Message\CsvImport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class RacingController extends AbstractController
{
    /**
     * @Route("/racing/index", name="racing")
     */
    public function index(MessageBusInterface $bus):Response
    {
        
        $bus->dispatch(new CsvImport('Look! I created a message!'));

        return new Response(
            ''
        );
    }

    
    // public function import(Request $request)
    // {
    //     $uploadedFile = $request->files->get('file');
    //     $racetitle = $request->get('racetitle');
    //     $racedate = $request->get('racedate');
        
    //     if (!$uploadedFile) {
    //         throw new BadRequestHttpException('"file" is required');
    //     }

    //     $racemaster = new RaceMaster();
        
        

    //     $contents = file_get_contents($uploadedFile->getRealPath());

        
       
    //     print_r($contents);
    //     print_r($racetitle);
    //     print_r($racedate);
    //     die;
    // }

    /**
     * @Route("/import_csv", name="import_csv", methods={"POST"})
     */
    public function import(ImportDtoClass $result): Response
    {
       
        dump($result);
        die;
    }
}