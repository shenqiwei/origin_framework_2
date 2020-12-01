<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架标签二维解析器
 * 根据二维解释器程序结构特性，解释器会将一维结构中所有的应用逻辑进行数组降维展开，
 * 所以当数据维度超过一维结构时结构解释将返回null字节，同结构标签将无法完成维度解析
 * 该结构设计限制值针对企业定制框架模型及开源社区框架结构
 */
namespace Origin\Package;
/**
 * 标签解析主函数类
 */
class Label
{
    /**
     * @access private
     * @var string $ViewCode 解析代码
     */
    private string $ViewCode;

    /**
     * @access private
     * @var string $Variable 变量标记标签规则(输出)
     */
    private string $Variable = '/{\$[^_\W\s]+([_-]?[^_\W\s]+)*(\.\[\d+]|\.[^_\W\s]+([_-]?[^_\W\s]+)*)*(\|[^_\W\s]+([_-]?[^_\W\s]+)*)?}/';

    /**
     * @access private
     * @var string $VariableI 变量标记标签规则
    */
    private string $VariableI = '/\$[^_\W\s]+([_-]?[^_\W\s]+)*(\.\[\d+]|\.[^_\W\s]+([_-]?[^_\W\s]+)*)*(\|[^_\W\s]+([_-]?[^_\W\s]+)*)?/';

    /**
     * @access private
     * @var string $IncludeRegular <include href="src/html/page.html"/> 页面引入标签规则
     */
    private string $Include = '/\<include\s+href\s*=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*[\/]?>/';

    /**
     * @access private
     * @var string $JudgeCi condition_information : 'variable eq conditions_variable'
     * @var string $JudgeSi Symbol
     * @var string $JudgeIf if <if condition = 'variable eq conditions_variable'>
     * @var string $JudgeEF elseif <elseif condition = 'variable eq conditions_variable'/>
     * @var string $JudgeEl else <else/>
     * @var string $JudgeEl end </if>
     * @context 逻辑判断标记规则
     */
    private string  $JudgeIf = '/\<if\s+condition\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private string $JudgeEF = '/\<elseif\s+condition\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*[\/]?\>/';
    private string $JudgeEl = '/\<else[\/]?\>/';
    private string $JudgeIe = '/\<[\/]if\s*\>/';

    /**
     * @access private
     * @var string $ForOperation 'variable to circulation_count'
     * @var string $ForBegin <for operation = 'variable to circulation_count'>
     * @var string $ForEnd </for>
     * @context 循环执行标签规则
     */
    private string $ForBegin = '/\<for\s+operation\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private string $ForEnd = '/<\/for\s*>/';

    /**
     * @access private
     * @var string $ForeachOperation 'variable (as mark_variable)'
     * @var string $ForeachBegin <foreach operation = 'variable (as mark_variable)'>
     * @var string $ForeachEnd </foreach>
     * @context foreach循环标签规则
     */
    private string $ForeachBegin = '/\<foreach\s+operation\s*\=\s*(\'[^\<\>]+\'|\"[^\<\>]+\")\s*\>/';
    private string $ForeachEnd = '/\<[\/]foreach\s*\>/';

    /**
     * @access public
     * @param string $page 视图模板内容信息
     * @context 构造方法 获取引用页面地址信息
     */
    function __construct(string $page)
    {
        $this->ViewCode = $page;
    }

