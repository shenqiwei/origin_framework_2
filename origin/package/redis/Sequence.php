<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package\Redis;

class Sequence
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
     * @param string $key 索引元素对象键
     * @param mixed $param 标记
     * @param mixed $value 存入值
     * @return int
     * @context 序列增加元素对象内容值
     */
    public function add(string $key, $param, $value)
    {
        return $this->Connect->zAdd($key,$param,$value);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @return int
     * @context 返回序列中元素对象内容数
     */
    public function count(string $key)
    {
        return $this->Connect->zCard($key);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min 最小区间数
     * @param string $max 最大区间数
     * @return int
     * @context 序列元素对象中区间值数量
     */
    public function mMCount(string $key, string $min, string $max)
    {
        return $this->Connect->zCount($key,$min,$max);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $increment 自增系数
     * @param mixed $value 值
     * @return mixed
     * @context 序列中元素对象值增加自增系数
     */
    public function ai(string $key, $increment, $value)
    {
        $_receipt = $this->Connect->zIncrBy($key,$increment,$value);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $new 目标序列键
     * @param string $key 索引元素对象键
     * @param mixed $param 索引系数
     * @param string $second 比对索引对象键
     * @return int
     * @context 搜索两个序列指定系数成员内容，并存入新的序列中
     */
    public function different(string $new, string $key, $param, string $second)
    {
        return $this->Connect->zInterStore($new,$key,$param,$second);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min 最小区间系数
     * @param string $max 最大区间系数
     * @return int
     * @context 序列中字典区间值数量
     */
    public function dictCount(string $key, string $min, string $max)
    {
        return $this->Connect->zLexCount($key,$min,$max);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return mixed
     * @context 序列元素对象指定区间内容对象内容
     */
    public function range(string $key, int $min, int $max)
    {
        return $this->Connect->zRange($key,$min,$max);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min
     * @param string $max
     * @return mixed
     * @context 序列元素对象指定字典区间内容
     */
    public function dictRange(string $key, string $min, string $max)
    {
        return $this->Connect->zRangeByLex($key,$min,$max);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return mixed
     * @context 序列元素对象指定分数区间内容
     */
    public function limitRange(string $key, int $min, int $max)
    {
        return $this->Connect->zRangeByScore($key,$min,$max);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 索引值
     * @return mixed
     * @context 返回有序集合中指定成员的索引
     */
    public function index(string $key, $value)
    {
        $_receipt = $this->Connect->zRank($key,$value);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 移除值
     * @return int
     * @context 移除有序集合中的一个成员
     */
    public function remove(string $key, $value)
    {
        return $this->Connect->zRem($key,$value);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return int
     * @context 移除有序集合中给定的字典区间的所有成员
     */
    public function dictRemove(string $key, string $start, string $end)
    {
        return $this->Connect->zRemRangeByLex($key,$start,$end);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return int
     * @context 移除有序集中，指定排名(rank)区间内的所有成员
     */
    public function dictRank(string $key, string $start, string $end)
    {
        return $this->Connect->zRemRangeByRank($key,$start,$end);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return int
     * @context 移除有序集中，指定分数（score）区间内的所有成员
     */
    public function dictScore(string $key, int $min, int $max)
    {
        return $this->Connect->zRemRangeByScore($key,$min,$max);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return mixed
     * @context 返回有序集中，指定区间内的成员
     */
    public function descRange(string $key, string $start, string $end)
    {
        $_receipt = $this->Connect->zRevRange($key,$start,$end);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 索引值
     * @return string
     * @context 返回有序集中，成员的分数值
     */
    public function score(string $key, $value)
    {
        $_receipt = $this->Connect->zScore($key,$value);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
}