<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin浏览器会话
 */
namespace Origin\Package;

class Cookie
{
    /**
     * @access public
     * @context 构造函数
     */
    function __construct()
    {
        if(!ini_get('session.auto_start')) session_start();
    }

    /**
     * @access public
     * @param string $option 设置项
     * @param string $value 会话值
     * @return mixed
     * @context cookie会话设置
     */
    function edit(string $option, string $value)
    {
        $_receipt = null;
        if(is_null($value))
            ini_set('session.'.strtolower($option), $value);
        else
            $_receipt = ini_get('session.'.strtolower($option));
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 会话键名
     * @param mixed $value 值
     * @context 创建会话值内容
     */
    function set(string $key, $value)
    {
        setcookie($key, $value, config('COOKIE_LIFETIME'),  config('COOKIE_PATH'),  config('COOKIE_DOMAIN'));
    }

    /**
     * @access public
     * @param string $key 会话键名
     * @return mixed
     * @context 获取会话值内容
     */
    function get(string $key)
    {
        return $_COOKIE[$key];
    }
    /**
     * @access public
     * @context 析构函数
     */
    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if(!ini_get('session.auto_start')) session_commit();
    }
}