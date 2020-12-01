<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2017
 */
/**
 * @access public
 * @param string $folder 日志路径
 * @param string $context 日志模板
 * @return  boolean
 * @content 日志写入
 */
function _log(string $folder, string $context)
{
    $_receipt = false;
    logWrite:
    # 使用写入方式进行日志创建创建和写入
    $_handle = fopen(ROOT.DS.replace($folder),"a");
    if($_handle){
        # 执行写入操作，并返回操作回执
        $_receipt = fwrite($_handle,$context);
        # 关闭文件源
        fclose($_handle);
    }else{
        if(!file_exists(ROOT.DS.replace($folder))){
            $_dir = explode(DS,$folder);
            $_new = null;
            for($_i = 0;$_i < count($_dir)-1;$_i++){
                $_new .= DS.$_dir[$_i];
                if(!is_dir(ROOT.DS.$_new)){
                    mkdir(ROOT.DS.$_new,0777);
                }
            }
            goto logWrite;
        }
    }
    return $_receipt;
}

/**
 * 异常记录日志 error log
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function errorLog(string $msg)
{
    $_uri = LOG_EXCEPTION.date('Ymd').'.log';
    $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
    return _log($_uri,$_model_msg);
}