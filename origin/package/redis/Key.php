<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package\Redis;

class Key
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
     * @param string $key 被检索对象键名
     * @return bool
     * @context 删除元素对象内容
     */
    public function del(string $key)
    {
        if($this->Connect->exists($key)){
            $_receipt = $this->Connect->del($key);
        }else{
            $_receipt = false;
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return mixed
     * @context 序列化元素对象内容
     */
    public function dump(string $key)
    {
        if($this->Connect->exists($key)){
            $_receipt = $this->Connect->dump($key);
            if($_receipt === "nil")
                $_receipt = null;
        }else{
            $_receipt = null;
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $timestamp 时间戳
     * @return bool
     * @context 使用时间戳设置元素对象生命周期
     */
    public function setTSC(string $key, int $timestamp)
    {
        return $this->Connect->expireAt($key,$timestamp);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $second 时间戳
     * @return bool
     * @context 使用秒计时单位设置元素对象生命周期
     */
    public function setSec(string $key, int $second)
    {
        return $this->Connect->expire($key,$second);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $timestamp 时间戳
     * @return bool
     * @context 使用毫秒时间戳设置元素对象生命周期
     */
    public function setTSM(string $key, int $timestamp)
    {
        return $this->Connect->pExpireAt($key,$timestamp);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param int $millisecond 时间戳
     * @return bool
     * @context 使用毫秒计时单位设置元素对象生命周期
     */
    public function setMil(string $key, int $millisecond)
    {
        return $this->Connect->pExpire($key,$millisecond);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return bool
     * @context 移除元素目标生命周期限制
     */
    public function rmCycle(string $key)
    {
        return $this->Connect->persist($key);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return int
     * @context 获取元素对象剩余周期时间(毫秒)
     */
    public function remaining(string $key)
    {
        return $this->Connect->pttl($key);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return int
     * @context 获取元素对象剩余周期时间(秒)
     */
    public function remain(string $key)
    {
        return $this->Connect->ttl($key);
    }

    /**
     * @access public
     * @param string $closeKey 相近元素对象（key*）
     * @return mixed
     * @context 获取搜索相近元素对象键
     */
    public function keys(string $closeKey)
    {
        $_receipt = $this->Connect->keys($closeKey);
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * @access public
     * @return mixed
     * @context 随机返回元素键
     */
    public function randKey()
    {
        $_receipt = $this->Connect->randomKey();
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return bool
     * @context 重命名元素对象
     */
    public function rnKey(string $key, string $newKey)
    {
        if($this->Connect->exists($key)){
            $_receipt = $this->Connect->rename($key, $newKey);
        }else{
            $_receipt = false;
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return int
     * @context 非重名元素对象重命名
     */
    public function irnKey(string $key, string $newKey)
    {
        return $this->Connect->renameNx($key, $newKey);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @return string
     * @context 获取元素对象内容数据类型
     */
    public function type(string $key)
    {
        return $this->Connect->type($key);
    }

    /**
     * @access public
     * @param string $key 被检索对象键名
     * @param string $database 对象数据库名
     * @return int
     * @context 将元素对象存入数据库
     */
    public function inDB(string $key, string $database)
    {
        return $this->Connect->move($key, $database);
    }
}