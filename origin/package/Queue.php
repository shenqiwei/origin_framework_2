<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context Origin队列功能封装
 */
namespace Origin\Package;

class Queue
{
    /**
     * @access public
     * @static
     * @param string $queue 创建队列名称
     * @return boolean
     * @context 创建任务队列目录
     */
    public static function make(string $queue)
    {
        # 创建返回值变量
        $_receipt = false;
        if(!file_exists($_queue = replace(RESOURCE_PUBLIC."/queue/{$queue}"))){
            $_dir = new Folder(replace(RESOURCE_PUBLIC."/queue"));
            $_receipt = $_dir->create($queue,true);
            if($_receipt){
                $_file = new File($_queue);
                $_receipt = $_file->create("origin_queue.tmp",true);
                $_file->write("origin_queue.tmp","w",json_encode(array("list"=>null,"create_time"=>time())));
            }
        }
        return $_receipt;
    }

    /**
     * @access public
     * @static
     * @param string $queue 队列名称
     * @return int|false
     * @context 获取当前队列任务数量
     */
    public static function count(string $queue)
    {
        if(file_exists($_queue = replace(RESOURCE_PUBLIC."/queue/{$queue}"))){
           if(is_file($_tmp = replace("{$_queue}/origin_queue.tmp"))){
               $_file = new File($_queue);
               $_string = $_file->read("origin_queue.tmp");
               $_array = json_decode($_string,true);
               return count($_array["list"]);
           }else
               return false;
        }else
            return false;
    }

    /**
     * @access public
     * @static
     * @param string $queue 队列名称
     * @param array $set 参数集合
     * @return boolean
     * @context 插入队列
     */
    public static function push(string $queue, array $set)
    {
        $_receipt = false;
        if(file_exists($_queue = replace(RESOURCE_PUBLIC."/queue/{$queue}"))){
            if(is_file($_tmp = replace("{$_queue}/origin_queue.tmp"))){
                $_file = new File($_queue);
                $_string = $_file->read("origin_queue.tmp");
                $_array = json_decode($_string,true);
                if(is_array($set)){
                    $_string = json_encode($set);
                    $_files = sha1($_string)."tmp";
                    if($_file->create($_files)){
                        if($_file->write($_files,"w",$_string)){
                            array_push($_array["list"],array("tmp"=>$_files));
                            $_file->write("origin_queue.tmp","w",json_encode($_array));
                            $_receipt = true;
                        }
                    }
                }
            }
        }
        return $_receipt;
    }

    /**
     * @access public
     * @static
     * @param string $queue 队列名称
     * @return array|boolean
     * @context 抽取第一个任务信息
     */
    public static function extract(string $queue)
    {
        $_receipt = false;
        if(file_exists($_queue = replace(RESOURCE_PUBLIC."/queue/{$queue}"))){
            if(is_file($_tmp = replace("{$_queue}/origin_queue.tmp"))){
                $_file = new File($_queue);
                $_string = $_file->read("origin_queue.tmp");
                $_array = json_decode($_string,true);
                $_set = array_shift($_array["list"]);
                $_tmp = $_set["tmp"];
                if(is_file($_queue.DS.$_tmp)){
                    $_receipt = $_file->read($_queue.DS.$_tmp);
                    unlink($_queue.DS.$_tmp);
                }
            }
        }
        return $_receipt;
    }

    /**
     * @access public
     * @static
     * @param string $queue 队列名称
     * @return boolean
     * @context 清空队列
     */
    public static function clear(string $queue)
    {
        # 创建返回值变量
        $_receipt = false;
        if(!file_exists($_queue = replace(RESOURCE_PUBLIC."/queue/{$queue}"))){
            $_dir = new Folder(replace(RESOURCE_PUBLIC."/queue"));
            $_list = $_dir->get($queue);
            if($_count = count($_list)){
                for($_i = 0;$_i < $_count;$_i++){
                    if(is_file($_list[$_i]["folder_uri"]))
                        unlink($_list[$_i]["folder_uri"]);
                }
            }
            $_receipt = $_dir->remove($_queue);
        }
        return $_receipt;
    }
}
