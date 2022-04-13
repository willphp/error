<?php
/*--------------------------------------------------------------------------
 | Software: [WillPHP framework]
 | Site: www.113344.com
 |--------------------------------------------------------------------------
 | Author: no-mind <24203741@qq.com>
 | WeChat: www113344
 | Copyright (c) 2020-2022, www.113344.com. All Rights Reserved.
 |-------------------------------------------------------------------------*/
namespace willphp\error;
use willphp\framework\build\Provider;
class ErrorProvider extends Provider {
	public $defer = false;	
	public function boot() {		
		Error::bootstrap();
	}
	public function register() {
		$this->app->single('Error', function () {
			return Error::single();
		});
	}
}