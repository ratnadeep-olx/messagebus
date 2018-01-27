<?php
namespace messagebus;
use Aws\Common\Aws;
class Producer {
    private $streamName;
    private $totalNumberOfRecords = 10000;
    function __construct($streamName) {
        $this->streamName = $streamName;
        $aws = Aws::factory('../config/config.php');
        $this->kinesisClient = $aws->get('Kinesis');
    }

    public function publish($data, $partitionKey) {
        return $this->kinesisClient->putRecord(array(
            'StreamName' => $this->streamName,
            'Data' => json_encode($data),
            'PartitionKey' => $data[$partitionKey],
        ));
    }
}