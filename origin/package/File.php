<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架文件操作封装
 */
namespace Origin\Package;

use Exception;

class File extends Folder
{
    /**
     * @access public
     * @context 操作常量
    */
    const FILE_READ = "r";
    const FILE_READ_WRITE = "rw";
    const FILE_SEQ_READ = "sr";
    const FILE_CONTENT_READ = "cr";
    const FILE_WRITE = "w";
    const FILE_LEFT_WRITE = "lw";
    const FILE_BEHIND_WRITE = "bw";
    const FILE_FULL_WRITE = "fw";
    const FILE_CONTENT_WRITE = "cw";

    /**
     * @access public
     * @param string $folder 文件地址
     * @param boolean $autocomplete 自动补全完整路径，默认值 false
     * @param boolean $throw 捕捉异常
     * @return boolean
     * @context 创建文件夹
     */
    function create(string $folder, bool $autocomplete=false, bool $throw=false)
    {
        # 设置返回对象
        $_receipt = false;
        # 判断文件夹是否已创建完成
        if(file_exists($_folder = replace(ROOT.DS.$folder)))
            $_receipt = true;
        else{
            if($_file = fopen($_folder, 'w')){
                $_receipt = true;
                fclose($_file);
            }else{
                # 获取文件夹信息
                $_dir = substr($folder,0,strrpos($folder,"/"));
                # 调用父类create方法
                if(parent::create($_dir,$autocomplete,true)){
                    if($_file = fopen($_folder, 'w')){
                        $_receipt = true;
                        fclose($_file);
                    }
                }else{
                    $this->Breakpoint = parent::getBreakpoint();
                }
            }
            if(!$_receipt){
                # 错误代码：00101，错误信息：文件创建失败
                $this->Error = "Create file [{$folder}] failed";
                try {
                    throw new Exception($this->Error);
                } catch (Exception $e) {
                    exception("File Error", $e->getMessage(), debug_backtrace(0, 1));
                    exit();
                }
            }
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $folder 文件夹地址
     * @param boolean $throw 捕捉异常
     * @return boolean
     * @context 删除文件夹
     */
    function remove(string $folder, bool $throw=false){
        # 设置返回对象
        $_receipt = false;
        if(!file_exists($_folder = replace(ROOT.DS.$folder)))
            $_receipt = true;
        else{
            if(!unlink($_folder)) {
                $this->Error = "Remove file [{$folder}] failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("File Error", $e->getMessage(), debug_backtrace(0, 1));
                        exit();
                    }
                }
            }else
                $_receipt = true;
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $folder 文件地址
     * @param string $name 新名称
     * @param boolean $throw 捕捉异常
     * @return boolean
     * @context 文件重命名
     */
    function rename(string $folder, string $name, bool $throw=false)
    {
        # 设置返回对象
        $_receipt = false;
        if(file_exists($_folder = replace(ROOT.DS.$folder))){
            if (!rename($_folder, $name)) {
                # 错误代码：00102，错误信息：文件重命名失败
                $this->Error = "File [{$_folder}] rename failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("File Error", $e->getMessage(), debug_backtrace(0, 1));
                        exit();
                    }
                }
            }
        }else{
            $this->Error = "The file is invalid!";
            if(!$throw){
                try{
                    throw new Exception($this->Error);
                }catch(Exception $e){
                    exception("File Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $file 文件路径
     * @param string $operate 操作类型
     * @param int $size 限定读取大小
     * @param boolean $throw 捕捉异常
     * @return mixed
     * @contact 内容信息读取
     * Operate 说明：
     * r:读取操作 操作方式：r
     * rw:读写操作 操作方式：r+
     * sr: 数据结构读取操作 操作对应函数file
     * cr: 读取全文 调用对应函数 file_get_contents
     */
    function read(string $file, string $operate=self::FILE_READ, int $size=0, bool $throw=false)
    {
        # 设置返回对象
        $_receipt = false;
        # 判断错误编号是否为初始状态
        # 调用路径文件验证
        if(!is_file($_folder = replace(ROOT.DS.$file))){
            switch ($operate) {
                case self::FILE_SEQ_READ: # 序列化读取
                    $_receipt = file($_folder);
                    break;
                case self::FILE_READ_WRITE: # 读写
                    $_handle = fopen($_folder, 'r+');
                    $_receipt = fread($_handle,($size > 0)?$size:filesize($_folder));
                    break;
                case self::FILE_CONTENT_READ: # 写入
                    $_receipt = file_get_contents($_folder, false);
                    break;
                case self::FILE_READ: # 读取
                default: # 默认状态与读取状态一致
                    $_handle = fopen($_folder, 'r');
                    $_receipt = fread($_handle,($size > 0)?$size:filesize($_folder));
                    break;
            }
        }else{
            $this->Error = "The file is invalid!";
            if(!$throw){
                try{
                    throw new Exception($this->Error);
                }catch(Exception $e){
                    exception("File Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $file 文件路径
     * @param string $msg 写入值
     * @param string $operate 操作类型
     * @param boolean $throw 捕捉异常
     * @return mixed
     * @contact 内容信息更新
     * Operate 说明：
     * w：写入操作 操作方式：w
     * lw：前写入 操作方式：w+
     * bw：后写入 操作方式：a
     * fw：补充写入 操作方式：a+
     * cw：重写 调用对应函数 file_put_contents
     */
    function write(string $file, string $msg, string $operate=self::FILE_WRITE, bool $throw=false)
    {
        # 设置返回对象
        $_receipt = false;
        # 判断错误编号是否为初始状态
        # 调用路径文件验证
        if(is_file($_folder = replace(ROOT.DS.$file))){
            # 未发生错误执行
            switch ($operate) {
                case self::FILE_WRITE: # 写入
                    $_write = fopen($_folder, 'w');
                    if ($_write) {
                        $_receipt = fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case self::FILE_LEFT_WRITE: # 写入
                    $_write = fopen($_folder, 'w+');
                    if ($_write) {
                        $_receipt = fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case self::FILE_BEHIND_WRITE: # 写入
                    $_write = fopen($_folder, 'a');
                    if ($_write) {
                        $_receipt = fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case self::FILE_FULL_WRITE: # 写入
                    $_write = fopen($_folder, 'a+');
                    if ($_write) {
                        $_receipt = fwrite($_write, strval($msg));
                        fclose($_write);
                    }
                    break;
                case self::FILE_CONTENT_WRITE: # 写入
                default: # 默认状态与读取状态一致
                    $_receipt = file_put_contents($_folder, strval($msg));
                    break;
            }
        }else{
            $this->Error = "The file is invalid!";
            if(!$throw){
                try{
                    throw new Exception($this->Error);
                }catch(Exception $e){
                    exception("File Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $folder 文件夹地址
     * @return mixed
     * @context 获取文件夹信息
     */
    function get(string $folder)
    {
        $_receipt = null;
        if(file_exists($_file = replace(ROOT.DS.$folder))){
            $_receipt = array(
                "file_name" => $folder,
                "file_size" => filesize($_file),
                "file_type" => filetype($_file),
                "file_change_time" => filectime($_file),
                "file_access_time" => fileatime($_file),
                "file_move_time" => filemtime($_file),
                "file_owner" => fileowner($_file),
                "file_limit" => fileperms($_file),
                "file_read" => is_readable($_file),
                "file_write" => is_writable($_file),
                "file_execute" => is_executable($_file),
                "file_create_type" => is_uploaded_file($_file)?"online":"location",
            );
        }
        return $_receipt;
    }
}