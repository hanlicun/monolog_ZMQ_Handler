<?php
/**
 * @function  Oklog.php
 * @Author: hanlc <hanlc@okooo.net>
 * @Date: 2017/5/31 15:39
 */

namespace App\Facades;


use App\Ok\Log\Logger;
use Illuminate\Support\Facades\Facade;

class Oklog extends Facade
{

	protected static function getFacadeAccessor(){
		return Logger::class;
	}


	public static function __callstatic($method,$args){
		return call_user_func_array(array(app("oklog"),$method),$args);
	}
}