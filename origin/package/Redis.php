<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package;

use Origin\Package\Redis\Key;
use Origin\Package\Redis\Str;
use Origin\Package\Redis\Set;
use Origin\Package\Redis\Hash;
use Origin\Package\Redis\Lists;
use Origin\Package\Redis\Sequence;

class Redis
{
    /**
     * @access protected
     * @var object|null $_Connect 数据库链接对象
    */
    protected ?object $Connect;

    /**
     * @access public
     * @param string|null $connect_name 配置源名称
     * @return void
     * @context 构造函数，预加载数据源配置信息
    */
    public function __construct(?string $connect_name=null)
    {
        $_connect_config = config('DATA_MATRIX_CONFIG');
        if(is_array($_connect_config)){
            for($_i = 0;$_i < count($_connect_config);$_i++){
                # 判断数据库类型
                if(key_exists("DATA_TYPE",$_connect_config[$_i]) and strtolower(trim($_connect_config[$_i]["DATA_TYPE"])) === "redis"
                    and key_exists("DATA_NAME",$_connect_config[$_i]) and $_connect_config[$_i]['DATA_NAME'] === $connect_name){
                    $_connect_conf = $_connect_config[$_i];
                    break;
                }
            }
            if(!isset($_connect_conf)) {
                for ($_i = 0; $_i < count($_connect_config); $_i++) {
                    # 判断数据库对象
                    if (key_exists("DATA_TYPE",$_connect_config[$_i]) and strtolower(trim($_connect_config[$_i]["DATA_TYPE"])) === "redis") {
                        $_connect_conf = $_connect_config[$_i];
                        break;
                    }
                }
            }else
                $_connect_config = $_connect_conf;
            # 创建数据库链接地址，端口，应用数据库信息变量
            $_redis_host = strtolower(trim($_connect_config['DATA_HOST']));
            $_redis_port = intval(strtolower(trim($_connect_config['DATA_PORT'])))?intval(strtolower(trim($_connect_config['DATA_PORT']))):6379;
            $this->Connect = new \Redis();
            if($_connect_config['DATA_P_CONNECT'])
                $this->Connect->pconnect($_redis_host,$_redis_port);
            else
                $this->Connect->connect($_redis_host,$_redis_port);
            if(!is_null($_connect_config['DATA_PWD']) and !empty($_connect_conf['DATA_PWD']))
                $this->Connect->auth($_connect_conf['DATA_PWD']);
        }
    }

    /**
     * @access public
     * @return mixed
     * @context 调用键位功能封装
    */
    public function key()
    {
        return new Key($this->Connect);
    }

    /**
     * @access public
     * @return mixed
     * @context 调用字符串功能封装
     */
    public function string()
    {
        return new Str($this->Connect);
    }

    /**
     * @access public
     * @return mixed
     * @context 调用集合包功能封装
     */
    public function set()
    {
        return new Set($this->Connect);
    }

    /**
     * @access public
     * @return mixed
     * @context 调用哈希表功能封装
     */
    public function hash()
    {
        return new Hash($this->Connect);
    }

    /**
     * @access public
     * @return mixed
     * @context 调用列表功能包封装
     */
    public function lists()
    {
        return new Lists($this->Connect);
    }

    /**
     * @access public
     * @return mixed
     * @context 调用队列表功能函数封装
     */
    public function seq()
    {
        return new Sequence($this->Connect);
    }

    /**
     * @access public
     * @param string $obj 刷新对象 all or db
     * @return bool
     * @context 执行Redis刷新
    */
    public function flush($obj="all")
    {
        if($obj == "db" or $obj == 1){
            $_receipt = $this->Connect->flushDB();
        }else{
            $_receipt = $this->Connect->flushAll();
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param int $db 指定数据库标尺
     * @return bool
     * @context Select 切换到指定的数据库，数据库索引号 index 用数字值指定，以 0 作为起始索引值
    */
    public function selectDB($db)
    {
        return $this->Connect->select($db);
    }

    /**
     * @access public
     * @return int
     * @context 最近一次 Redis 成功将数据保存到磁盘上的时间，以 UNIX 时间戳格式表示
    */
    public function saveTime()
    {
        return $this->Connect->lastSave();
    }

    /**
     * @access public
     * @return array
     * @context 返回redis服务器时间
    */
    public function time()
    {
        return $this->Connect->time();
    }

    /**
     * @access public
     * @return int
     * @context 返回数据库容量使用信息
    */
    public function dbSize()
    {
        return $this->Connect->dbSize();
    }

    /**
     * @access public
     * @return bool
     * @context 异步执行一个 AOF（AppendOnly File） 文件重写操作
    */
    public function bgAOF()
    {
        return $this->Connect->bgrewriteaof();
    }

    /**
     * @access public
     * @return bool
     * @context 异步保存当前数据库的数据到磁盘
    */
    public function bgSave()
    {
        return $this->Connect->bgsave();
    }

    /**
     * @access public
     * @return bool
     * @context 保存当前数据库的数据到磁盘
     */
    public function save()
    {
        return $this->Connect->save();
    }

    /**
     * @access public
     * @return void
     * @context 析构函数，释放连接
    */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        if(!is_null($this->Connect))
            $this->Connect = null;
    }
}