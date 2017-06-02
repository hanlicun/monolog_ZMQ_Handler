<?php
/**
 * @function  TestController.php
 * @Author: hanlc <hanlc@okooo.net>
 * @Date: 2017/5/26 11:56
 */

namespace App\Http\Controllers;


use App\Facades\Oklog;
use App\Ok\Handler\StreamHandler;


class TestController
{
	public function test(){

		$msg = json_encode([
			"data"=>random_int(10000,90000),
			"aa"=>"bbb",
			"time"=>date("Y-m-d H:i:s U")]
		);
		//app("oklog")->setLogger("logger")->info($msg);
		Oklog::setLogger("okooo.logger.test.test")->debug($msg);
		//可能的方法 info  debug  notice warning error alert
		//Oklog::info($msg);
		return $msg;
	}

}