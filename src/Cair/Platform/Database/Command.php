<?php

namespace Cair\Platform\Database;

interface Command {

	public function get($resource);

	public function find($resource, $id);

	public function create($resource, $attributes);

	public function update($resource, $id, $attributes);

	public function delete($resource, $id);

}