    /**
     * @access public
     * @return string
     * @context 默认函数，用于对模板中标签进行转化和结构重组
    */
    function execute()
    {
        # 创建初始标签标记变量
        $_obj = file_get_contents($this->ViewCode);
        # 去标签差异化
        $_obj = preg_replace('/\s*\\\:\s*(end)\s*\\\>/', ':end>',$_obj);
        # 转义引入结构
        $_obj = $this->__include($_obj);
        $_obj = $this->__for($_obj);
        $_obj = $this->__foreach($_obj);
        $_obj = $this->__if($_obj);
        $_obj = $this->variable($_obj);
        # 去除空白注释
        $_obj = preg_replace('/\\\<\\\!\\\-\\\-\s*\\\-\\\-\\\>/',"\r\n",str_replace('<!---->','',$_obj));
        # 去多余标签结构
        $_obj = preg_replace($this->ForEnd, '', $_obj);
        $_obj = preg_replace($this->ForeachEnd, '', $_obj);
        $_obj = preg_replace($this->JudgeEF, '', $_obj);
        $_obj = preg_replace($this->JudgeEl, '', $_obj);
        $_obj = preg_replace($this->JudgeIe, '', $_obj);
        # 遍历资源目录，替换资源信息
        $_obj = str_replace('__RESOURCE__',WEB_RESOURCE, $_obj);
        if(MARK_RELOAD){
            $_obj = preg_replace('/\s+/', ' ', $_obj);
        }
        return $_obj;
    }

    /**
     * @access protected
     * @param string $obj 解析代码段
     * @return string
     * @context 引入结构标签解释方法
     */
    function __include(string $obj)
    {
        # 获取include标签信息
        $_count = preg_match_all($this->Include, $obj, $_include, PREG_SET_ORDER);
        # 遍历include对象内容
        for($_i = 0;$_i < $_count; $_i++){
            # 拼接引入文件地址信息
            $_files = DIR_RESOURCE.'/public/'.str_replace('"','',$_include[$_i][1]);
            # 判断文件完整度
            if(is_file($_files)){
                # 读取引入对象内容
                $_mark = file_get_contents(ROOT.DS.replace($_files));
                # 执行结构内容替换
                $obj = str_replace($_include[$_i][0],$_mark,$obj);
            }
        }
        return $obj;
    }

    /**
     * @access protected
     * @param string $obj 解析代码段
     * @return string
     * @context 变量标签解释方法
     */
    function variable(string $obj)
    {
        # 传入参数为初始化状态，对代码段进行筛选过滤
        preg_match_all($this->Variable, $obj, $_label, PREG_SET_ORDER);
        # 迭代标记信息
        for($i=0; $i<count($_label);$i++) {
            # 存在连接符号,拆分标记
            $_var = str_replace('}', '', str_replace('{', '', $_label[$i][0]));
            # 拆分变量
            if(strpos($_var,".")){
                $_var = explode(".",$_var);
                $_variable = null;
                for($_i = 0;$_i < count($_var);$_i++){
                    if(empty($_i)) {
                        $_variable = $_var[$_i];
                    }elseif($_i == count($_var)-1){
                        # 验证拆分方法
                        if(strpos($_var[$_i],"|")){
                            $_vars = explode("|",$_var[$_i]);
                            $_function = $_vars[1];
                            $_variable .= "[\"{$_vars[0]}\"]";
                            $obj = str_replace($_label[$i][0],"<?php echo({$_function}({$_variable})); ?>",$obj);
                        }else{
                            $_variable .= "[\"{$_var[$_i]}\"]";
                            $obj = str_replace($_label[$i][0],"<?php echo({$_variable}); ?>",$obj);
                        }
                    }else{
                        if(preg_match("/^\[.+]$/",$_var[$_i]))
                            $_variable .= $_var[$_i];
                        else
                            $_variable .= "[\"{$_var[$_i]}\"]";
                    }
                }
            }else{
                # 验证拆分方法
                if(strpos($_var,"|")){
                    $_var = explode("|",$_var);
                    $obj = str_replace($_label[$i][0],"<?php echo({$_var[1]}({$_var[0]})); ?>",$obj);
                }else{
                    $obj = str_replace($_label[$i][0],"<?php echo({$_var}); ?>",$obj);
                }
            }
        }
        # 传入参数为初始化状态，对代码段进行筛选过滤
        preg_match_all($this->VariableI, $obj, $_label, PREG_SET_ORDER);
        # 迭代标记信息
        for($i=0; $i<count($_label);$i++) {
            # 存在连接符号,拆分标记
            $_var = str_replace(']', '', str_replace('[', '', $_label[$i][0]));
            # 拆分变量
            if(strpos($_var,".")){
                $_var = explode(".",$_var);
                $_variable = null;
                for($_i = 0;$_i < count($_var);$_i++){
                    if(empty($_i)) {
                        $_variable = $_var[$_i];
                    }elseif($_i == count($_var)-1){
                        # 验证拆分方法
                        if(strpos($_var[$_i],"|")){
                            $_vars = explode("|",$_var[$_i]);
                            $_function = $_vars[1];
                            $_variable .= "[\"{$_vars[0]}\"]";
                            $obj = str_replace($_label[$i][0],"{$_function}({$_variable})",$obj);
                        }else{
                            $_variable .= "[\"{$_var[$_i]}\"]";
                            $obj = str_replace($_label[$i][0],"{$_variable}",$obj);
                        }
                    }else{
                        if(preg_match("/^\[.+]$/",$_var[$_i]))
                            $_variable .= $_var[$_i];
                        else
                            $_variable .= "[\"{$_var[$_i]}\"]";
                    }
                }
            }else{
                # 验证拆分方法
                if(strpos($_var,"|")){
                    $_var = explode("|",$_var);
                    $obj = str_replace($_label[$i][0],"{$_var[1]}({$_var[0]})",$obj);
                }else{
                    $obj = str_replace($_label[$i][0],"{$_var}",$obj);
                }
            }
        }
        return $obj;
    }

