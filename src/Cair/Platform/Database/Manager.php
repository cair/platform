<?php

namespace Cair\Platform\Database;

use Predis\Client;

class Manager {

	protected $commands = [];

	/**
	 *
	 *
	 * @param string  $connection
	 */
	public function resolve($connection, $resource)
	{
		$method = 'resolve' . ucfirst($connection);

		return call_user_func([$this, $method]);
	}

	protected function resolveRedis()
	{
		if ( ! isset($this->commands['redis']))
		{
			$this->commands['redis'] = new RedisCommand(new Client);
		}

		return $this->commands['redis'];
	}

}