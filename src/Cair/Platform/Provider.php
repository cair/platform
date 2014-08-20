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
	protected $structure;

	protected $connection;

	public function __construct($structure, $parameters)
	{
		$this->structure = $structure;

		$this->connection = new RedisCommand(new Client($parameters));
	}

	/**
	 * Get a connection for a collection.
	 *
	 * @param string  $resource
	 * @return Cair\Platform\Database\ConnectionInterface
	 */
	public function in($resource)
	{
		if(isset(static::$structure[$resource]))
		{
			return (new DynamicModel($resource))->setConnection($this->connection);
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

	public function setStructure($structure)
	{
		$this->$structure = $structure;
	}

	public function getStructure()
	{
		return $this->$structure;
	}

}