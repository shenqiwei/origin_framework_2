<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context Origin框架路由配置文件
 */
return array(
    /*
     * 路由配置信息为多为数组结构
     * mapping：路由映射地址 (string|array)
     * method：路由访问方式 (string):(normal|get|post)，强制方法类型
     * classes：应用类映射地址 (string)
     * functions：应用函数名称 (string)，默认函数 index
     * 例：
     * array( # 路由配置数组
     *     "mapping"=>array("/index.html"),
     *     "method"=>"get",
     *     "classes"=>"Application/Home/Classes/Index",
     *     "functions"=>"t"
     * ),
     */
    array(
        "mapping"=>array("/","/index.html","/default.html","/main/index.html"),
        "method"=>"normal",
        "classes"=>"Application/Home/Classes/Index",
        "functions"=>"index"
    ),
);