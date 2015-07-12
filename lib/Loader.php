<?php

namespace Regen;

use Composer\Autoload\ClassLoader;
use Temping\Temping;

class Loader {
	/**
	 * @var Loader
	 */
	static private $loader;

	/**
	 * @param string[] $prefixes
	 * @param int $target (optional) any of the Regen::TARGET_* constants, if not provided the target will be derived from the php version
	 */
	public static function register($prefixes, $target = 0) {
		if (self::$loader) {
			return;
		}
		if ($target === 0) {
			$target = self::getTarget();
		}
		$regen = new Regen($target);
		if (is_file(__DIR__ . '/../../autoload.php')) { // installed as composer packages
			$composer = require __DIR__ . '/../../autoload.php';
		} else {
			$composer = require __DIR__ . '/../vendor/autoload.php';
		}
		self::$loader = new Loader($regen, $composer);
		foreach ($prefixes as $prefix) {
			self::$loader->addPrefix($prefix);
		}
		// register our loader before the composer one
		spl_autoload_register([self::$loader, 'loadClass'], true, true);
	}

	/**
	 * @return int
	 * @throws \Exception
	 */
	public static function getTarget() {
		if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
			return Regen::TARGET_70;
		}
		if (version_compare(PHP_VERSION, '5.6.0') >= 0) {
			return Regen::TARGET_56;
		}
		if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
			return Regen::TARGET_55;
		}
		if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
			return Regen::TARGET_54;
		}
		throw new \Exception('php versions 5.3 and lower are not supported');
	}

	const EXEC_FILE = 1;
	const EXEC_STRING = 2;

	private $regen;

	/**
	 * @var \Composer\Autoload\ClassLoader
	 */
	private $composerLoader;

	private $prefixes = [];

	/**
	 * @var \Temping\Temping
	 */
	private $temp;

	private $execType;

	public function  __construct(Regen $regen, ClassLoader $composerLoader, $execType = self::EXEC_FILE) {
		$this->regen = $regen;
		$this->composerLoader = $composerLoader;
		$this->execType = $execType;
		if ($execType == self::EXEC_FILE) {
			$this->temp = new Temping();
		}
	}

	/**
	 * @param string $prefix
	 */
	public function addPrefix($prefix) {
		$prefix = trim($prefix, '\\') . '\\';
		$this->prefixes[] = $prefix;
	}

	private function shouldRegen($class) {
		foreach ($this->prefixes as $prefix) {
			if (substr($class, 0, strlen($prefix)) === $prefix) {
				return true;
			}
		}
		return false;
	}

	public function loadClass($class) {
		$class = trim($class, '\\');
		if (!$this->shouldRegen($class) || class_exists($class, false)) {
			return false;
		}
		$file = $this->composerLoader->findFile($class);
		if ($file) {
			$this->loadFile($file);
			return true;
		} else {
			return false;
		}
	}

	public function loadFile($file) {
		$code = file_get_contents($file);
		$code = $this->regen->regen($code);
		if ($this->execType == self::EXEC_FILE) {
			$this->temp->create($file, $code);
			require $this->temp->getPathname($file);
		} else {
			eval('?>' . $code);
		}
	}
}
