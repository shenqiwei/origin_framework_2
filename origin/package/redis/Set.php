<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package\Redis;

class Set
{
    /**
     * @access private
     * @var object $Connect 数据库链接对象
     */
    private object $Connect;

    /**
     * @access public
     * @param object $connect redis主类链接信息
     */
    function __construct(object $connect)
    {
        $this->Connect = $connect;
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 存入值
     * @return int
     * @context 集合：向集合添加一个或多个成员
     */
    function add(string $key, $value)
    {
        return $this->Connect->sAdd($key,$value);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @return int
     * @context 获取集合内元素数量
     */
    function count(string $key)
    {
        return $this->Connect->sCard($key);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param string $second 比对元素对象键
     * @return mixed
     * @context 获取两集合差值
     */
    function diff(string $key, string $second)
    {
        $_receipt = $this->Connect->sDiff($key,$second);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 索引元素对象
     * @param string $second 比对元素对象键
     * @param string $new 新集合元素对象
     * @return int
     * @context 获取两集合之间的差值，并存入新集合中
     */
    function different(string $key, string $second, string $new)
    {
        return $this->Connect->sDiffStore($new,$key,$second);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param string $value 验证值
     * @return int
     * @context 判断集合元素对象值是否存在元素对象中
     */
    function member(string $key, string $value)
    {
        return $this->Connect->sIsMember($key,$value);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @return mixed
     * @context 返回元素对象集合内容
     */
    function reSet(string $key)
    {
        $_receipt = $this->Connect->sMembers($key);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @param string $key 索引元素对象键
     * @param string $second 迁移集合对象
     * @param mixed $value 迁移值
     * @return int
     * @context 元素对象集合值迁移至其他集合中
     */
    function move(string $key, string $second, $value)
    {
        return $this->Connect->sMove($key,$second,$value);
    }

    /**
     * @access public
     * @param string $key 索引元素对象
     * @return mixed
     * @context 移除元素对象随机内容值
     */
    function pop(string $key)
    {
        $_receipt = $this->Connect->sPop($key);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param int $count 随机抽调数量
     * @return mixed
     * @context 随机从元素对象中抽取指定数量元素内容值
     */
    function randMember(string $key, int $count=1)
    {
        if($count > 1)
            $_receipt = $this->Connect->sRandMember($key);
        else
            $_receipt = $this->Connect->sRandMember($key,$count);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 移除值
     * @return int
     * @context 移除元素对象中指定元素内容
     */
    function remove(string $key, $value)
    {
        return $this->Connect->sRem($key,$value);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param string $second 索引元素对象键
     * @return mixed
     * @context 返回指定两个集合对象的并集
     */
    function merge(string $key, string $second)
    {
        $_receipt = $this->Connect->sUnion($key,$second);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $new 存储指向集合键
     * @param string $key 索引元素对象键
     * @param string $second 索引元素对象键
     * @return int
     * @context 返回指定两个集合对象的并集
     */
    function mergeTo(string $new, string $key, string $second)
    {
        return $this->Connect->sUnionStore($new,$key,$second);
    }

    /**
     * @access public
     * @param string $key 索引元素对象
     * @param string $value 索引值
     * @param int $cursor 执行标尺
     * @param string $pattern 操作参数
     * @return mixed
     * @context 迭代元素对象指定结构内容
     */
    function tree(string $key, string $value, int $cursor=0, string $pattern="match")
    {
        $_receipt = $this->Connect->sScan($key,$cursor,$pattern,$value);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
}