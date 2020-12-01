<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin上传模块封装 (重构)
 */
namespace Origin\Package;

class Upload
{
    /**
     * @access private
     * @var string $Input 表单名
     */
    private string $Input;

    /**
     * @access private
     * @var int $Size 上传大小限制
     */
    private int $Size = 0;

    /**
     * @access private
     * @var array $Type 上传类型限制
     */
    private array $Type = array();

    /**
     * @access private
     * @var string|null $Store 存储位置
     */
    private ?string $Store = null;

    /**
     * @access private
     * @var string|null $_Error 错误信息
     */
    private ?string $Error = null;

    /**
     * @access private
     * @var array $TypeArray 文件扩展名比对数组
     */
    private array $TypeArray = array(
        'text/plain' => 'txt',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'text/html' => 'html',
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
    );

    /**
     * @access public
     * @param string $input 表单名称 form type is 'multipart/form-data' 该结构有效
     * @param array $type 上传文件类型
     * @param int $size 上传文件大小，默认值 0
     * @context 上传条件设置函数
     */
    function condition(string $input, array $type, int $size=0)
    {
        $this->Input = $input;
        $this->Type = $type;
        if(!empty(intval($size)))
            $this->Size = $size;
    }

    /**
     * @access public
     * @param string $guide 上传文件存储路径
     * @context 主目录设置函数
     */
    function store(string $guide)
    {
        if(!is_null($guide))
            $this->Store = replace($guide);
    }

    /**
     * @access public
     * @return boolean|string
     * @context 执行上传，上传成功后返回上传文件相对路径信息
     */
    function update()
    {
        $_receipt = false;
        # 存储目录
        $_dir = null;
        # 验证存储主目录是否有效
        if(is_null($this->Store)){
            # 设置存储子目录，使用年月日拆分存储内容
            $_dir = date("Ymd",time());
            $this->Store = replace("resource/upload/".$_dir);
        }
        if(!is_dir(replace(ROOT.DS.$this->Store))){
            $_file = new File();
            $_file->create(str_replace(DS,"/",$this->Store),true);
        }
        if(!$this->Input)
            $this->Error = "Upload file input is invalid";
        else{
            $_file = $_FILES[$this->Input];
            if(is_array($_file["name"])){
                $_folder = array();
                for($_i = 0;$_i < count($_file["name"]);$_i++){
                    if(key_exists($_file["type"][$_i],$this->TypeArray))
                        $_suffix = $this->TypeArray[$_file["type"][$_i]];
                    if(!isset($_suffix)){
                        $_suffix = explode(".",$_file["name"][$_i])[1];
                    }
                    if(isset($_suffix)){
                        if(!empty($this->Type)){
                            if(!in_array($_suffix,$this->Type))
                                $this->Error = "Files type is invalid";
                        }
                    }else{
                        $this->Error = "Files type is invalid";
                    }
                    if(is_null($this->Error)){
                        if($this->Size and $_file["size"][$_i] > $this->Size)
                            $this->Error = "Files size greater than defined value";
                    }
                    if(is_null($this->Error)){
                        $_upload_file = sha1($_file["tmp_name"][$_i]).time().".".$_suffix;
                        if(move_uploaded_file($_file['tmp_name'][$_i],
                            replace(ROOT.DS.$this->Store).DS.$_upload_file)){
                            array_push($_folder,$_dir."/".$_upload_file);
                        }else{
                            $this->Error = "Files upload failed";
                            break;
                        }
                    }
                }
                $_receipt = $_folder;
            }else{
                if(key_exists($_file["type"],$this->TypeArray))
                    $_suffix = $this->TypeArray[$_file["type"]];
                if(!isset($_suffix)){
                    $_suffix = explode(".",$_file["name"])[1];
                }
                if(isset($_suffix)){
                    if(!is_null($this->Type)){
                        if(!in_array($_suffix,$this->Type))
                            $this->Error = "Files type is invalid";
                    }
                }else{
                    $this->Error = "Files type is invalid";
                }
                if(is_null($this->Error)){
                    if($this->Size and $_file["size"] > $this->Size)
                        $this->Error = "Files size greater than defined value";
                }
                if(is_null($this->Error)){
                    $_upload_file = sha1($_file["tmp_name"]).time().".".$_suffix;
                    if(move_uploaded_file($_file['tmp_name'],
                        replace(ROOT.DS.$this->Store).DS.$_upload_file)){
                        $_receipt = $_dir."/".$_upload_file;
                    }else{
                        $this->Error = "Files upload failed";
                    }
                }
            }
        }
        return $_receipt;
    }

    /**
     * @access public
     * @return mixed
     * @context 获取错误信息
     */
    function getError()
    {
        return $this->Error;
    }
}