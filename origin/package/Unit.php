<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin公共控制器
 */
namespace Origin\Package;

use Exception;

/**
 * 功能控制器，负责内核功能方法的预加载调用
*/
abstract class Unit
{
    /**
     * @access private
     * @var array $Param 装载参数信息数组
    */
    private array $Param = array();

    /**
     * @access public
     * @return void
     * @context 构造方法，获取当前操作类信息
    */
    public function __construct()
    {}

    /**
     * @access protected
     * @param string $key
     * @param mixed $value
     * @return void
     * @context 向模板加载数据信息
     */
    protected function param(string $key, $value)
    {
        $_regular = '/^[^\_\W]+(\_[^\_\W]+)*$/';
        if(is_true($_regular, $key)){
            $this->Param[Junction::$Class][$key] = $value;
        }else{
            # 异常提示：变量名称包含非合法符号
            try{
                throw new Exception('Variable name contains non legal symbols');
            }catch(Exception $e){
                errorLog($e->getMessage());
                exception("Param Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
    }

    /**
     * @access protected
     * @param string|null $template 视图模板
     * @return void
     * @context 调用模板方法
     */
    protected function template(?string $template=null)
    {
        $_page = Junction::$Function;
        $_regular = '/^[^\_\W]+(\_[^\_\W]+)*(\:[^\_\W]+(\_[^\_\W]+)*)*$/';
        $_dir = str_replace("classes/", '',
                str_replace(DEFAULT_APPLICATION."/", '',
                    str_replace('application/', '',
                        str_replace('\\', '/', strtolower(Junction::$Class)))));
        if(is_null($template) and is_true($_regular, $template)){
            $_page = $template;
        }
        View::view($_dir, $_page,$this->Param[Junction::$Class],Junction::$LoadTime);
    }

    /**
     * @access protected
     * @return string
     * @context 返回执行对象类名
     */
    protected function get_class()
    {
        return Junction::$Class;
    }

    /**
     * @access protected
     * @return string
     * @context 返回执行对象方法名
     */
    protected function get_function()
    {
        return Junction::$Function;
    }

    /**
     * @access protected
     * @param string $message
     * @param string $url
     * @param int $time
     * @return void
     * @context 执行成功提示信息
    */
    protected function success(string $message='success',string $url='#',int $time=3)
    {
        $_setting = array("bgcolor"=>"floralwhite","color"=>"#000000","title"=>"Success");
        Output::output($message, $url, $time, $_setting);
    }

    /**
     * @access protected
     * @param string $message
     * @param string $url
     * @param int $time
     * @return void
     * @context 错误提示
    */
    protected function error(string $message='error',string $url='#',int $time=3)
    {
        $_setting = array("bgcolor"=>"orangered","color"=>"floralwhite","title"=>"Error");
        Output::output($message, $url, $time, $_setting);
    }

    /**
     * @access public
     * @param string $url
     * @return void
     * @context 地址跳转（重定向）
     */
    protected function redirect(string $url)
    {
        header("Location:{$url}");
    }

    /**
     * @access public
     * @param array $array
     * @return void
     * @context json格式输出
     */
    protected function json(array $array)
    {
        Output::json($array);
    }
}