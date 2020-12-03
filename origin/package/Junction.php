<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context Origin自动加载封装类
 */
namespace Origin\Package;

use Exception;

class Junction
{
    /**
     * @access public
     * @static
     * @var string|null $Class
     */
    public static ?string $Class = null;

    /**
     * @access public
     * @static
     * @var string|null $Function
     */
    public static ?string $Function = null;

    /**
     * @access public
     * @static
     * @var float $LoadTime
    */
    public static float $LoadTime = 0.0;

    /**
     * @access public
     * @return void
     * @context 默认模式，自动加载入口
     */
    public static function initialize()
    {
        # 应用结构包调用
        if(is_file($_common = replace(ROOT . "/application/common/public.php")))
            include("{$_common}");
        # 运行起始时间
        self::$LoadTime = explode(" ",microtime());
        self::$LoadTime = floatval(self::$LoadTime[0])+floatval(self::$LoadTime[1]);
        /**
         * 使用请求器和验证结构进行入口保护
         * @var string $_class 带命名空间信息的类信息
         * @var string $_object 类实例化对象
         * @var string $_method 类对象方法
         */
        # 判断自动加载方法
        if(function_exists('spl_autoload_register')){
            # 设置基础控制器参数变量
            $_catalogue = DEFAULT_APPLICATION;
            # 默认控制器方法名
            $_method = DEFAULT_FUNCTION;
            # 获取的路径信息
            if(is_null($_SERVER['PATH_INFO']) or empty($_SERVER['PATH_INFO']))
                $_path = self::route($_SERVER["REQUEST_URI"]);
            else
                $_path = self::route($_SERVER['PATH_INFO']); // nginx条件下PATH_INFO返回值为空
            # 路由返回值为数组
            if(is_array($_path)){
                $_function = $_path[1];
                $_path = $_path[0];
            }
            # 获取协议信息
            $_protocol = $_SERVER["SERVER_PROTOCOL"];
            # 获取服务软件信息
            $_server = $_SERVER["SERVER_SOFTWARE"];
            # 获取地址完整信息
            $_http = $_SERVER["HTTP_HOST"];
            # 获取请求地址信息
            $_request = $_SERVER["REQUEST_URI"];
            # 获取请求器类型
            $_type = $_SERVER["REQUEST_METHOD"];
            # 获取用户ip
            $_use = $_SERVER["REMOTE_ADDR"];
            # 初始化对象及路径变量
            $_class_path = null;
            $_class_namespace = null;
            # 对请求对象地址请求内容进行截取
            if(strpos($_request,'?'))
                $_request = substr($_request,0,strpos($_request,'?'));
            # 执行初始化
            # 判断执行对象是否为程序单元
            $_bool = false;
            $_suffix = array(".html",".htm",".php");
            for($_i = 0;$_i < count($_suffix);$_i++){
                if(!empty(strpos($_request,$_suffix[$_i]))){
                    $_bool = true;
                    break;
                }
            }
            # 忽略其他资源类型文件索引
            if(!$_bool)
                if(strpos($_request,".") === false) $_bool = true;
            if($_bool){
                # 重定义指针， 起始位置0
                if(!empty($_path) and $_path != "/"){
                    # 转化路径为数组结构
                    $_path_array = explode('/',strtolower($_path));
                    $_i = 0;
                    # 循环路径数组
                    for(;$_i < count($_path_array);$_i++){
                        $_symbol = null;
                        $_namespace_symbol = null;
                        if(!is_null($_class_path)){
                            $_symbol = DS;
                            $_namespace_symbol = "\\";
                        }
                        # 拼接地址内容
                        if(is_dir(ROOT.$_class_path.$_symbol.$_path_array[$_i])){
                            $_class_path .= $_symbol.$_path_array[$_i];
                            $_class_namespace .= $_namespace_symbol.ucfirst($_path_array[$_i]);
                            if($_path_array[$_i] === "application")
                                $_catalogue = $_path_array[$_i + 1];
                            continue;
                        }
                        if(is_dir(ROOT.DS."application".DS.$_path_array[$_i])){
                            $_class_path .= DS."application".DS.$_path_array[$_i];
                            $_class_namespace .= $_namespace_symbol."Application\\".ucfirst($_path_array[$_i]);
                            $_catalogue = $_path_array[$_i];
                            continue;
                        }
                        if(is_file(ROOT.$_class_path.$_symbol."classes".DS.ucfirst($_path_array[$_i]).".php")){
                            $_class_path .= $_symbol."classes".DS.ucfirst($_path_array[$_i]).".php";
                            $_class_namespace .= $_namespace_symbol."Classes\\".ucfirst($_path_array[$_i]);
                            $_class = ucfirst($_path_array[$_i]);
                            break;
                        }
                        if(isset($_function) and $_i === (count($_path_array) - 1)
                            and is_file(ROOT.$_class_path.$_symbol.ucfirst($_path_array[$_i]).".php")){
                            $_class_path .= $_symbol.ucfirst($_path_array[$_i]).".php";
                            $_class_namespace .= $_namespace_symbol.ucfirst($_path_array[$_i]);
                            $_class = ucfirst($_path_array[$_i]);
                            break;
                        }
                        if(is_file(ROOT.DS."application".DS.DEFAULT_APPLICATION.DS."classes".DS.ucfirst($_path_array[$_i]).".php")){
                            $_class_path = replace("application/{$_catalogue}/classes/".ucfirst($_path_array[$_i]).".php");
                            $_class_namespace = "Application\\".ucfirst(DEFAULT_APPLICATION)."\\Classes\\".ucfirst($_path_array[$_i]);
                            $_class = ucfirst(ucfirst($_class_path[$_i]));
                            break;
                        }
                    }
                    if(!isset($_class)){
                        $_class_path .= replace("/classes/".ucfirst(DEFAULT_CLASS).".php");
                        $_class_namespace .= "\\Classes\\".ucfirst(DEFAULT_CLASS);
                    }
                    if(!isset($_function)){
                        if($_i < (count( $_path_array) -1))
                            $_method = $_path_array[$_i+1];
                    }else
                        $_method = $_function;
                }else{
                    $_class_path = replace("application/{$_catalogue}/classes/".ucfirst(DEFAULT_CLASS).".php");
                    $_class_namespace = "Application\\".ucfirst(DEFAULT_APPLICATION)."\\Classes\\".ucfirst(DEFAULT_CLASS);
                }
                # 使用加载函数引入应用公共方法文件
                if(is_file($_public = replace(ROOT."/application/{$_catalogue}/common/public.php")))
                    include("{$_public}");
                # 初始化重启位置
                load:
                # 验证文件地址是否可以访问
                if(!isset($_class_path) or !is_file(ROOT.DS.$_class_path)){
                    if(DEBUG){
                        if(initialize()){
                            goto load;
                        }
                        try {
                            throw new Exception('Origin Loading Error: Not Fount Classes Document');
                        } catch (Exception $e) {
                            self::error(replace("{$_class_path}.php"), $e->getMessage(), "File");
                            exit(0);
                        }
                    }else{
                        $_404 = replace(ROOT_RESOURCE."/public/template/404.html");
                        if(!is_file($_404)){
                            echo("ERROR:404");
                            exit();
                        }else{
                            include("{$_404}");
                        }
                    }
                }
                # 调用自动加载函数
                self::autoload($_class_path);
                # 链接记录日志
                $_uri = LOG_ACCESS.date('Ymd').'.log';
                $_msg = "[".$_protocol."] [".$_server."] [Request:".$_type."] to ".$_http.$_request.", by user IP:".$_use;
                $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$_msg.PHP_EOL;
                _log($_uri,$_model_msg);
                # 判断类是否存在,当自定义控制与默认控制器都不存在时，系统抛出异常
                if(class_exists($_class_namespace)){
                    self::$Class = $_class_namespace;
                    # 声明类对象
                    $_object = new $_class_namespace();
                }else{
                    try {
                        throw new Exception('Origin Loading Error: Not Fount Control Class');
                    }catch(Exception $e){
                        self::error("{$_class_namespace}",$e->getMessage(),"Class");
                        exit(0);
                    }
                }
                # 判断方法信息是否可以被调用
                if(method_exists($_object, $_method) and is_callable(array($_object, $_method))){
                    self::$Function = $_method;
                    # 执行方法调用
                    $_object->$_method();
                }else{
                    try {
                        throw new Exception('Origin Loading Error: Not Fount Function Object');
                    } catch (Exception $e) {
                        self::error("{$_method}", $e->getMessage(), "Function");
                        exit(0);
                    }
                }
            }
        }
    }

