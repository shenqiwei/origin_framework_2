<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2017
 * @context: Origin在线请求器
 */
namespace Origin\Package;

use CURLFile;

class Curl
{
    /**
     * @access protected
     * @var array $CurlReceipt 请求返回信息
     * @var boolean $CurUtf8 是否执行utf-8转码
     */
    protected array $CurlReceipt = array();
    protected bool $CurUtf8;
    /**
     * @access public
     * @param boolean $bool 设置强制utf-8编码转换
     * @context 构造器
    */
    function __construct($bool = false)
    {
        $this->CurUtf8 = boolval($bool);
    }

    /**
     * @access public
     * @param string $url 访问地址
     * @param array $param 访问参数，可以使用get参数结构或者（k/v）数组结构
     * @param array $header 报文
     * @param boolean $ssl_peer 验证证书
     * @param boolean $ssl_host 验证地址
     * @return mixed
     * @content get请求函数
     */
    function get(string $url, array $param = array(), array$header=array(), bool $ssl_peer = false, bool $ssl_host = false)
    {
        $_receipt = null;
        if (!is_null($url)) {
            $_curl = curl_init();
            if(!empty($header)){
                # 设置请求头
                curl_setopt($_curl, CURLOPT_HTTPHEADER, $header);
            }
            curl_setopt($_curl, CURLOPT_URL, $url);
            curl_setopt($_curl, CURLOPT_POST, false);
            curl_setopt($_curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($_curl, CURLOPT_SSL_VERIFYPEER, boolval($ssl_peer));
            curl_setopt($_curl, CURLOPT_SSL_VERIFYHOST, boolval($ssl_host));
            if (!is_null($param))
                curl_setopt($_curl, CURLOPT_POSTFIELDS, $param);
            $_receipt = curl_exec($_curl);
            if ($this->CurUtf8)
                # 将会输内容强制转化为utf-8
                $_receipt = mb_convert_encoding($_receipt, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
            $this->CurlReceipt['errno'] = curl_errno($_curl);
            $this->CurlReceipt['error'] = curl_error($_curl);
            curl_close($_curl);
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $url 访问地址
     * @param array $param 访问参数，（k/v）数组结构
     * @param array $header 报文
     * @param boolean $ssl_peer 验证证书
     * @param boolean $ssl_host 验证地址
     * @return mixed
     * @content get请求函数
     */
    function post(string $url, array $param=array(), array $header=array(), bool $ssl_peer = false, bool $ssl_host = false)
    {
        $_receipt = null;
        if (!is_null($url)) {
            $_curl = curl_init();
            if(!empty($header)){
                # 设置请求头
                curl_setopt($_curl, CURLOPT_HTTPHEADER, $header);
            }
            curl_setopt($_curl, CURLOPT_URL, $url);
            curl_setopt($_curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($_curl, CURLOPT_HEADER, false);
            curl_setopt($_curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($_curl, CURLOPT_SSL_VERIFYPEER, boolval($ssl_peer));
            curl_setopt($_curl, CURLOPT_SSL_VERIFYHOST, boolval($ssl_host));
            curl_setopt($_curl, CURLOPT_POST, true);
            curl_setopt($_curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($_curl, CURLOPT_POSTFIELDS, $param);
            $_receipt = curl_exec($_curl);
            if ($this->CurUtf8)
                # 将会输内容强制转化为utf-8
                $_receipt = mb_convert_encoding($_receipt, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
            $this->CurlReceipt['errno'] = curl_errno($_curl);
            $this->CurlReceipt['error'] = curl_error($_curl);
            curl_close($_curl);
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param string $url 访问地址
     * @param string $folder 本地文件地址
     * @param string $type 文件类型
     * @param array $header 报文
     * @param string $input 表单名
     * @param boolean $ssl_peer 验证证书
     * @param boolean $ssl_host 验证地址
     * @return mixed
     * @context 文件上传
     */
    function upload(string $url, string $folder, string $type, array $header=array(), string $input = "pic", bool $ssl_peer = false, bool $ssl_host = false)
    {
        $_curl = curl_init();
        if(!empty($header)){
            # 设置请求头
            curl_setopt($_curl, CURLOPT_HTTPHEADER, $header);
            # 返回response头部信息
            curl_setopt($_curl, CURLOPT_HEADER, false);
        }
        $_file = new CURLFile($folder,$type);
        curl_setopt($_curl, CURLOPT_URL, $url);
        curl_setopt($_curl, CURLOPT_POSTFIELDS,array($input=>$_file));
        curl_setopt($_curl, CURLOPT_SSL_VERIFYPEER, boolval($ssl_peer));
        curl_setopt($_curl, CURLOPT_SSL_VERIFYHOST, boolval($ssl_host));
        $_receipt = curl_exec($_curl);
        curl_close($_curl);
        return $_receipt;
    }
    /**
     * @access public
     * @return mixed
     * @context 获取请求后返回值内容
    */
    function get_curl_receipt()
    {
        return $this->CurlReceipt;
    }
}