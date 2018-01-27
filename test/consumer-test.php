<?php
require '../vendor/autoload.php';
use messagebus\Consumer;
use messagebus\AbstractObserver;

class ConsumerObserver extends AbstractObserver
{
    public function update($record)
    {
        print_r($record);
    }
}

$consumer = new Consumer('poc');
$consumer->attach(new ConsumerObserver())->init();
?>
