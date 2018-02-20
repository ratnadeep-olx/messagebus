'use strict';
var Kafka = require('no-kafka');
var producer = new Kafka.Producer({connectionString: 'ec2-34-243-193-22.eu-west-1.compute.amazonaws.com:9092' });

exports.handler = (event, context, callback) => {
//console.log('Received event:', JSON.stringify(event, null, 2));
    event.Records.forEach((record) => {
    		// Kinesis data is base64 encoded so decode here
        const payload = new Buffer(record.kinesis.data, 'base64').toString('ascii');
    return producer.init()
        .then(function(){
            return producer.send({
                topic: 'rdkinesis',
                partition: 0,
                message: {value:payload}
            });
        })
        .then(function (result) {
            console.log('Published data to kafka',payload);
        })
        .catch(function (e) {
            console.log('Error data',e);
        });
});
    callback(null, `Successfully processed ${event.Records.length} records.`);
};
		