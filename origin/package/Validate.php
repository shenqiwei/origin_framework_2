<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context:
 * Origin变量验证封装类，可以对预设结构或自定义结构进行验证
 * 界限值只支持大于和小于，最大和最小界限值相等且都大于0时，表示验证值进行等于验证
 * 当最小值和最大值都等于0或者为空时表示验证值不受长度限制
 */
namespace Origin\Package;
/**
 * 参数验证基类，公共类
 */
class Validate
{
    /**
     * @access private
     * @var string|null $Error 错误信息返回
     */
    private ?string $Error = null;

    /**
     * @access public
     * @param mixed $variable 验证对象变量
     * @return boolean
     * @context 执行空值验证
    */
    public function _empty($variable)
    {
        $_return = false;
        # 判断验证参数是否为数据类型，如果是则跳过验证直接返回错误提示
        if(is_array($variable) and !empty($this->_Variable)){
            $_return = true;
        }else{
            # 使用empty函数判断参数值是否为空
            if(!empty($variable))
                $_return = true;
            elseif(empty($variable) and !is_null($variable)){
                # 由于empty函数特性，设置例外参数数据类型的验证，保证验证精度，由于当前版本值支持字符串验证，所以本结构段只有少量结构代码会被执行
                if(is_int($variable) and $variable == 0)
                    $_return = true;
                elseif((is_float($variable) or is_double($variable)) and $variable == 0.0)
                    $_return = true;
                elseif(is_bool($variable) and $variable == false)
                    $_return = true;
                elseif(is_string($variable) and $variable == '0')
                    $_return = true;
                else
                    # error: Verify the value is null
                    $this->Error = 'Verify the value is null';
            }else{
                if(!is_null($variable))
                    $_return = true;
            }
        }
        return $_return;
    }

    /**
     * @access public
     * @param mixed $variable 验证对象变量
     * @param int|float $min
     * @param int|float $max
     * @return boolean
     * @context 执行值长度范围验证
     * 1.最小范围值大于最大范围值，验证参数数值对调
     * 2.最小范围值等于最大范围值，值进行大于等于值的验证
     * 3.最小范围值大于0，最大范围值小于等于0，值进行大于等于值的验证
     * 4.最小范围值等于小于0，最大范围值大于等于0，值进行小于等于值的验证、
     * 5.最小范围值和最小范围值小于等于0，则不限制大小，参数值全部默认等于0
    */
    public function _size($variable,$min=0,$max=0)
    {
        /**
         * @var mixed $_return
        */
        $_return = false;
        # 判断验证参数是否为数据类型，如果是则跳过验证直接返回错误提示
        if(is_array($variable)){
            # Origin Class Error: Unable to verify the array
            $this->Error = 'Unable to verify the array';
        }else{
            # 判断验证参数值是否被设置为可以为空
            if($this->_empty($variable)) {
                # 判断范围值是否都为小于0的参数，如果小于0，则参数值都等于0
                if ($min < 0) {
                    $min = 0;
                }
                if ($max < 0) {
                    $max = 0;
                }
                # 比对范围值大小，如果最小范围值大于最大范围值，执行参数值置换
                if ($min > $max and $max > 0) {
                    $_middle = $min;
                    $min = $max;
                    $max = $_middle;
                }
                # 当最大范围值和最小范围值相等时
                if(($min == $max) and $min > 0){
                    if(strlen($variable) != $min){
                        # Origin Class Error: Verify the value length do not agree with requirements
                        $this->Error = 'Verify the value length do not agree with requirements';
                    }
                }
                # 判断最小范围值大于零，且最大范围值等于0，执行大于等于值的判断
                # 如果最小范围值等于最大范围值，且值大于0，也将执行大于等于值的判断
                if (($min > 0 and $max == 0)) {
                    if (strlen($variable) < $min or mb_strlen($variable) < $min) {
                        # Origin Class Error: Verify the value length is less than the minimum range
                        $this->Error = 'Verify the value length is less than the minimum range';
                    }
                }
                # 判断最大范围值大于0，且最小范围之等于0，执行小等于值的判断
                if ($max > 0 and $min == 0) {
                    if (strlen($variable) > $max or mb_strlen($variable) > $max) {
                        # Origin Class Error: Verify the length value is greater than the maximum range
                        $this->Error = 'Verify the length value is greater than the maximum range';
                    }
                }
                # 判断区间范围
                if($min > 0 and $max > 0 and $min < $max){
                    if(strlen($variable) < $min or mb_strlen($variable) < $min
                        or strlen($variable) > $max or mb_strlen($variable) > $max){
                        # Origin Class Error: Verify the value is beyond the range
                        $this->Error = 'Verify the value is beyond the range';
                    }
                }
            }
            # 如果最小范围值和最大范围值都等于0，则不作任何判断
        }
        if(is_null($this->Error))
            $_return = true;
        return $_return;
    }

