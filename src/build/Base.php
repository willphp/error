<?php
/*--------------------------------------------------------------------------
 | Software: [WillPHP framework]
 | Site: www.113344.com
 |--------------------------------------------------------------------------
 | Author: no-mind <24203741@qq.com>
 | WeChat: www113344
 | Copyright (c) 2020-2022, www.113344.com. All Rights Reserved.
 |-------------------------------------------------------------------------*/
namespace willphp\error\build;
use willphp\config\Config;
use willphp\log\Log;
/**
 * 错误处理
 * Class Base
 * @package willphp\error\build
 */
class Base {	
	protected $debug; //当前debug状态	
	protected $error_tpl; //错误显示模板(关闭debug)
	protected $errors = []; //错误信息列表
	/**
	 * 组件启动
	 */
	public function bootstrap() {
		error_reporting(0);		
		$this->debug = Config::get('app.debug', true);
		$this->error_tpl = Config::get('error.error_tpl', '');	
		if (empty($this->error_tpl)) {
			$this->error_tpl = __DIR__.'/../view/error.php';
		}
		register_shutdown_function([$this, 'fatalError']);
		set_error_handler([$this, 'error'], E_ALL);
		set_exception_handler([$this, 'exception']);
	}
	/**
	 * 获取所有错误
	 */
	public function all() {
		return $this->errors;
	}	
	/**
	 * 自定义错误处理
	 * @param $errno 错误码
	 * @param $error 错误信息
	 * @param $file  文件 
	 * @param $line  行数
	 */	
	public function error($errno, $error, $file, $line) {
		$info = [];
		$info['errno'] = $errno;
		$info['file'] = $file;
		$info['line'] = $line;
		$info['type'] = $this->errorType($errno);
		$info['error'] = $error;		
		$info['msg'] = '['.$errno.']'.$error.'['.$file.':'.$line.']';
		$this->errors[] = '['.$info['type'].']'.$info['msg'];		
		if ($errno == E_NOTICE) {	
			if (PHP_SAPI != 'cli' && $this->debug && Config::get('error.show_notice') && !Config::get('app.trace')) {
				echo '<p style="color:#900">['.$info['type'].'] '.$error.' ['.basename($file).':'.$line.']<p>';	
			}				
		} elseif (!in_array($errno, [E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED])) {
			$this->showError($info);
		}
	}	
	/**
	 * 自定义异常处理
	 * @param $e
	 */
	public function exception($e) {	
		$info = [];
		$info['errno'] = $e->getCode();
		$info['error'] = $e->getMessage();
		$info['file'] = $e->getFile();
		$info['line'] = $e->getLine();
		$info['path'] = $e->__toString();
		$info['type'] = 'EXCEPTION';
		$info['msg'] = '['.$info['errno'].']'.$info['error'].'['.$info['file'].':'.$info['line'].']';	
		$this->errors[] = '['.$info['type'].']'.$info['msg'];
		$this->showError($info);
	}	
	/**
	 * 自定义致命错误处理
	 */	
	public function fatalError() {
		if (function_exists('error_get_last')) {
			$e = error_get_last();			
			if ($e) {
				$info = [];
				$info['error'] = $e['message'];
				$info['file'] = $e['file'];
				$info['line'] = $e['line'];
				$info['errno'] = $e['type'];
				$info['type'] = 'FATAL';
				$this->errors[] = '['.$info['type'].']'.$info['msg'];
				require __DIR__.'/../view/debug.php';
				die;
			}
		}		
	}
	/**
	 * 显示错误
	 * @param array $info 错误信息
	 * @param array $level 错误等级(模板)
	 * @return mixed
	 */
	protected function showError($info = []) {
		if (PHP_SAPI == 'cli') {
			die(PHP_EOL."\033[;36m ".$info['msg']." \x1B[0m\n".PHP_EOL); //命令行错误处理
		}
		$ajax = Config::get('error.error_ajax', ['msg'=>'系统错误，请稍候访问', 'status'=>0]); //ajax数据
		if (!$this->debug) {
			Log::write($info['msg'], $info['type']); //写入日志
			$info['msg'] = $ajax['msg'];
			$error_template = $this->error_tpl; //错误模板			
		} else {
			$ajax['msg'] = $info['msg'];
			$error_template = __DIR__.'/../view/debug.php'; //错误模板
		}
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			header('Content-type: application/json;charset=utf-8');
			$res = json_encode($ajax, JSON_UNESCAPED_UNICODE);			
			echo $res;
		} elseif (file_exists($error_template)) {
			require $error_template;
		} else {
			echo $info['msg'];
		}
		die;
	}
	/**
	 * 输出404页面
	 * @return mixed
	 */
	public function _404($return = false) {
		ob_start();
		include __DIR__.'/../view/404.php';
		$res = ob_get_clean();
		if ($return) {
			return $res;
		} 		
		exit($res);	
	}	
	/**
	 * 输出403页面
	 * @return mixed
	 */
	public function _403($return = false) {
		ob_start();
		include __DIR__.'/../view/403.php';
		$res = ob_get_clean();
		if ($return) {
			return $res;
		}
		exit($res);
	}	
	/**
	 * 获取错误标识
	 * @param $type
	 * @return string
	 */
	public function errorType($type) {
		switch ($type) {
			case E_ERROR: // 1 //
				return 'E_ERROR';
			case E_WARNING: // 2 //
				return 'E_WARNING';
			case E_PARSE: // 4 //
				return 'E_PARSE';
			case E_NOTICE: // 8 //
				return 'E_NOTICE';
			case E_CORE_ERROR: // 16 //
				return 'E_CORE_ERROR';
			case E_CORE_WARNING: // 32 //
				return 'E_CORE_WARNING';
			case E_COMPILE_ERROR: // 64 //
				return 'E_COMPILE_ERROR';
			case E_COMPILE_WARNING: // 128 //
				return 'E_COMPILE_WARNING';
			case E_USER_ERROR: // 256 //
				return 'E_USER_ERROR';
			case E_USER_WARNING: // 512 //
				return 'E_USER_WARNING';
			case E_USER_NOTICE: // 1024 //
				return 'E_USER_NOTICE';
			case E_STRICT: // 2048 //
				return 'E_STRICT';
			case E_RECOVERABLE_ERROR: // 4096 //
				return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED: // 8192 //
				return 'E_DEPRECATED';
			case E_USER_DEPRECATED: // 16384 //
				return 'E_USER_DEPRECATED';
		}		
		return $type;
	}
}