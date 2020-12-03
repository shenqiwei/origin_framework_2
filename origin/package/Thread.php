<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context Origin单一化事务线程处理功能封装 (该功能尽在控制台条件下生效，php ver 7.2+)
 */
namespace Origin\Package;

use parallel\Runtime;
use parallel\Channel;

abstract class Thread
{
    /**
     * @access protected
     * @var array $Array 预存变量数组
     */
    protected $Array;

    /**
     * @access protected
     * @var object $Thread 执行线程对象
    */
    protected $Thread;

    /**
     * @access public
     * @param string $name 传入值名称
     * @param mixed $value 传入值
     * @return void
     * @context 传入值
     */
    public abstract function set($name,$value);

    /**
     * @access public
     * @return void
     * @context 获取值内容
     */
    public abstract function get($name);

    /**
     * @access public
     * @param string $name 拦截器名称
     * @param string $function 拦截器方法
     * @param array $param 拦截器参数
     * @return void
     * @context 拦截器
     */
    public abstract function filter($name,$function,$param);

    /**
     * @access public
     * @param object $channel 通道对象
     * @return void
     * @context 执行函数，该函数用于封装操作内容
     */
    public abstract function action($channel);

    /**
     * @access public
     * @static
     * @param object $object 执行对象
     * @return mixed
     * @context 线程主执行函数
    */
    public function parallel($object)
    {
        # 创建返回值变量
        $_receipt = null;
        # 声明线程对象
        $_thread = new Runtion;
        # 声明通道对象
        $_channel = new Channel;
        # 执行线程
        $_future = $_thread->run(function()use($object,$_channel){
            $object->action($_channel);
        });
        # 获取执行后内容
        $_receipt = $_channel->recv();
        # 判断线程状态
        if($_future->done)
            $_thread->close();
        return $_receipt;
    }
}