    /**
     * @access protected
     * @param string $obj 解析代码段
     * @return string
     * @context 逻辑判断标签解释方法
     */
    function __if(string $obj)
    {
        # 获取if标签
        $_count = preg_match_all($this->JudgeIf,$obj , $_IF, PREG_SET_ORDER);
        for($_i = 0;$_i < $_count;$_i++){
            # 获取条件内容
            $_condition =  preg_replace('/[\'\"]*/', '', $_IF[$_i][1]);
            # 拆分条件内容
            $_condition = explode(" ",$_condition);
            $_symbol = $this->symbol($_condition[1]);
            if(is_numeric($_condition[2])){
                if(strpos($_condition[2],"."))
                    $_comparison = floatval($_condition[2]);
                else
                    $_comparison = intval($_condition[2]);
            }else
                $_comparison = strval($_condition[2]);
            if($_symbol == "in")
                $obj = str_replace($_IF[$_i][0],"<?php if(in_array({$_condition[0]},{$_comparison})){?>",$obj);
            else
                $obj = str_replace($_IF[$_i][0],"<?php if({$_condition[0]} {$_symbol} {$_comparison}){?>",$obj);
        }
        # 获取elseif标签
        $_count = preg_match_all($this->JudgeEF, $obj, $_EF, PREG_SET_ORDER);
        for($_i = 0;$_i < $_count;$_i++){
            # 获取条件内容
            $_condition =  preg_replace('/[\'\"]*/', '', $_EF[$_i][1]);
            # 拆分条件内容
            $_condition = explode(" ",$_condition);
            $_symbol = $this->symbol($_condition[1]);
            if(is_numeric($_condition[2])){
                if(strpos($_condition[2],"."))
                    $_comparison = floatval($_condition[2]);
                else
                    $_comparison = intval($_condition[2]);
            }else
                $_comparison = strval($_condition[2]);
            if($_symbol == "in")
                $obj = str_replace($_EF[$_i][0],"<?php }elseif(in_array({$_condition[0]},{$_comparison})){?>",$obj);
            else
                $obj = str_replace($_EF[$_i][0],"<?php }elseif({$_condition[0]} {$_symbol} {$_comparison}){?>",$obj);
        }
        # 转义else逻辑语法
        if(preg_match_all($this->JudgeEl, $obj, $_ELSE, PREG_SET_ORDER))
            $obj = str_replace($_ELSE[0][0], "<?php }else{ ?>", $obj);
        # 转义if逻辑结尾标签
        if(preg_match_all($this->JudgeIe, $obj, $_EIF, PREG_SET_ORDER))
            $obj = str_replace($_EIF[0][0],"<?php } ?>",$obj);
        return $obj;
    }

