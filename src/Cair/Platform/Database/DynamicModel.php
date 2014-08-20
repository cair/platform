<?php

namespace Cair\Platform\Database;

use Cair\Platform\Database\Command;

class DynamicModel {

	/**
	 * The model resource namespace.
	 *
	 * @var string
	 */
	protected $resource;

	/**
	 * The currently loaded attributes.
	 *
	 * @var array
	 */
	protected $attributes;

	/**
	 * The command connection.
	 *
	 * @var Cair\Platform\Database\connection
	 */
	protected static $connection;

	/**
	 * The model's primary key.
	 *
	 * @var integer
	 */
	protected $primaryKey;

	/**
	 * Create a new dynamic model instance.
	 *
	 * @param string  $resource
	 * @param array   $resource
	 */
	public function __construct($resource, $attributes = [])
	{
		$this->resource = $resource;

		$this->setAttributes($attributes);
	}

	public function get()
	{
		$command = $this->newCommand();

		$results = $command->get($this->resource);

		$models = [];

		foreach ($results as $result)
		{
			$models[] = new self($this->resource, $result);
		}

		return $models;
	}

	public function find($id)
	{
		$command = $this->newCommand();

		if($this->setAttributes($command->find($this->resource, $id)))
		{
			$this->primaryKey = $id;

			return $this;
		}

		throw new \RuntimeException('Model not found.');
	}

	public function create($attributes)
	{
		$command = $this->newCommand();

		$this->primaryKey = $command->create($this->resource, $attributes);

		$this->attributes = $attributes;

		return $this;
	}

	public function update($attributes)
	{
		$command = $this->newCommand();

		$command->update($this->resource, $this->primaryKey, $attributes);

		$this->setAttributes(array_merge($this->attributes, $attributes));

		return $this;
	}

	public function delete()
	{
		$command = $this->newCommand();

		$command->delete($this->resource, $this->primaryKey);
	}

	public function toArray()
	{
		$attributes = $this->attributes;

		$attributes['id'] = $this->primaryKey;
		
		return $attributes;
	}


	public function toJson()
	{
		return json_encode($this->toArray());
	}

	/**
	 * Create a new command.
	 *
	 * @return Cair\Platform\Database\Command
	 */
	public function newCommand()
	{
		return static::$connection;
	}

	/**
	 * Get the model resource namespace.
	 *
	 * @return string
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * Get the command connection.
	 *
	 * @return string
	 */
	public static function getConnection()
	{
		return static::$connection;
	}

	/**
	 * Get the command connection.
	 *
	 * @param Cair\Platform\Database\connection
	 * @return Cair\Platform\Database\DynamicModel
	 */
	public static function setConnection(Command $connection)
	{
		static::$connection = $connection;
	}

	/**
	 * Set the attributes.
	 *
	 * @param array   $primaryKey
	 * @return Cair\Platform\Database\DynamicModel
	 */
	public function setAttributes($attributes)
	{
		if (isset($attributes['id']))
		{
			$this->primaryKey = $attributes['id'];

			unset($attributes['id']);
		}

		$this->attributes = $attributes;

		return $this;
	}

	/**
	 * Set an attribute via property.
	 *
	 * @param string  $attribute
	 * @param mixed   $value
	 * @return void
	 */
	public function __set($attribute, $value)
	{
		$this->attributes[$attribute] = $value;
	}

	/**
	 * Get an attribute via property.
	 *
	 * @param string  $attribute
	 * @return mixed
	 */
	public function __get($attribute)
	{
		return $this->attributes[$attribute];
	}

	/**
	 * Check if an attribute is set.
	 *
	 * @param string  $attribute
	 * @return bool
	 */
	public function __isset($attribute)
	{
		return isset($this->attributes[$attribute]);
	}

	/**
	 * Unset an attribute via property.
	 *
	 * @param string  $attribute
	 * @return void
	 */
	public function __unset($attribute)
	{
		unset($this->attributes[$attribute]);
	}

}
