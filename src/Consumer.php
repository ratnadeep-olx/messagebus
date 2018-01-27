<?php
namespace messagebus;
use Aws\Common\Aws;
class Consumer extends AbstractSubject {
    private $observers = array();
    private $streamName;
    private $numberOfRecordsPerBatch = 10000;
    private $shardIterator = array();
    function __construct($streamName) {
        $this->streamName = $streamName;
        $aws = Aws::factory('../config/config.php');
        $this->kinesisClient = $aws->get('Kinesis');
    }
    public function attach(AbstractObserver $observer) {
        $this->observers[] = $observer;
        return $this;
    }
    public function detach(AbstractObserver $observer) {
        foreach($this->observers as $okey => $oval) {
            if ($oval == $observer) {
                unset($this->observers[$okey]);
            }
        }
    }
    protected function notify($record) {
        foreach($this->observers as $obs) {
            $obs->update($record);
        }
    }
    public function init() {
        $res = $this->kinesisClient->describeStream([ 'StreamName' =>$this->streamName ]);
        $shards = $res->toArray()['StreamDescription']['Shards'];
        foreach ($shards as $sh) {
            $shardIds[] = $sh['ShardId'];
        }
        while(true) {
            $this->getData($shardIds);
            sleep(1);
        }
    }
    protected function getData($shardIds) {
        foreach ($shardIds as $shardId) {
            if(empty($this->shardIterator[$shardId])) {
                $res = $this->kinesisClient->getShardIterator([
                    'ShardId' => $shardId,
                    'ShardIteratorType' => 'TRIM_HORIZON',
                    'StreamName' => $this->streamName,
                ]);
                $this->shardIterator[$shardId] = $res->get('ShardIterator');
            }
            do {
                $res = $this->kinesisClient->getRecords([
                    'Limit' => $this->numberOfRecordsPerBatch,
                    'ShardIterator' => $this->shardIterator[$shardId]
                ]);
                $this->shardIterator[$shardId] = $res->get('NextShardIterator');
                $dataAvailable = false;
                if ($res->get('Records')) {
                    $this->notify($res->get('Records'));
                    $dataAvailable = true;
                }
            } while ($dataAvailable);
        }
    }
}