    /**
     * @access protected
     * @param string $symbol 运算符号
     * @return string
     * @context 逻辑批处理方法
     */
    function symbol(string $symbol)
    {
        $_symbol = array(
            "gt" => ">","lt" => "<","ge" => ">=","le" => "<=",
            "heq" => "===","nheq" => "!==","eq" => "==","neq" => "!=",
            "in" => "in"
        );
        if(key_exists($symbol,$_symbol))
            return $_symbol[$symbol];
        else
            return "==";

    }

    /**
     * @access public
     * @param string $obj 进行解析的代码段
     * @return string
     * @context for标签结构解释方法
     */
    function __for(string $obj)
    {
        # 获取当前代码段中是否存在foreach标签
        $_count = preg_match_all($this->ForBegin, $obj, $_begin, PREG_SET_ORDER);
        for($_i = 0;$_i < $_count;$_i++){
            $_operate = preg_replace('/[\'\"]*/', '', $_begin[$_i][1]);
            $_operate_i = "\$i_".str_replace("\$",null,$_operate);
            $obj = str_replace($_begin[$_i][0],"<?php for({$_operate_i}=0;$_operate_i < count({$_operate});{$_operate_i}++){ ?>",$obj);
            # 传入参数为初始化状态，对代码段进行筛选过滤
            preg_match_all($this->Variable, $obj, $_label, PREG_SET_ORDER);
            # 迭代标记信息
            for($i=0; $i<count($_label);$i++) {
                # 存在连接符号,拆分标记
                $_var = str_replace('}', '', str_replace('{', '', $_label[$i][0]));
                # 拆分变量
                if (strpos($_var, ".")) {
                    $_var = explode(".", $_var);
                    if($_var[0] == $_operate){
                        $_variable = null;
                        for($_m = 0;$_m < count($_var);$_m++){
                            if(empty($_m)){
                                $_variable = "$_var[$_m][{$_operate_i}]";
                            }elseif($_m == count($_var)-1){
                                # 验证拆分方法
                                if(strpos($_var[$_m],"|")){
                                    $_vars = explode("|",$_var[$_m]);
                                    $_function = $_vars[1];
                                    $_variable .= "[\"{$_vars[0]}\"]";
                                    $obj = str_replace($_label[$i][0],"<?php echo({$_function}({$_variable})); ?>",$obj);
                                }else{
                                    $_variable .= "[\"{$_var[$_m]}\"]";
                                    $obj = str_replace($_label[$i][0],"<?php echo({$_variable}); ?>",$obj);
                                }
                            }else{
                                $_variable .= "[\"{$_var[$_m]}\"]";
                            }
                        }
                    }
                }
            }
            # 传入参数为初始化状态，对代码段进行筛选过滤
            preg_match_all($this->VariableI, $obj, $_label, PREG_SET_ORDER);
            # 迭代标记信息
            for($i=0; $i<count($_label);$i++) {
                # 存在连接符号,拆分标记
                $_var = str_replace(']', '', str_replace('[', '', $_label[$i][0]));
                # 拆分变量
                if (strpos($_var, ".")) {
                    $_var = explode(".", $_var);
                    if($_var[0] == $_operate){
                        $_variable = null;
                        for($_m = 0;$_m < count($_var);$_m++){
                            if(empty($_m)){
                                $_variable = "$_var[$_m][{$_operate_i}]";
                            }elseif($_m == count($_var)-1){
                                # 验证拆分方法
                                if(strpos($_var[$_m],"|")){
                                    $_vars = explode("|",$_var[$_m]);
                                    $_function = $_vars[1];
                                    $_variable .= "[\"{$_vars[0]}\"]";
                                    $obj = str_replace($_label[$i][0],"{$_function}({$_variable})",$obj);
                                }else{
                                    $_variable .= "[\"{$_var[$_m]}\"]";
                                    $obj = str_replace($_label[$i][0],"{$_variable}",$obj);
                                }
                            }else{
                                $_variable .= "[\"{$_var[$_m]}\"]";
                            }
                        }
                    }
                }
            }
        }
        # 转义foreach逻辑结尾标签
        if(preg_match_all($this->ForEnd, $obj, $_end, PREG_SET_ORDER))
            $obj = str_replace($_end[0][0],"<?php } ?>",$obj);
        return $obj;
    }