    /**
     * @access protected
     * @param string $uri 路由对象地址
     * @return array|string
     * @context 路由解析函数
     */
    protected static function route(string $uri){
        # 创建回执变量
        $_receipt = null;
        # 配置文件地址
        $_config = replace(ROOT."/common/config/route.php");
        # 获取路由列表信息
        $_configure = include("{$_config}");
        # 循环比对路由信息
        for($_i = 0;$_i < count($_configure);$_i++){
            # 判断路由内容数据类型(string|array)
            if(key_exists("mapping",$_configure[$_i])){
                $_config= array_change_key_case($_configure[$_i]);
                if(is_array($_config["mapping"])){
                    $_config["mapping"] = array_change_value_case($_config["mapping"]);
                    if(!in_array($uri,$_config["mapping"])) continue;
                }elseif(strtolower($_config["mapping"]) != strtolower($uri)) continue;
                if(key_exists("method",$_config) and $_config["method"] != "normal"){
                    if(strtoupper($_config["method"]) != $_SERVER["REQUEST_METHOD"]) break;
                }
                if(key_exists("classes",$_config) and key_exists("functions",$_config)){
                    $_receipt = array($_config["classes"],$_config["functions"]);
                    break;
                }else break;
            }else
                continue;
        }
        End:
        if(is_null($_receipt)){
            $_start = 0;
            if(strpos("/",$uri) == 0)
                $_start = 1;
            if(strpos($uri,'.'))
                $_path = substr($uri, $_start, strpos($uri,'.')-1);
            else
                $_path = substr($uri, $_start);
            $_receipt = $_path;
        }
        # 返回回执内容
        return $_receipt;
    }

