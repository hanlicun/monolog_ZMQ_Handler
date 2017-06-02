<?php
/**
 * @function  LogServiceProvider.php
 * @Author: hanlc <hanlc@okooo.net>
 * @Date: 2017/5/26 16:44
 */

namespace App\Providers;

use App\Ok\Log\Logger;
use Illuminate\Support\ServiceProvider;
use App\Ok\Handler\ZMQHandler;
use App\Ok\Log\Writer;
use App\Ok\Socket\ZMQSocket;

class LogServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('oklog', function () {
			if (extension_loaded('zmq')) {
				if (app('config')->get("ok.zmq.env") == "zmq") {
					$this->app->configureMonologUsing(function ($monolog) {
						$monolog->pushHandler(new ZMQHandler(ZMQSocket::getInstance()));
					});
				}
			}
			return $this->createLogger();
		});
	}

	/**
	 * Create the logger.
	 *
	 * @return \Illuminate\Log\Writer
	 */
	public function createLogger()
	{

		$log = new Writer(
			new Logger($this->channel()),
			$this->app['events']
		);
		if ($this->app->hasMonologConfigurator()) {
			call_user_func($this->app->getMonologConfigurator(), $log->getMonolog());
		} else {
			$this->configureHandler($log);
		}
		return $log;
	}

	/**
	 * Get the name of the log "channel".
	 *
	 * @return string
	 */
	protected function channel()
	{
		return $this->app->bound('env') ? $this->app->environment() : 'production';
	}

	/**
	 * Configure the Monolog handlers for the application.
	 *
	 * @param  \Illuminate\Log\Writer $log
	 * @return void
	 */
	protected function configureHandler(Writer $log)
	{
		$this->{'configure' . ucfirst($this->handler()) . 'Handler'}($log);
	}

	/**
	 * Configure the Monolog handlers for the application.
	 *
	 * @param  \Illuminate\Log\Writer $log
	 * @return void
	 */
	protected function configureSingleHandler(Writer $log)
	{
		$log->useFiles(
			$this->app->storagePath() . '/logs/laravel.log',
			$this->logLevel()
		);
	}

	/**
	 * Configure the Monolog handlers for the application.
	 *
	 * @param  \Illuminate\Log\Writer $log
	 * @return void
	 */
	protected function configureDailyHandler(Writer $log)
	{
		$log->useDailyFiles(
			$this->app->storagePath() . '/logs/laravel.log', $this->maxFiles(),
			$this->logLevel()
		);
	}

	/**
	 * Configure the Monolog handlers for the application.
	 *
	 * @param  \Illuminate\Log\Writer $log
	 * @return void
	 */
	protected function configureSyslogHandler(Writer $log)
	{
		$log->useSyslog('laravel', $this->logLevel());
	}

	/**
	 * Configure the Monolog handlers for the application.
	 *
	 * @param  \Illuminate\Log\Writer $log
	 * @return void
	 */
	protected function configureErrorlogHandler(Writer $log)
	{
		$log->useErrorLog($this->logLevel());
	}

	/**
	 * Get the default log handler.
	 *
	 * @return string
	 */
	protected function handler()
	{
		if ($this->app->bound('config')) {
			return $this->app->make('config')->get('app.log');
		}

		return 'single';
	}

	/**
	 * Get the log level for the application.
	 *
	 * @return string
	 */
	protected function logLevel()
	{
		if ($this->app->bound('config')) {
			return $this->app->make('config')->get('app.log_level', 'debug');
		}

		return 'debug';
	}

	/**
	 * Get the maximum number of log files for the application.
	 *
	 * @return int
	 */
	protected function maxFiles()
	{
		if ($this->app->bound('config')) {
			return $this->app->make('config')->get('app.log_max_files', 5);
		}
		return 0;
	}
}
