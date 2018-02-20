
<?php
require '../vendor/autoload.php';

use \messagebus\Producer;

$data = [
    'user_id' => rand(1,5),
    'msg_id' => rand(1, 100),
    'ad_id' => rand(1,500),
    'text' => 'Here goes the text - '.rand(1,1000)
];

$producer = new Producer('rdkinesis');
print_r($producer->publish($data, 'user_id'));
print_r($data);
?>
