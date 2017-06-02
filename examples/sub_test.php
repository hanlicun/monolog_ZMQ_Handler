<?php
/*
* Pubsub envelope subscriber
* @author Ian Barber <ian(dot)barber(at)gmail(dot)com>
*/

//  Prepare our context and subscriber
$context = new \ZMQContext();
$subscriber = new \ZMQSocket($context, \ZMQ::SOCKET_SUB);
$subscriber->connect("tcp://127.0.0.1:7777");
$subscriber->setSockOpt(\ZMQ::SOCKOPT_SUBSCRIBE, "logger");

while (true) {
    //  Read envelope with address
    $address = $subscriber->recv();
    //  Read message contents
    $contents = $subscriber->recv();
    printf ("[%s] %s%s", $address, $contents, PHP_EOL);
}
//  We never get here



