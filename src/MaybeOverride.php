<?php
/**
 * MaybeOverride.php
 *
 * Allows the customization of code that would otherwise be unable to be modified without being possibly overwritten,
 * by specifying an overrides directory outside of the projects codebase, where customized classes can be used instead,
 * using a directory structure that emulates the namespace beyond the base namespace specified.
 */
namespace Tsquare\ClassOverrider;

/**
 * Class ClassOverrider
 *
 * Either Instantiate the provided class, or an overriding class if one exists.
 *
 * @package Tsquare\ClassOverrider
 */
class MaybeOverride {
	/**
	 * @var $path string The path to the overrides directory.
	 */
	private $path;

	/**
	 * @var $class string The class for which to decide instantiation.
	 */
	private $class;

	/**
	 * MaybeOverride constructor.
	 *
	 * @param  string $class    The fully qualified class name.
	 * @param  mixed  ...$args  Optional arguments to pass to the class constructor.
	 */
	public function __construct( $class, ...$args )
	{
		$this->confirmConstants();

		$this->validatePath();

		$this->class = $class;

		$override = $this->checkForOverride();

		if($override !== false) {
			include $override;

			$this->overrideClassNamespace();
		}

		$this->instantiate(...$args);
	}

	/**
	 * Confirm the existence of the necessary constants.
	 */
	private function confirmConstants()
	{
		if(!defined('CLASSOVERRIDER_NS')) {
			throw new \UnexpectedValueException('Base namespace CLASSOVERRIDER_NS is not defined.');
		}

		if(!defined('CLASSOVERRIDER_BASE_NS')) {
			throw new \UnexpectedValueException('Base namespace CLASSOVERRIDER_BASE_NS is not defined.');
		}

		if(!defined('CLASSOVERRIDER_PATH')) {
			throw new \UnexpectedValueException('Overrides directory CLASSOVERRIDER_PATH is not defined.');
		}

		if(!is_dir(CLASSOVERRIDER_PATH)) {
			throw new \UnexpectedValueException('Overrides directory \'' . CLASSOVERRIDER_PATH . '\' does not exist.');
		}
	}

	/**
	 * Make sure the overrides path ends with a /.
	 */
	private function validatePath()
	{
		$this->path = CLASSOVERRIDER_PATH;
		if( strpos(strrev($this->path), '/') !== 0 ) {
			$this->path .= '/';
		}
	}

	/**
	 * Check if a file exists in overrides matching the class to be instantiated.
	 *
	 * @return bool|string
	 */
	private function checkForOverride()
	{
		$overrideFilePath = $this->getOverridePath() . '.php';
		if(file_exists($overrideFilePath)) {
			return $overrideFilePath;
		}

		return false;
	}

	/**
	 * Get the path to the overrides base location.
	 *
	 * @return mixed
	 */
	private function getOverridePath()
	{
		return str_replace([CLASSOVERRIDER_BASE_NS, '\\'], [CLASSOVERRIDER_PATH, '/'], $this->class);
	}

	/**
	 * Replace the base namespace with the overrides namespace.
	 */
	private function overrideClassNamespace()
	{
		$this->class = str_replace(CLASSOVERRIDER_BASE_NS, CLASSOVERRIDER_NS, $this->class);
	}

	/**
	 * Instantiate the class with any optional arguments.
	 *
	 * @param  mixed  ...$args
	 * @return mixed
	 */
	public function instantiate(...$args)
	{
		return new $this->class(...$args);
	}
}
