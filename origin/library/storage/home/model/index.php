<?php
return array(
    /*
     *    # MODEL_OBJECT_NAME_MARK : 模板名，用于在多模板结构下区分对象结构信息
     *    # MODEL_SERVICE_OBJECT_FUNCTION_MARK : 应用对象函数对象名，用来提示监听单元，在目标对象函数启动时激活本服务配置
     *    # MODEL_SERVICE_OBJECT_REQUEST_METHOD_MARK : 应用对参数请求类型 (Get:0|Post:1|Request:2|GetAndPost:3)
     *    # MODEL_SERVICE_OBJECT_EXECUTE_QUERY_MARK : 应用sql语句
     *    # MODEL_SERVICE_OBJECT_VARIABLE_MARK : 应对对象参数列表，参数结构详见变量对象结构说明
     * */
    # 变量对象标签设置
    'VARIABLE_OBJECT_NAME_MARK' => 'name',
    'VARIABLE_OBJECT_TO_FIELD_MARK' => 'field',
    'VARIABLE_OBJECT_PARAM_MARK' => 'param',
    'VARIABLE_OBJECT_TYPE_MARK' => 'type',
    'VARIABLE_OBJECT_MIN_MARK' => 'min',
    'VARIABLE_OBJECT_MAX_MARK' => 'max',
    'VARIABLE_OBJECT_NULL_STATUS_MARK' => 'is_null',
    'VARIABLE_OBJECT_DEFAULT_MARK' => 'default',
    'VARIABLE_OBJECT_VALIDATE_FORMAT_MARK' => 'format',
    # 模板对象标签设置
    'MODEL_OBJECT_NAME_MARK' => 'model_name',
    'MODEL_SERVICE_OBJECT_REQUEST_METHOD_MARK' => 'object_request',
    'MODEL_SERVICE_OBJECT_EXECUTE_QUERY_MARK' => 'object_query',
    'MODEL_SERVICE_OBJECT_VARIABLE_MARK' => 'object_variable',
    array(
        'Model_Name' => '',
        'Object_Request' => '',
        'Object_Query' => '',
        'Object_Variable' => array(
            array(
                'Name' => 'SHen',
                'Field' => '',
                'Param' => array('Type' => 'string', 'Min' => '4', 'Max' => '0', 'Default' => 'hello', 'format' => null)
            ),
            array(
                'Name' => 'Qi',
                'Field' => '',
                'Param' => array('Type' => 'string', 'Min' => '2', 'Max' => '0', 'Default' => 'world', 'format' => null)
            ),
        ),
    ),
);