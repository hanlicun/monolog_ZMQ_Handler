<?php

namespace App\Ok\Socket;
/**
 * @function  ZMQSocket.php
 * @Author    : hanlc <hanlc@okooo.net>
 * @Date      : 2017/5/23 15:35
 */
class ZMQSocket
{

	private static $instance;

	public static function getInstance(
		$type = \ZMQ::SOCKET_PUB,
		$persistent_id = "logger",
		$on_new_socket = null
	)
	{
		$key = md5($type . $persistent_id . $on_new_socket);
		if (!isset(static::$instance[$key])) {
			$context = new \ZMQContext();
			$persistent_id = config("ok.zmq.channel");
			$zmqSocket = new \ZMQSocket($context, \ZMQ::SOCKET_PUB,
				$persistent_id);
			static::$instance[$key] = $zmqSocket;
		}
		return static::$instance[$key];
	}

	public function write(
		$zmqSocket,
		array $record,
		$zmqMode=\ZMQ::MODE_DONTWAIT,
		$topic = "logger",
		$multipart
	)
	{
		$connection = config("ok.zmq.connection");
		$timeout = config("ok.zmq.timeout");
		$method  = config("ok.zmq.method");
		$msg = $this->formatLog($record);
		if (method_exists($zmqSocket, 'getEndpoints')) {
			for ($n = 0; $n < 10; $n++) {
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
		if ($multipart) {
			$zmqSocket->send($topic, $zmqMode);
			$zmqSocket->send($msg);
		} else {
			$zmqSocket->send($msg, $zmqMode);
		}
		if($method=="bind"){
			//$zmqSocket->unbind($connection);
		}
		return $record;
	}

	public function getPubMethod()
	{
		return PHP_VERSION_ID > 70000 ? "bind" : "connect";
	}

	public function formatLog($msg)
	{
		return \App\Ok\Log\Logger::format($msg);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter()
	{
		return new JsonFormatter();
	}

}