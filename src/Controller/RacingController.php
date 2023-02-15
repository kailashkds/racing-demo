<?php
namespace App\Controller;

use App\Message\CsvImport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;


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
}