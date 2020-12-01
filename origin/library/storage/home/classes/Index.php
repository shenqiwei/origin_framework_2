<?php
/**
 * @context （Default Visit） Application class file
 */
namespace Application\Home\Classes;

use Origin\Package\Unit;

class Index extends Unit
{
    function __construct()
    {
        parent::__construct();
        $this->param('title','origin framework');
    }

    function index()
    {
        $welcomes = array(
            array('statement' => '感谢使用Origin ver 1.0'),
            array('statement' => 'Origin已经完成结构初始化，日志地址（common/log/initialize/initialize.log）'),
            array('statement' => '若需要重新初始化，请删除application、common、resource文件夹并刷新浏览器'),
            array('statement' => 'Origin的部分功能需要独立安装插件，请在使用前根据跟人需要进行匹配安装'),
            array('statement' => 'Github地址：https://github.com/shenqiwei/origin_framework'),
            array('statement' => '说明文档地址：https://github.com/shenqiwei/origin_readme'),
        );
        $this->param("welcome",$welcomes);
        $this->param("year",date("Y"));
        $this->template();
    }
}