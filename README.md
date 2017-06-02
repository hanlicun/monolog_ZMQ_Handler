# monolog_ZMQ_Handler
laravel monolog ZMQ Handler

��laravel��������config/ok.php
<?php
/**
 * @function  ok.php
 * @Author: hanlc <hanlc@okooo.net>
 * @Date: 2017/5/26 16:52
 */
return [
	"zmq" => [
		"env"        => env("LOG_ENV","file"), //��ѡ�� zmq �� file
		"connection" => env("ZMQ_CONNECTION", "tcp://127.0.0.1:7777"),
		"format" => env("ZMQ_FORMAT", "json"),  //json php string
		"isBackground" => env("ZMQ_IS_BACKGROUND", false),
		"timeout"	   => env("ZMQ_TIMEOUT", 100000),
		"method"	  =>"bind", //$connect  bind
		"channel"	  =>"okooo.logger"
	]
];

��� app/Facades/Oklog.php
��� app/Providers/LogServiceProvider.php

���ʹ�ã�

	public function test(){

		$msg = json_encode([
			"data"=>random_int(10000,90000),
			"aa"=>"bbb",
			"time"=>date("Y-m-d H:i:s U")]
		);
		//app("oklog")->setLogger("logger")->info($msg);
		Oklog::setLogger("okooo.logger.test.test")->debug($msg);
		//���ܵķ��� info  debug  notice warning error alert
		//Oklog::info($msg);
		return $msg;
	}