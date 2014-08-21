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
	 * The indicator wheter the model exists.
	 *
	 * @var bool
	 */
	protected $exists = false;

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

		if($attributes = $command->find($this->resource, $id))
		{
			$this->exists = true;

			$this->primaryKey = $id;

			$this->setAttributes($attributes);

			return $this;
		}

		throw new \RuntimeException('Model not found.');
	}

	public function create($attributes)
	{
		$command = $this->newCommand();

		$this->primaryKey = $command->create($this->resource, $attributes);

		$this->exists = true;

		$this->attributes = $attributes;

		return $this;
	}

	public function update($attributes)
	{
		// If we did not fetch or create the model earlier, we just assume, the
		// a new model should be created. We pass the parameters and return a
		// freshly created model.
		if ( ! $this->exists)
		{
			return $this->create($attributes);
		}

		$command = $this->newCommand();

		$result = $command->update($this->resource, $this->primaryKey, $attributes);

		if($result)
		{
			$this->mergeAttributes($attributes);
		}

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
	 * Get the attributes.
	 *
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * Merge with the existing attributes.
	 *
	 * @param array   $attributes
	 * @return self
	 */
	public function mergeAttributes($attributes)
	{
		$newAttributes = array_merge($this->getAttributes(), $attributes);

		$this->setAttributes($newAttributes);

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
