<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2019
 * @context:
 * Origin单向入口操作文件
 */
# 版本控制
if((float)PHP_VERSION < 5.5) die('this program is support to lowest php version 5.5');
# DIRECTORY_SEPARATOR：PHP内建常量，用来返回当前系统文件夹连接符号LINUX（/）,WINNER（\）
# 路径分割符
if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);
# 系统类型
define("OS",PHP_OS);
# 反路径连接符号
define("RE_DS",(strpos(OS,"WIN") === false)?"\\":"/");
# 主程序文件目录常量
if(!defined('ROOT')) define('ROOT',dirname(__DIR__));
# 封装目录
if(!defined('ORIGIN')) define('ORIGIN',ROOT.DS.'origin'.DS);
# 引述文件根地址
if(!defined("ROOT_ADDRESS")) define("ROOT_ADDRESS",dirname(__FILE__));
# 是否启用编码混成
if(!defined('MARK_RELOAD')) define('MARK_RELOAD',true);
# 协议类型
if(!defined("__PROTOCOL__")) define("__PROTOCOL__", isset($_SERVER["HTTPS"])? "https://" : "http://");
# 地址信息
if(!defined("__HOST__")) define("__HOST__",__PROTOCOL__.$_SERVER["HTTP_HOST"]."/");
# 调试状态常量
if(!defined('DEBUG')) define('DEBUG',false);
# 加载时间常量
if(!defined('TIME')) define('TIME',false);
# 错误信息常量
if(!defined('ERROR')) define('ERROR',false);
# 请求器类型
if(!defined('REQUEST_METHOD')) define('REQUEST_METHOD',strtolower($_SERVER["REQUEST_METHOD"]));
# 默认应用访问地址
if(!defined('DEFAULT_APPLICATION')) define('DEFAULT_APPLICATION','home');
# 资源目录名
if(!defined('DIR_RESOURCE')) define('DIR_RESOURCE','resource');
# 资源目录访问地址
if(!defined('ROOT_RESOURCE')) define('ROOT_RESOURCE',ROOT.DS.DIR_RESOURCE);
# 在线地址
if(!defined('WEB_RESOURCE')) define('WEB_RESOURCE',__HOST__.DIR_RESOURCE);
# 资源公共文件夹
if(!defined('RESOURCE_PUBLIC')) define('RESOURCE_PUBLIC',ROOT_RESOURCE.DS.'public');
# 资源上传文件夹
if(!defined('RESOURCE_UPLOAD')) define('RESOURCE_UPLOAD',ROOT_RESOURCE.DS.'upload');
# 日志主目录
if(!defined('ROOT_LOG')) define('ROOT_LOG','common'.DS.'log'.DS);
# 服务请求链接日志
if(!defined('LOG_ACCESS')) define('LOG_ACCESS',ROOT_LOG.'access'.DS);
# 数据库连接日志
if(!defined('LOG_CONNECT')) define('LOG_CONNECT',ROOT_LOG.'connect'.DS);
# 系统异常信息日志
if(!defined('LOG_EXCEPTION')) define('LOG_EXCEPTION',ROOT_LOG.'error'.DS);
# 系统操作日志
if(!defined('LOG_OPERATE')) define('LOG_OPERATE',ROOT_LOG.'action'.DS);
# 框架初始化日志
if(!defined('LOG_INITIALIZE')) define('LOG_INITIALIZE',ROOT_LOG.'initialize'.DS);
# 默认访问应用类
if(!defined('DEFAULT_CLASS')) define('DEFAULT_CLASS','index');
# 默认访问应用方法
if(!defined('DEFAULT_FUNCTION')) define('DEFAULT_FUNCTION','index');
# 错误信息显示
# E_ALL = 11 所有的错误信息
# E_ERROR = 1 报致命错误
# E_WARNING = 2 报警告错误
# E_NOTICE = 8 报通知警告
# E_ALL& ~E_NOTICE = 3 不报NOTICE错误, 常量参数 TRUE
# 0 不报错误，默认常量参数 FALSE
if(ERROR == true or ERROR == 3)
    error_reporting(E_ALL & ~E_NOTICE);
elseif(ERROR == 11)
    error_reporting(E_ALL);
elseif(ERROR == 1)
    error_reporting(E_ERROR);
elseif(ERROR == 2)
    error_reporting(E_WARNING);
elseif(ERROR == 8)
    error_reporting(E_NOTICE);
else error_reporting(0);
# 引入主方法文件
include('library/common.php');
# 调用加载
include('package/Junction.php');
# 启动加载函数
Origin\Package\Junction::initialize();