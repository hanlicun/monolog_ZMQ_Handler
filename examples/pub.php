<?php

/*
* Pubsub envelope publisher
* @author Ian Barber <ian(dot)barber(at)gmail(dot)com>
*/

//  Prepare our context and publisher
$context = new \ZMQContext();
$zmqSocket = new \ZMQSocket($context, ZMQ::SOCKET_PUB);
$connection ="tcp://127.0.0.1:7777";
//$zmqSocket->bind($connection);
$timeout = 200000;

//$method = $zmqSocket->isPersistent() ? "bind" : "connect";
$method = "bind";
if (method_exists($zmqSocket, 'getEndpoints')) {
	for ($n = 0; $n < 3; $n++) {
		$endpoints = $zmqSocket->getEndpoints();
		if (!in_array($connection, $endpoints['connect'])) {
			$zmqSocket->$method($connection);
			usleep($timeout);
		} else {
			break;
		}
	}
} else {
	$zmqSocket->$method($connection);
}

//while (true) {

$zmqSocket->send("log-channel", \ZMQ::MODE_SNDMORE);
$zmqSocket->send("We would like to see this");

//}

//  We never get here

