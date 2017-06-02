<?php
/**
 * @function  Logger.php
 * @Author: hanlc <hanlc@okooo.net>
 * @Date: 2017/5/26 16:28
 */

namespace App\Ok\Log;

use App\Ok\Handler\ZMQHandler;
use App\Ok\Socket\ZMQSocket;

class Logger extends \Monolog\Logger
{

	private static $instance;
	private $fqcn = 'Logger';
	public $messagePrefix = "";
	public $topic = "okooo";
	public $name = "okooo";

	protected static $levels = array(
		self::DEBUG => 'DEBUG',
		self::INFO => 'INFO',
		self::NOTICE => 'NOTICE',
		self::WARNING => 'WARNING',
		self::ERROR => 'ERROR',
		self::CRITICAL => 'CRITICAL',
		self::ALERT => 'ALERT',
		self::EMERGENCY => 'EMERGENCY',
	);

	public function setTopic($topicName)
	{
		$this->topic = $topicName;
		return $this;
	}

	public function getTopic()
	{
		return $this->topic;
	}


	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function apps($name)
	{
		$this->messagePrefix = "APPS {$name} ";
		return $this;
	}

	public static function getLogger($name)
	{
		if (!isset(static::$instance[$name])) {
			$env = config("ok.zmq.env");
			if ($env == "zmq") {
				$handler = new ZMQHandler(ZMQSocket::getInstance());
				$logger = new self($name, [$handler], []);
			} else {
				$logger = new self($name);
			}
			static::$instance[$name] = $logger;
		}
		return static::$instance[$name];
	}

	public function info($message, array $context = array())
	{
		return $this->addRecord(static::INFO, $this->messagePrefix . $message, $context);
	}

	public function warn($message, array $context = array()){
		return $this->addRecord(static::WARNING, $message, $context);
	}

	public function notice($message, array $context = array())
	{
		return $this->addRecord(static::NOTICE, $message, $context);
	}

	public static function format($log)
	{
		$logger = Logger::getLogger("logger");
		$fqcn = $logger->fqcn;
		$loggerName = $logger->name;
		$priority = $log["level_name"];
		$message = $log["message"];
		$dateTime = $log["datetime"];
		$timeStamp = $dateTime->format("Y-m-d H:i:s u");
		$content = $timeStamp . ' [' . $loggerName;
		$content .= '] ' . $priority . ': webtest01 [';
		$content .= $log["channel"] . '] ' . $fqcn . ' ' . $message;
		return $content;
	}

}