<?php

namespace Cair\Platform\Database;

use Predis\Client;

class RedisCommand implements Command {

	protected $client;

	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	public function get($resource)
	{
		$records = [];

		foreach ($this->client->lrange($resource, 0, -1) as $id)
		{
			$records[] = $this->find($resource, $id);
		}

		return $records ?: [];
	}

	public function find($resource, $id)
	{
		$key = $resource . ':' . $id . ':check';

		$this->client->lpush($key, $this->client->lrange($resource, 0, -1));

        $found = $this->client->lrem($key, -1, $id) !== 0;

        $this->client->del($key);

        if ($found)
        {
        	$attributes = $this->client->hgetall($resource.':'.$id);

        	$attributes['id'] = $id;

        	return $attributes;
        }
	}

	public function create($resource, $attributes)
	{
		$id = $this->client->incr($resource.':id');

		$this->client->rpush($resource, $id);

		$this->client->hmset($resource.':'.$id, $attributes);

		return $id;
	}

	public function update($resource, $id, $attributes)
	{
		$this->client->hmset($resource.':'.$id, $attributes);
	}

	public function delete($resource, $id)
	{
		$this->client->lrem($resource, 0, $id);
	}

}