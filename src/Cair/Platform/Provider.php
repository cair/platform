<?php

namespace Cair\Platform;

use Cair\Platform\Database\DynamicModel;
use Cair\Platform\Database\RedisCommand;
use Predis\Client;

class Provider {

	/**
	 * The configuration.
	 *
	 * @var array
	 */
	protected static $config;

	protected $instances;

	/**
	 * Get a connection for a collection.
	 *
	 * @param string  $resource
	 * @return Cair\Platform\Database\ConnectionInterface
	 */
	public function in($resource)
	{
		if(isset(static::$config['resources'][$resource]))
		{
			if ( ! isset($this->instances[$resource]))
			{
				$connection = new RedisCommand(new Client);

				$this->instances[$resource] = (new DynamicModel($resource))->setConnection($connection);
			}

			return $this->instances[$resource];
		}
	}

	/**
	 * Register a method call listener.
	 *
	 * @param string  $method
	 * @param array   $arguments
	 */
	public function __call($method, $arguments = [])
	{
		return $this->in($method);
	}

	public static function setConfig($config)
	{
		static::$config = $config;
	}

	public static function getConfig()
	{
		return static::$config;
	}

}