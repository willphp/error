# 错误处理
error组件用于网站错误与异常处理处理

#开始使用

####安装组件
使用 composer 命令进行安装或下载源代码使用(依赖willphp/config组件)。

    composer require willphp/error

> WillPHP 框架已经内置此组件，无需再安装。

####组件启动

    \willphp\error\Error::bootstrap(); //必须先启动才能使用

####错误配置

`config/error.php`配置文件可设置：
	
	'error_tpl' => '', //系统错误自定义页面(关闭debug后)
	//ajax请求时错误后显示的json数据模板
	'error_ajax' => [
			'msg' => '系统错误，请稍候访问', //关闭debug后显示的默认信息
			'code' => 400, 
			'status' => 0, 
			'url' => 'javascript:history.back(-1);'
	],		
	'show_notice' => true, //是否显示提示错误
	

####获取错误

    $errors = Error::all();

####显示404

    Error::_404(false); //参数为true则返回页面html



