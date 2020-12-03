<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package\Redis;

class Str
{
    /**
     * @access private
     * @var object $Connect 数据库链接对象
    */
    private object $Connect;

    /**
     * @access public
     * @return void
     * @param object $connect redis主类链接信息
     */
    public function __construct(object $connect)
    {
        $this->Connect = $connect;
    }

    /**
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return mixed
     * @context 创建元素对象值内容
     */
    public function create(string $key, $value)
    {
        $_receipt = $this->Connect->set($key,$value);
        if(strtolower($_receipt) === "ok")
            $_receipt = true;
        else
            $_receipt = false;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @param int $second 生命周期时间（second）
     * @return boolean
     * @context 创建元素对象，并设置生命周期
     */
    public function createSec(string $key, $value, int $second=0)
    {
        $_receipt = $this->Connect->setex($key,$value,intval($second));
        if(strtolower($_receipt) === "ok")
            $_receipt = true;
        else
            $_receipt = false;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return int
     * @context 非覆盖创建元素对象值
     */
    public function createOnly(string $key, $value)
    {
        return $this->Connect->setnx($key,$value);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @param int $milli 生命周期时间（milli）
     * @return boolean
     * @context 创建元素对象，并设置生命周期
     */
    public function createMil(string $key, $value, int $milli=0)
    {
        $_receipt = $this->Connect->psetex($key,$value,intval($milli));
        if(strtolower($_receipt) === "ok")
            $_receipt = true;
        else
            $_receipt = false;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key
     * @return mixed
     * @context 获取内容
     */
    public function get(string $key)
    {
        $_receipt = $this->Connect->get($key);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return int
     * @context 叠加（创建）对象元素值内容
     */
    public function append(string $key, $value)
    {
        return $this->Connect->append($key,$value);
    }

    /**
     * @access public
     * @param string $key 被创建对象键名
     * @param int $value 被创建元素对象内容值
     * @param int $offset 偏移系数
     * @return int
     * @context 设置元素对象偏移值
     */
    public function cBit(string $key, int $value, int $offset)
    {
        return $this->Connect->setBit($key,$value,$offset);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $value 被创建元素对象内容值
     * @return int
     * @context 获取元素对象偏移值
     */
    public function gBit(string $key, int $value)
    {
        return $this->Connect->getBit($key,$value);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return int
     * @context 检索元素对象值内容长度
     */
    public function getLen(string $key)
    {
        return $this->Connect->strlen($key);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $start 起始位置参数
     * @param int $end 结束位置参数
     * @return object
     * @context 检索元素对象值（区间截取）内容，（大于0的整数从左开始执行，小于0的整数从右开始执行）
     */
    public function getRange(string $key, int $start=1, int $end=-1)
    {
        $_receipt = $this->Connect->getRange($key,$start,$end);
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return mixed
     * @context 替换原有值内容，并返回原有值内容
     */
    public function getRollback(string $key, $value)
    {
        $_receipt = $this->Connect->getSet($key,$value);
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param array $columns 对应元素列表数组
     * @return boolean
     * @context 创建元素列表
     */
    public function createList(array $columns)
    {
        $_receipt = $this->Connect->mset($columns);
        if(strtolower($_receipt) === "ok")
            $_receipt = true;
        else
            $_receipt = false;
        return $_receipt;
    }

    /**
     * @access public
     * @param array $columns 对应元素列表数组
     * @return int
     * @context 非替换创建元素列表
     */
    public function createListOnly(array $columns)
    {
        return $this->Connect->msetnx($columns);
    }

    /**
     * @access public
     * @param array $keys 对应元素列表数组
     * @return mixed
     * @context 检索元素列表
     */
    public function getList(array $keys)
    {
        $_receipt = $this->Connect->mget($keys);
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $increment 自增系数值
     * @return mixed
     * @context 对应元素（数据）指定值自增
     */
    public function plus(string $key, int $increment=1)
    {
        # 判断系数条件是否为大于的参数值
        if(intval($increment) > 1){
            if(is_int($increment))
                # 执行自定义递增操作
                $_receipt = $this->Connect->incrBy($key,intval($increment));
            else
                # 执行自定义递增(float,double)操作
                $_receipt = $this->Connect->incrByFloat($key,floatval($increment));
        }else
            # 执行递增1操作
            $_receipt = $this->Connect->incr($key);
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $decrement 自减系数值
     * @return mixed
     * @context 对应元素（数据）指定值自减
     */
    public function minus(string $key, int $decrement=1)
    {
        # 判断系数条件是否为大于的参数值
        if(intval($decrement) > 1)
            # 执行自定义递减操作
            $_receipt = $this->Connect->decrBy($key,intval($decrement));
        else
            # 执行递减1操作
            $_receipt = $this->Connect->decr($key);
        return $_receipt;
    }
}