    /**
     * @access protected
     * @param string $obj 解析代码段
     * @return string
     * @context foreach循环标签解释方法
     */
    function __foreach(string $obj)
    {
        $_count = preg_match_all($this->ForeachBegin, $obj, $_begin, PREG_SET_ORDER);
        for($_i = 0;$_i < $_count;$_i++){
            $_operate = preg_replace('/[\'\"]*/', '', $_begin[$_i][1]);
            if(strpos($_operate," as ")){
                $_operate = explode(" as ",$_operate);
                $_as_name = $_operate[1];
                $_operate = $_operate[0];
                $obj = str_replace($_begin[$_i][0],"<?php foreach({$_operate} as \${$_as_name}){ ?>",$obj);
                # 传入参数为初始化状态，对代码段进行筛选过滤
                preg_match_all($this->Variable, $obj, $_label, PREG_SET_ORDER);
                # 迭代标记信息
                for($i=0; $i<count($_label);$i++) {
                    # 存在连接符号,拆分标记
                    $_var = str_replace('}', '', str_replace('{', '', $_label[$i][0]));
                    # 拆分变量
                    if (strpos($_var, ".")) {
                        $_var = explode(".", $_var);
                        if($_var[0] == $_as_name) {
                            $_variable = null;
                            for ($_m = 0; $_m < count($_var); $_m++) {
                                if (empty($_m)) {
                                    $_variable = "$_var[$_m]";
                                }elseif($_m == count($_var)-1){
                                    # 验证拆分方法
                                    if(strpos($_var[$_m],"|")){
                                        $_vars = explode("|",$_var[$_m]);
                                        $_function = $_vars[1];
                                        $_variable .= "[\"{$_vars[0]}\"]";
                                        $obj = str_replace($_label[$i][0],"<?php echo({$_function}({$_variable})); ?>",$obj);
                                    }else{
                                        $_variable .= "[\"{$_var[$_m]}\"]";
                                        $obj = str_replace($_label[$i][0],"<?php echo({$_variable}); ?>",$obj);
                                    }
                                } else {
                                    $_variable .= "[\"{$_var[$_m]}\"]";
                                }
                            }
                        }
                    }
                }
                # 传入参数为初始化状态，对代码段进行筛选过滤
                preg_match_all($this->VariableI, $obj, $_label, PREG_SET_ORDER);
                # 迭代标记信息
                for($i=0; $i<count($_label);$i++) {
                    # 存在连接符号,拆分标记
                    $_var = str_replace(']', '', str_replace('[', '', $_label[$i][0]));
                    # 拆分变量
                    if (strpos($_var, ".")) {
                        $_var = explode(".", $_var);
                        if($_var[0] == $_as_name) {
                            $_variable = null;
                            for ($_m = 0; $_m < count($_var); $_m++) {
                                if (empty($_m)) {
                                    $_variable = "$_var[$_m]";
                                }elseif($_m == count($_var)-1){
                                    # 验证拆分方法
                                    if(strpos($_var[$_m],"|")){
                                        $_vars = explode("|",$_var[$_m]);
                                        $_function = $_vars[1];
                                        $_variable .= "[\"{$_vars[0]}\"]";
                                        $obj = str_replace($_label[$i][0],"{$_function}({$_variable})",$obj);
                                    }else{
                                        $_variable .= "[\"{$_var[$_m]}\"]";
                                        $obj = str_replace($_label[$i][0],"{$_variable}",$obj);
                                    }
                                } else {
                                    $_variable .= "[\"{$_var[$_m]}\"]";
                                }
                            }
                        }
                    }
                }
            }else{
                $_as = "{$_operate}_i";
                $obj = str_replace($_begin[$_i][0],"<?php foreach({$_operate} as {$_as}){ ?>",$obj);
                # 传入参数为初始化状态，对代码段进行筛选过滤
                preg_match_all($this->Variable, $obj, $_label, PREG_SET_ORDER);
                # 迭代标记信息
                for($i=0; $i<count($_label);$i++) {
                    # 存在连接符号,拆分标记
                    $_var = str_replace('}', '', str_replace('{', '', $_label[$i][0]));
                    # 拆分变量
                    if (strpos($_var, ".")) {
                        $_var = explode(".", $_var);
                        if($_var[0] == $_operate) {
                            $_variable = null;
                            for ($_m = 0; $_m < count($_var); $_m++) {
                                if (empty($_m)) {
                                    $_variable = "$_as";
                                }elseif($_m == count($_var)-1){
                                    # 验证拆分方法
                                    if(strpos($_var[$_m],"|")){
                                        $_vars = explode("|",$_var[$_m]);
                                        $_function = $_vars[1];
                                        $_variable .= "[\"{$_vars[0]}\"]";
                                        $obj = str_replace($_label[$i][0],"<?php echo({$_function}({$_variable})); ?>",$obj);
                                    }else{
                                        $_variable .= "[\"{$_var[$_m]}\"]";
                                        $obj = str_replace($_label[$i][0],"<?php echo({$_variable}); ?>",$obj);
                                    }
                                } else {
                                    $_variable .= "[\"{$_var[$_m]}\"]";
                                }
                            }
                        }
                    }
                }
                # 传入参数为初始化状态，对代码段进行筛选过滤
                preg_match_all($this->VariableI, $obj, $_label, PREG_SET_ORDER);
                # 迭代标记信息
                for($i=0; $i<count($_label);$i++) {
                    # 存在连接符号,拆分标记
                    $_var = str_replace(']', '', str_replace('[', '', $_label[$i][0]));
                    # 拆分变量
                    if (strpos($_var, ".")) {
                        $_var = explode(".", $_var);
                        if($_var[0] == $_operate) {
                            $_variable = null;
                            for ($_m = 0; $_m < count($_var); $_m++) {
                                if (empty($_m)) {
                                    $_variable = "$_as";
                                }elseif($_m == count($_var)-1){
                                    # 验证拆分方法
                                    if(strpos($_var[$_m],"|")){
                                        $_vars = explode("|",$_var[$_m]);
                                        $_function = $_vars[1];
                                        $_variable .= "[\"{$_vars[0]}\"]";
                                        $obj = str_replace($_label[$i][0],"{$_function}({$_variable})",$obj);
                                    }else{
                                        $_variable .= "[\"{$_var[$_m]}\"]";
                                        $obj = str_replace($_label[$i][0],"{$_variable}",$obj);
                                    }
                                } else {
                                    $_variable .= "[\"{$_var[$_m]}\"]";
                                }
                            }
                        }
                    }
                }
            }
        }
        # 转义foreach逻辑结尾标签
        if(preg_match_all($this->ForeachEnd, $obj, $_end, PREG_SET_ORDER))
            $obj = str_replace($_end[0][0],"<?php } ?>",$obj);
        return $obj;
    }
}