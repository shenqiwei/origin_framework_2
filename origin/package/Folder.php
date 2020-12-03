<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin框架文件夹操作封装
 */
namespace Origin\Package;

use Exception;

class Folder
{
    /**
     * @access protected
     * @var string $Root 根目录
    */
    protected string $Root = ROOT;

    /**
     * @access public
     * @param string|null $root 根目录地址
     * @return void
     * @context 构造方法
    */
    public function __construct(?string $root=null)
    {
        if(!is_null($root))
            $this->Root = $root;
    }

    /**
     * @access protected
     * @var string|null $Breakpoint 断点变量
     */
    protected ?string $Breakpoint = null;

    /**
     * @access public
     * @return string
     * @context 断点信息返回
     */
    public function getBreakpoint()
    {
        return $this->Breakpoint;
    }

    /**
     * @access protected
     * @var string|null $Error 错误信息
     */
    protected ?string $Error = null;

    /**
     * @access public
     * @return string
     * @context 获取错误信息
     */
    public function getError(){
        return $this->Error;
    }

    /**
     * @access public
     * @param string $folder 文件夹地址
     * @param boolean $autocomplete 自动补全完整路径，默认值 false
     * @param boolean $throw 捕捉异常
     * @return boolean
     * @context 创建文件夹
     */
    public function create(string $folder, bool $autocomplete=false, bool $throw=false)
    {
        # 设置返回对象
        $_receipt = false;
        # 判断文件夹是否已创建完成
        if(file_exists($_folder = replace(ROOT.DS.$folder)))
            $_receipt = true;
        else{
            if(!mkdir($_folder, 0777)){
                $_folder = explode('/',$folder);
                $_guide = null;
                for($_i = 0;$_i < count($_folder);$_i++){
                    if(empty($_i))
                        $_guide = $_folder[$_i];
                    else
                        $_guide .= DS.$_folder[$_i];
                    if(is_dir(ROOT.DS.$_guide))
                        continue;
                    else{
                        if($autocomplete){
                            if(!mkdir(ROOT.DS.$_guide,0777))
                                $this->Breakpoint = $_folder[$_i];
                        }else
                            $this->Breakpoint = $_folder[$_i];
                    }
                }
            }else
                $_receipt = true;
            if(!$_receipt){
                # 错误代码：00101，错误信息：文件创建失败
                $this->Error = "Create folder [{$_folder}] failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("Folder Error", $e->getMessage(), debug_backtrace(0, 1));
                        exit();
                    }
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
    public function remove(string $folder, bool $throw=false){
        # 设置返回对象
        $_receipt = false;
        if(!file_exists($_folder = replace(ROOT.DS.$folder)))
            $_receipt = true;
        else{
            if(!rmdir($_folder)) {
                $this->Error = "Remove folder [{$folder}] failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("Folder Error", $e->getMessage(), debug_backtrace(0, 1));
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
     * @context 文件夹重命名
     */
    public function rename(string $folder, string $name, bool $throw=false)
    {
        # 设置返回对象
        $_receipt = false;
        if(file_exists($_folder = replace(ROOT.DS.$folder))){
            if (!rename($_folder, $name)) {
                # 错误代码：00102，错误信息：文件夹重命名失败
                $this->Error = "Folder [{$_folder}] rename failed";
                if(!$throw){
                    try {
                        throw new Exception($this->Error);
                    } catch (Exception $e) {
                        exception("Folder Error", $e->getMessage(), debug_backtrace(0, 1));
                        exit();
                    }
                }
            }
        }else{
            $this->Error = "The folder is invalid!";
            if(!$throw){
                try{
                    throw new Exception($this->Error);
                }catch(Exception $e){
                    exception("Folder Error",$e->getMessage(),debug_backtrace(0,1));
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
    public function get(string $folder)
    {
        $_receipt = null;
        if(file_exists($_directory = replace(ROOT.DS.$folder))){
            if($_dir = opendir($_directory)){
                # 执行列表遍历
                while($_folder = readdir($_dir) !== false){
                    $_info = array(
                        "folder_name" => $_folder,
                        "folder_size" => filesize($_folder),
                        "folder_type" => filetype($_folder),
                        "folder_change_time" => filectime($_folder),
                        "folder_access_time" => fileatime($_folder),
                        "folder_move_time" => filemtime($_folder),
                        "folder_owner" => fileowner($_folder),
                        "folder_limit" => fileperms($_folder),
                        "folder_read" => is_readable($_folder),
                        "folder_write" => is_writable($_folder),
                        "folder_execute" => is_executable($_folder),
                        "folder_create_type" => is_uploaded_file($_folder)?"online":"location",
                        "folder_uri" => $_directory.DS.$_folder,
                    );
                    array_push($_receipt,$_info);
                }
                # 释放
                closedir($_dir);
            }
        }
        return $_receipt;
    }
}