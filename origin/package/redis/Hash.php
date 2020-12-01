<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package\Redis;

class Hash
{
    /**
     * @access private
     * @var object $_Connect 数据库链接对象
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
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return int
     * @context 创建hash元素对象内容
     */
    function create(string $key, string $field, $value)
    {
        return $this->Connect->hSet($key, $field, $value);
    }

    /**
     * @access public
     * @param string $key 创建对象元素键
     * @param array $array 字段数组列表
     * @return mixed
     * @context 获取指定元素内容
     */
    function createList(string $key, array $array)
    {
        return $this->Connect->hMset($key,$array);
    }

    /**
     * @access public
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return int
     * @context 非替换创建hash元素对象内容
     */
    function createNE(string $key, string $field, $value)
    {
        return $this->Connect->hSetNx($key,$field,$value);
    }

    /**
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @return mixed
     * @context 获取hash元素对象内容
     */
    function get(string $key, string $field)
    {
        if($this->Connect->exists($key)) {
            if ($this->Connect->hExists($key, $field)) {
                $_receipt = $this->Connect->hGet($key, $field);
                if ($_receipt === "nil")
                    $_receipt = null;
            }else{
                $_receipt = null;
            }
        }else{
            $_receipt = null;
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 索引对象元素键
     * @return mixed
     * @context 返回hash元素对象列表
     */
    function lists(string $key)
    {
        if($this->Connect->exists($key)) {
            $_receipt = $this->Connect->hGetAll($key);
            if ($_receipt === "nil")
                $_receipt = null;
        }else{
            $_receipt = null;
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 索引对象元素键
     * @param array $fields 字段数组列表
     * @return mixed
     * @context 获取hash元素对象内容
     */
    function getList(string $key, array $fields)
    {
        if($this->Connect->exists($key)) {
            $_receipt = $this->Connect->hMGet($key,$fields);
            if ($_receipt === "nil")
                $_receipt = null;
        }else{
            $_receipt = null;
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 索引对象元素键
     * @param int $start 起始位置标记
     * @param string $pattern 执行模板(搜索模板)
     * @param int $count 显示总数
     * @return mixed
     * @context 获取hash元素对象区间列表内容(用于redis翻页功能)
     */
    function limit(string $key, int $start, string $pattern, int $count)
    {
        $_receipt = $this->Connect->hScan($key,$start,$pattern,$count);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 索引对象元素键
     * @return mixed
     * @context 返回hash元素对象列表
     */
    function values(string $key)
    {
        return $this->Connect->hVals($key);
    }

    /**
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @return int
     * @context 删除元素对象内容
     */
    function del(string $key, string $field)
    {
        return $this->Connect->hDel($key,$field);
    }

    /**
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @param int $value 增量值
     * @return mixed
     * @context 设置hash元素对象增量值
     */
    function plus(string $key, string $field, int $value)
    {
        if (is_float($value)) {
            $_receipt = $this->Connect->hIncrByFloat($key, $field, $value);
        } else {
            $_receipt = $this->Connect->hIncrBy($key, $field, intval($value));
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 索引元素对象键
     * @return mixed
     * @context 获取hash元素对象全部字段名(域)
     */
    function fields(string $key)
    {
        return $this->Connect->hKeys($key);
    }

    /**
     * @access public
     * @param string $key 索引元素对象键s
     * @return int
     * @context 获取hash元素对象字段内容（域）长度
     */
    function len(string $key)
    {
        return $this->Connect->hLen($key);
    }
}