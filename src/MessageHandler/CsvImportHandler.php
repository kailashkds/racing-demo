<?php
namespace App\MessageHandler;

use App\Message\CsvImport;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler]
class CsvImportHandler implements MessageHandlerInterface
{
    public function __invoke(CsvImport $message)
    {
        // ... do some work - like sending an SMS message!

        print_r($message);

        
    }
}