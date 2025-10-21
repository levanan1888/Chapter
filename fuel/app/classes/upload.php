<?php

/**
 * Custom Upload Class để fix PHP 8+ compatibility
 * 
 * @package    App
 * @subpackage Classes
 */
class Upload extends \Fuel\Core\Upload
{
	/**
	 * Override offsetExists để tương thích PHP 8+
	 * 
	 * @param mixed $offset
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists($offset)
	{
		return parent::offsetExists($offset);
	}

	/**
	 * Override offsetGet để tương thích PHP 8+
	 * 
	 * @param mixed $offset
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($offset)
	{
		return parent::offsetGet($offset);
	}

	/**
	 * Override offsetSet để tương thích PHP 8+
	 * 
	 * @param mixed $offset
	 * @param mixed $value
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet($offset, $value)
	{
		parent::offsetSet($offset, $value);
	}

	/**
	 * Override offsetUnset để tương thích PHP 8+
	 * 
	 * @param mixed $offset
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset($offset)
	{
		parent::offsetUnset($offset);
	}
}
