<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context:
 * Origin框架单向入口操作文件
*/
# 设置调试状态
define('DEBUG',true);
# 设置加载时间显示状态
define('TIME',false);
# 设置错误提示
define('ERROR',false);
# 代码重加载
define('MARK_RELOAD',false);
# 默认访问应用目录
define('DEFAULT_APPLICATION','home');
# 默认访问类名称
define('DEFAULT_CLASS','index');
# 默认访问方法名称
define('DEFAULT_FUNCTION','index');
# 调用通道入口文件
include('origin/point.php');