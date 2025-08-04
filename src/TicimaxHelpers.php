<?php

namespace AlperRagib\Ticimax;

class TicimaxHelpers
{

	public function checkRequestParams($this_class, $request_params)
	{
		$missing_params = [];
		foreach ($request_params as $method_name) {
			$new_method_name = 'get_' . $method_name;
			$old_method_name = 'set_' . $method_name;

			if (method_exists($this_class, $new_method_name)) {
				if (is_null($this_class->$new_method_name())) {
					$missing_params[] = $old_method_name;
				} else if (is_array($this_class->$new_method_name()) and $this_class->$new_method_name() == null) {
					$missing_params[] = $old_method_name;
				} else if (empty($this_class->$new_method_name())) {
					$missing_params[] = $old_method_name;
				}
			}
		}

		if (!empty($missing_params)) {
			$missing_params_string = implode(', ', $missing_params);
			trigger_error("The following required parameters are missing. Please provide them: " . $missing_params_string, E_USER_WARNING);
			return false;
		}

		return true;
	}

	/**
	 * Convert object to associative array recursively
	 * @param mixed $object Object to convert
	 * @return array|mixed
	 */
	public static function objectToArray($object)
	{
		if (is_object($object)) {
			$object = get_object_vars($object);
		}
		
		if (is_array($object)) {
			return array_map([self::class, 'objectToArray'], $object);
		}
		
		return $object;
	}


}
