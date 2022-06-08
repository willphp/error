##错误处理

error组件用于网站错误与异常处理处理

###安装组件

使用 composer 命令进行安装或下载源代码使用(依赖willphp/config组件)。

    composer require willphp/error

> WillPHP框架已经内置此组件，无需再安装。

###必须常量

	define('APP_DEBUG', true); //是否开启调试
	define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

###组件启动

    \willphp\error\Error::bootstrap(); //必须先启动才能使用

###错误配置

`config/error.php`配置文件可设置：
	
	'msg' => '系统错误，请稍候访问', //关闭debug后错误显示的信息
	'show_notice' => true, //是否显示提示错误
	
###获取错误

    $errors = Error::getError();