    /**
     * @access public
     * @param mixed $variable 验证对象变量
     * @param string $format
     * @return boolean
     * @context 执行正则比对验证
     */
    public function _type($variable, string $format)
    {
        /**
         * @var mixed $_return
         */
        $_return = false;
        # 判断验证参数是否为数据类型，如果是则跳过验证直接返回错误提示
        # Origin Class Error: Unable to verify the array
        if(is_array($variable)){
            $this->Error = 'Unable to verify the array';
        }else{
            # 判断验证参数值是否被设置为可以为空
            if($this->_empty($variable)){
                if(!preg_match($format, $variable)){
                    $this->Error = 'Variable type error';
                }else{
                    $_return = true;
                }
            }
        }
        return $_return;
    }

    /**
     * @access public
     * @param string  $variable 验证对象变量
     * @return boolean
     * @context 执行ipv4地址验证，当is_null为true时，且参数值为空，则不进行验证，反之进行验证
    */
    public function _ipv4(string $variable)
    {
        /**
         * @var mixed $_return
         * @var array _array
         * @var int i
         */
        $_return = false;
        # 判断验证参数是否为数据类型，如果是则跳过验证直接返回错误提示
        # error: Verify the value format does not accord with requirements
        if(is_array($variable)){
            $this->Error = 'Verify the value format does not accord with requirements';
        }else {
            if(!empty(trim($variable)) or strlen(trim($variable)) > 0){
                # 拆分ip地址，将字符串转化为数组结构
                $_array = explode('.', $variable);
                # 判断转化后数组状态，是否可以进行进一步验证
                if (is_array($_array) and count($_array) == 4) {
                    # 循环遍历数组元素，进行ip地址信息特性验证
                    for ($i = 0; $i < count($_array); $i++) {
                        if ($i < 0 or $i > 255) {
                            $this->Error = 'Verify the value format does not accord with requirements';
                            break;
                        }
                    }
                } else {
                    $this->Error = 'Verify the value format does not accord with requirements';
                }
            }
        }
        if(is_null($this->Error))
            $_return = true;
        return $_return;
    }

    /**
     * @access public
     * @param string $variable 验证对象变量
     * @return boolean
     * @context 执行ipv6地址验证，当is_null为true时，且参数值为空，则不进行验证，反之进行验证
     */
    public function _ipv6(string $variable)
    {
        /**
         * @var mixed $_return
         * @var array _array
         * @var int i
         * @var int k
         * @var string $_keys
         * @var string $_regular
         */
        $_return = false;
        # 判断验证参数是否为数据类型，如果是则跳过验证直接返回错误提示
        if(is_array($variable)){
            $this->Error = ' Verify the value format does not accord with requirements';
        }else {
            if(!empty(trim($variable)) or strlen(trim($variable)) > 0){
                # 判断ip地址是否为初始地址，即：0:0:0:0:0:0:0:0 或者 ::
                if($variable != '0:0:0:0:0:0:0:0' and $variable != '::'){
                    # 拆分ip地址，将字符串转化为数组结构
                    $_array = explode(':', $variable);
                    # 判断转化后数组状态，是否可以进行进一步验证
                    if (is_array($_array) and count($_array) >2 and count($_array) < 9) {
                        $k = 0;
                        $_keys = null;
                        # 循环遍历数组元素，进行ip地址信息特性验证
                        for ($i = 0; $i < count($_array); $i++) {
                            $_regular = '/^([0-9A-F]{1,4}|[0-9a-f]|)$/';
                            if (!preg_match($_regular, $_array[$i])) {
                                # error: Verify the value format does not accord with requirements
                                $this->Error = 'Verify the value format does not accord with requirements';
                                break;
                            }else{
                                # 查找ipv6数组中有多少个空元素
                                if($_array[$i] == ''){
                                    # 当存在空元素时，将其键存入预设变量中，并进行累加
                                    if($k == 0)
                                        $_keys .= $i;
                                    else
                                        $_keys .= ','.$i;
                                    $k += 1;
                                    # 当空元素值大于2个时，根据ipv6语法特性进行，结构运算
                                    if($k >2){
                                        $_keys = explode(',',$_keys);
                                        if(intval($_keys[0]) + 1 == intval($_keys[1]) and intval($_keys[0]) + 2 == intval($_keys[2])){
                                            $this->Error = 'Variable type error';
                                            break;
                                        }
                                    }
                                }else{
                                    continue;
                                }
                            }
                        }
                    } else {
                        $this->Error = 'Variable type error';
                    }
                }
            }
        }
        if(is_null($this->Error))
            $_return = true;
        return $_return;
    }

    /**
     * @access public
     * @return mixed
     * @context 获取错误信息
     */
    public function getError(){
        return $this->Error;
    }
}