    /**
     * @access protected
     * @param string $file 文件地址
     * @return void
     * @context 自动加载模块
     */
    protected static function autoload(string $file)
    {
        # 设置引导地址
        set_include_path(ROOT);
        # 判断文件是否存在
        if(!spl_autoload_register(function($file){
            # 转化命名空间内容，拆分结构
            $_file = explode("\\",$file);
            # 循环修改命名空间元素首字母
            for($_i = 0;$_i < count($_file);$_i++){
                # 修改文件名,类文件名跳过
                if($_i === (count($_file) - 1))
                    continue;
                $_file[$_i] = strtolower($_file[$_i]);
            }
            # 重组加载信息内容
            $file = implode(DS,$_file);
            require_once("{$file}.php");
        })){
            try {
                throw new Exception('Origin Loading Error: Registration load failed');
            } catch (Exception $e) {
                self::error(replace("{$file}.php"), $e->getMessage(), "File");
                exit(0);
            }
        }
    }

    /**
     * @access public
     * @param string $obj 未加载对象（class|function）
     * @param string $error 错误信息
     * @param string $type 加载类型
     * @return void
     * @context 加载错误信息
     */
    public static function error(string $obj, string $error, string $type)
    {
        if(DEBUG or ERROR){
            if(!is_file($_404 = replace(RESOURCE_PUBLIC."/template/404.html"))){
                $_404 = replace(ORIGIN.'template/404.html');
            }
            include("{$_404}");
            if($obj) unset($obj);
            if($error) unset($error);
            if($type) unset($type);
            exit(0);
        }
    }
}