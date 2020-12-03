<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架变量过滤封装类
 */
namespace Origin\Package;

use Exception;

class View
{
    /**
     * @access public
     * @param string $dir 地址目录
     * @param string $page 模板信息
     * @param array $param 参数值内容
     * @param float $time 起始加载时间
     * @return void
     * @context 模板页加载方法
     */
    public static function view(string $dir, string $page, array $param, float $time)
    {
        # 转化文件路径
        $_guide = explode('/',$dir);
        # 判断结构模型
        $_dir = DEFAULT_APPLICATION."/";
        # 判断引导路径中是否存在多级文件
        if(count($_guide) > 1){
            for($i=0; $i<count($_guide);$i++){
                if(($i+1) == count($_guide))
                    $dir = $_guide[count($_guide)-1];
                else
                    $_dir = $_guide[$i].'/';
            }
        }
        # 获取应用目录
        $_url = replace("application/{$_dir}");
        # 判断应用目录是否有效
        if(is_dir($_url)){
            # 获得前台模板目录
            $_url_view = replace($_url."template/");
            # 判断前台模板目录是否有效
            if(is_dir($_url_view)){
                # 判断应用控制器对应前台模板目录是否有效
                if(is_dir($_url_view.$dir)){
                    # 调用模板
                    $_page = $_url_view.$dir.DS.$page.'.html';
                    if(is_file($_page)){
                        # 创建运行时间模板
                        $_temp = null;
                        if(DEBUG and TIME){
                            $_temp = ORIGIN."template/200.html";
                            if(is_file(replace($_temp))){
                                $_load_end = explode(" ",microtime());
                                $_load_end = floatval($_load_end[0])+floatval($_load_end[1]);
                                $_time= round(($_load_end-$time)*1000,2);
                                $_temp = file_get_contents($_temp);
                                $_temp = str_replace('{/time/}',$_time,$_temp);
                            }
                        }
                        # 加载参数内容
                        foreach($param as $_key => $_value){
                            $$_key = $_value;
                        }
                        # 清除寄存数组信息
                        unset($param);
                        # 执行模板解析
                        $_label = new Label($_page);
                        # 获取解析后文件内容
                        $_cache_code = $_label->execute();
                        if(config("ROOT_USE_BUFFER")){
                            $_debug_tmp = "resource/public/cache/".sha1($_page).".tmp";
                            $_file = new File();
                            $_cache_uri = replace(ROOT.DS.$_debug_tmp);
                            if(!is_file($_cache_uri) or time() > strtotime("+30 minutes",filemtime($_cache_uri))){
                                $_file->write($_debug_tmp,"cw",$_cache_code.$_temp);
                            }
                        }else{
                            # 获取解析后代码,生成临时缓存文件
                            $_cache_file = tmpfile();
                            # 写入解析后模板内容
                            fwrite($_cache_file,$_cache_code.$_temp);
                            # 通过数据流获取缓存文件临时路径信息
                            $_cache_uri = stream_get_meta_data($_cache_file)["uri"];
                        }
                        # 调用缓存文件
                        include("{$_cache_uri}");
                        # 关闭缓存文件，系统自动释放缓存空间
                        if(isset($_cache_file))
                            fclose($_cache_file);
                    }else{
                        # 异常提示：该对象模板不存在
                        try{
                            throw new Exception('The object template '.$_page.' does not exist');
                        }catch(Exception $e){
                            exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                            exit();
                        }
                    }
                }else{
                    # 异常提示：该对象模板不存在
                    try{
                        throw new Exception('The object template dir '.$_url_view.$dir.' does not exist');
                    }catch(Exception $e){
                        exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                        exit();
                    }
                }
            }else{
                # 异常提示：请在当前路径下创建view文件夹
                try{
                    throw new Exception('Please create the (view) folder under the current path:'.$_url);
                }catch(Exception $e){
                    exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }
        }else{
            try{
                throw new Exception('The folder address '.$_url.' does not exist');
            }catch(Exception $e){
                exception("View Error",$e->getMessage(),debug_backtrace(0,1));
                exit();
            }
        }
    }
}