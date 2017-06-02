<?php
/**
 * @function  ZMQHander.php
 * @Author: hanlc <hanlc@okooo.net>
 * @Date: 2017/5/18 15:48
 */

namespace App\Ok\Handler;

use App\Ok\Socket\ZMQSocket;
use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\AbstractProcessingHandler;

class ZMQHandler extends AbstractProcessingHandler
{
	/**
	 * @var \ZMQSocket
	 */
	protected $zmqSocket;

	/**
	 * @see http://api.zeromq.org/4-0:zmq-sendmsg
	 * @var int
	 */
	protected $zmqMode = \ZMQ::MODE_DONTWAIT;

	/**
	 * @var boolean
	 */
	protected $multipart = false;

	public $topic ="okooo.logger";

	public function setTopic($name){
		$this->topic = $name;
		return $this;
	}

	public function getTopic(){
		return $this->topic;
	}

	/**
	 * @param \zmqSocket $zmqSocket instance of \ZMQSocket for now only the send types allowed
	 * @param int $zmqMode ZMQ mode
	 * @param boolean $multipart send multipart message
	 * @param int $level
	 * @param bool $bubble Whether the messages that are handled can bubble up the stack or not
	 */
	public function __construct(
		\zmqSocket $zmqSocket,
		$multipart = true,
		$level = Logger::DEBUG,
		$bubble = true
	)
	{
		$zmqSocketType = $zmqSocket->getSocketType();
		if (!$zmqSocketType == \ZMQ::SOCKET_PUB || !$zmqSocketType == \ZMQ::SOCKET_PUSH) {
			throw new \Exception("Invalid socket type used, only PUB, PUSH allowed.");
		}
		$this->zmqSocket = $zmqSocket;
		$isBackground = config("ok.ice.isBackground");
		$zmqMode = ($isBackground == true) ? \ZMQ::MODE_DONTWAIT : \ZMQ::MODE_SNDMORE;
		$this->zmqMode = $zmqMode;
		$this->multipart = $multipart;
		parent::__construct($level, $bubble);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function write(array $record)
	{
		$topic 		= $this->topic;
		$multipart 	= $this->multipart ;
		call_user_func_array(
			array(new ZMQSocket(), "write"),
			array($this->zmqSocket,$record,$this->zmqMode,$topic,$multipart)
		);
		return $record;
	}




}
