<?php
error_reporting(E_ALL ^(E_NOTICE | E_WARNING));
/*四个常量定义*/
define('IN_DISCUZ', true);
define('DISCUZ_ROOT', substr(dirname(__FILE__), 0, -12));
define('DISCUZ_CORE_DEBUG', false);
define('DISCUZ_TABLE_EXTENDABLE', false);
/*设置异常处理函数*/
set_exception_handler(array('core', 'handleException'));//自定义异常处理。

if(DISCUZ_CORE_DEBUG) {
	set_error_handler(array('core', 'handleError'));// 设置一个用户定义的错误处理函数
	register_shutdown_function(array('core', 'handleShutdown'));//定义PHP程序执行完成后执行的函数
}
/*设置类自动引入的处理函数（这里看仔细，很重要）。 __autoload 只是去include_path寻找类文件并加载，我们可以根据自己的需要定义__autoload加载类的规则。 默认文件名和文件中类名保持一致。*/
if(function_exists('spl_autoload_register')) {
	spl_autoload_register(array('core', 'autoload'));
} else {
	function __autoload($class) {
		return core::autoload($class);
	}
}
/*初始化core类*/
C::creatapp();

class core
{
	private static $_tables;
	private static $_imports;
	private static $_app;
	private static $_memory;

	public static function app() {
		return self::$_app;
	}

	public static function creatapp() {
		if(!is_object(self::$_app)) {
			self::$_app = discuz_application::instance();//从上面的自动引入中我们看到这个类的地址是 ./source/class/discdz/discdz_application.php,
		}
		return self::$_app;
	}

	public static function t($name) {
		return self::_make_obj($name, 'table', DISCUZ_TABLE_EXTENDABLE);
	}

	public static function m($name) {
		$args = array();
		if(func_num_args() > 1) {
			$args = func_get_args();
			unset($args[0]);
		}
		return self::_make_obj($name, 'model', true, $args);
	}

	protected static function _make_obj($name, $type, $extendable = false, $p = array()) {
		$pluginid = null;
		if($name[0] === '#') {
			list(, $pluginid, $name) = explode('#', $name);
		}
		$cname = $type.'_'.$name;
		if(!isset(self::$_tables[$cname])) {
			if(!class_exists($cname, false)) {
				self::import(($pluginid ? 'plugin/'.$pluginid : 'class').'/'.$type.'/'.$name);
			}
			if($extendable) {
				self::$_tables[$cname] = new discuz_container();
				switch (count($p)) {
					case 0:	self::$_tables[$cname]->obj = new $cname();break;
					case 1:	self::$_tables[$cname]->obj = new $cname($p[1]);break;
					case 2:	self::$_tables[$cname]->obj = new $cname($p[1], $p[2]);break;
					case 3:	self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3]);break;
					case 4:	self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3], $p[4]);break;
					case 5:	self::$_tables[$cname]->obj = new $cname($p[1], $p[2], $p[3], $p[4], $p[5]);break;
					default: $ref = new ReflectionClass($cname);self::$_tables[$cname]->obj = $ref->newInstanceArgs($p);unset($ref);break;
				}
			} else {
				self::$_tables[$cname] = new $cname();
			}
		}
		return self::$_tables[$cname];
	}

	public static function memory() {
		if(!self::$_memory) {
			self::$_memory = new discuz_memory();
			self::$_memory->init(self::app()->config['memory']);
		}
		return self::$_memory;
	}

	public static function import($name, $folder = '', $force = true) {
		$key = $folder.$name;
		if(!isset(self::$_imports[$key])) {
			$path = DISCUZ_ROOT.'/source/'.$folder;
			//$name中是否存在'/'
			if(strpos($name, '/') !== false) {
				$pre = basename(dirname($name));
				$filename = dirname($name).'/'.$pre.'_'.basename($name).'.php';
			} else {
				$filename = $name.'.php';
			}

			if(is_file($path.'/'.$filename)) {
				include $path.'/'.$filename;
				self::$_imports[$key] = true;

				return true;
			} elseif(!$force) {
				return false;
			} else {
				throw new Exception('Oops! System file lost: '.$filename);
			}
		}
		return true;
	}

	public static function handleException($exception) {
		discuz_error::exception_error($exception);
	}


	public static function handleError($errno, $errstr, $errfile, $errline) {
		if($errno & DISCUZ_CORE_DEBUG) {
			discuz_error::system_error($errstr, false, true, false);
		}
	}

	public static function handleShutdown() {
		if(($error = error_get_last()) && $error['type'] & DISCUZ_CORE_DEBUG) {
			discuz_error::system_error($error['message'], false, true, false);
		}
	}

	public static function autoload($class) {
		$class = strtolower($class);
		if(strpos($class, '_') !== false) {
			list($folder) = explode('_', $class);
			$file = 'class/'.$folder.'/'.substr($class, strlen($folder) + 1);
		} else {
			$file = 'class/'.$class;
		}

		try {

			self::import($file);
			return true;

		} catch (Exception $exc) {

			$trace = $exc->getTrace();
			foreach ($trace as $log) {
				if(empty($log['class']) && $log['function'] == 'class_exists') {
					return false;
				}
			}
			discuz_error::exception_error($exc);
		}
	}

	public static function analysisStart($name){
		$key = 'other';
		if($name[0] === '#') {
			list(, $key, $name) = explode('#', $name);
		}
		if(!isset($_ENV['analysis'])) {
			$_ENV['analysis'] = array();
		}
		if(!isset($_ENV['analysis'][$key])) {
			$_ENV['analysis'][$key] = array();
			$_ENV['analysis'][$key]['sum'] = 0;
		}
		$_ENV['analysis'][$key][$name]['start'] = microtime(TRUE);
		$_ENV['analysis'][$key][$name]['start_memory_get_usage'] = memory_get_usage();
		$_ENV['analysis'][$key][$name]['start_memory_get_real_usage'] = memory_get_usage(true);
		$_ENV['analysis'][$key][$name]['start_memory_get_peak_usage'] = memory_get_peak_usage();
		$_ENV['analysis'][$key][$name]['start_memory_get_peak_real_usage'] = memory_get_peak_usage(true);
	}

	public static function analysisStop($name) {
		$key = 'other';
		if($name[0] === '#') {
			list(, $key, $name) = explode('#', $name);
		}
		if(isset($_ENV['analysis'][$key][$name]['start'])) {
			$diff = round((microtime(TRUE) - $_ENV['analysis'][$key][$name]['start']) * 1000, 5);
			$_ENV['analysis'][$key][$name]['time'] = $diff;
			$_ENV['analysis'][$key]['sum'] = $_ENV['analysis'][$key]['sum'] + $diff;
			unset($_ENV['analysis'][$key][$name]['start']);
			$_ENV['analysis'][$key][$name]['stop_memory_get_usage'] = memory_get_usage();
			$_ENV['analysis'][$key][$name]['stop_memory_get_real_usage'] = memory_get_usage(true);
			$_ENV['analysis'][$key][$name]['stop_memory_get_peak_usage'] = memory_get_peak_usage();
			$_ENV['analysis'][$key][$name]['stop_memory_get_peak_real_usage'] = memory_get_peak_usage(true);
		}
		return $_ENV['analysis'][$key][$name];
	}
}

class C extends core {}
class DB extends discuz_database {}

?>