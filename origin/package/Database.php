<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin框架SQL执行语句封装类
 */
namespace Origin\Package;

use PDOException;
use PDO;

class Database extends Query
{
    /**
     * @access public
     * @context 操作常量
     */
    const QUERY_SELECT = "select";
    const QUERY_INSERT = "insert";
    const QUERY_UPDATE = "update";
    /**
     * @access private
     * @var PDO|object|resource $Connect 数据库连接
     */
    private $Connect;

    /**
     * @access private
     * @var string $Select select 为起始词
     */
    private string $Select = '/^(select)\s(([^\s]+\s)+|\*)\s(from)\s.*/';

    /**
     * @access private
     * @var string $SelectCount 带count关键字段
     */
    private string $SelectCount = '/^(select)\s(count\(([^\s]+\s)+|\*)\)\s(from)\s.*/';

    /**
     * @access private
     * @var string $From from 为起始词
     */
    private string $From = '/^(from)\s.*/';

    /**
     * @access private
     * @var int $RowCount 获取select查询响应条数信息
    */
    private int $RowCount = 0;

    /**
     * @access public
     * @param string|null $connect_name 数据源配置名称
     * @param int $type 数据库类型，默认值 0 <mysql|mariadb>
     * @context 构造函数，用于预加载数据源配置信息
    */
    function __construct(?string $connect_name=null, int $type=0)
    {
        # 保存数据源类型
        $this->DataType = intval($type);
        # 获取配置信息
        $_connect_config = config('DATA_MATRIX_CONFIG');
        if(is_array($_connect_config)){
            for($_i = 0;$_i < count($_connect_config);$_i++){
                if(key_exists("DATA_NAME",$_connect_config[$_i]) and $_connect_config[$_i]['DATA_NAME'] === $connect_name){
                    $_connect_conf = $_connect_config[$_i];
                    break;
                }
            }
            # 判断配置加载情况，如果失效则自动调用第一个配置信息数组
            if(!isset($_connect_conf)){
                $_connect_config = $_connect_config[0];
            }else
                $_connect_config = $_connect_conf;
            switch($this->DataType){
                case self::RESOURCE_TYPE_PGSQL:
                    $_DSN = "pgsql:host={$_connect_config["DATA_HOST"]};port={$_connect_config["DATA_PORT"]};dbname={$_connect_config["DATA_DB"]}";
                    break;
                case self::RESOURCE_TYPE_MSSQL:
                    $_DSN = "dblib:host={$_connect_config["DATA_HOST"]}:{$_connect_config["DATA_PORT"]};dbname={$_connect_config["DATA_DB"]}";
                    break;
                case self::RESOURCE_TYPE_SQLITE:
                    $_DSN = "sqlite:{$_connect_config["DATA_DB"]}";
                    break;
                case self::RESOURCE_TYPE_ORACLE:
                    $_oci = "(DESCRIPTION =
                            (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = {$_connect_config["DATA_HOST"]})(PORT = {$_connect_config["DATA_PORT"]})))
                            (CONNECT_DATA = (SERVICE_NAME = {$_connect_config["DATA_DB"]}))";
                    $_DSN = "oci:dbname={$_oci}";
                    break;
                case self::RESOURCE_TYPE_MYSQL:
                case self::RESOURCE_TYPE_MARIADB:
                default:
                    $_DSN = "mysql:host={$_connect_config["DATA_HOST"]};port={$_connect_config["DATA_PORT"]};dbname={$_connect_config["DATA_DB"]}";
                    break;
            }
            if(!in_array($this->DataType,array(self::RESOURCE_TYPE_SQLITE))){
                # 创建数据库链接地址，端口，应用数据库信息变量
                $_username = $_connect_config['DATA_USER']; # 数据库登录用户
                $_password = $_connect_config['DATA_PWD']; # 登录密码
                $_option = array(
                    # 设置数据库编码规则
                    PDO::ATTR_PERSISTENT => true,
                );
                # 创建数据库连接对象
                $this->Connect = new PDO($_DSN, $_username, $_password, $_option);
            }else{
                $this->Connect = new PDO($_DSN);
            }
            # 设置数据库参数信息
            $this->Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            # 是否使用持久链接
            $this->Connect->setAttribute(PDO::ATTR_PERSISTENT,boolval($_connect_config['DATA_P_CONNECT']));
            # SQL自动提交单语句
            if(in_array($this->DataType,array(self::RESOURCE_TYPE_ORACLE,self::RESOURCE_TYPE_MYSQL,self::RESOURCE_TYPE_MARIADB)))
                $this->Connect->setAttribute(PDO::ATTR_AUTOCOMMIT,boolval($_connect_config['DATA_AUTO']));
            # SQL请求超时时间
            if(intval(config('DATA_TIMEOUT')))
                $this->Connect->setAttribute(PDO::ATTR_TIMEOUT,intval($_connect_config['DATA_TIMEOUT']));
            # SQL是否使用缓冲查询
            if(boolval(config('DATA_USE_BUFFER'))){
                if(in_array($this->DataType,array(self::RESOURCE_TYPE_MYSQL,self::RESOURCE_TYPE_MARIADB)))
                    $this->Connect->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,boolval($_connect_config['DATA_USE_BUFFER']));
            }
        }
    }

    /**
     * @access public
     * @return int
     * @context 返回查询信息的总数
     */
    function count()
    {
        $_field = (!is_null($this->Field))?"count({$this->Field})":"count(*)";
        # 起始结构
        $_sql = "select {$_field} from {$this->Table} {$this->JoinOn} {$this->Union} {$this->Where}";
        # 返回数据
        return intval($this->query($_sql)[0][0]);
    }

    /**
     * @access public
     * @return mixed
     * @context 查询信息函数
     */
    function select()
    {
        # 求总和
        if(!is_null($this->Total)){
            if(!is_null($this->Field))
                $this->Total = ','.$this->Total;
        }
        # 平均数信息 与field冲突，需要group by配合使用
        if(!is_null($this->Avg)){
            if(!is_null($this->Total))
                $this->Avg = ','.$this->Avg;
        }
        # 最大值 与field冲突，需要group by配合使用
        if(!is_null($this->Max)){
            if(!is_null($this->Total) or !is_null($this->Avg))
                $this->Max = ','.$this->Max;

        }
        # 最小值 与field冲突，需要group by配合使用
        if(!is_null($this->Min)){
            if(!is_null($this->Total) or !is_null($this->Avg) or !is_null($this->Max))
                $this->Min = ','.$this->Min;
        }
        # 求和 与field冲突，需要group by配合使用
        if(!is_null($this->Sum)){
            if(!is_null($this->Total) or !is_null($this->Avg) or !is_null($this->Max) or !is_null($this->Min))
                $this->Sum = ','.$this->Sum;
        }
        # 添加查询头
        # 添加查询头
        $_sql = "select {$this->Field}{$this->Top}{$this->Total}{$this->Avg}{$this->Max}{$this->Min}{$this->Sum}{$this->Abs}{$this->Mod}".
                "{$this->Random}{$this->LTrim}{$this->Trim}{$this->RTrim}{$this->Replace}{$this->UpperCase}{$this->LowerCase}".
                "{$this->Mid}{$this->Length}{$this->Round}{$this->Now}{$this->Format}{$this->Distinct}".
                " from {$this->Table} {$this->JoinOn} {$this->AsTable} {$this->Union} {$this->Where} {$this->Group}".
                " {$this->Order} {$this->Having} {$this->Limit}";
        # 返回数据
        return $this->query($_sql);
    }

    /**
     * @access public
     * @return mixed
     * @context 插入信息函数
     */
    function insert()
    {
        $_columns = null;
        $_values = null;
        for($_i = 0; $_i < count($this->Data); $_i++){
            foreach($this->Data[$_i] as $_key => $_value){
                if($_i == 0){
                    $_columns = $_key;
                    if(is_integer($_value) or is_float($_value) or is_double($_value))
                        $_values = $_value;
                    else
                        $_values = '\''.$_value.'\'';
                }else{
                    $_columns .= ','.$_key;
                    if(is_integer($_value) or is_float($_value) or is_double($_value))
                        $_values .= ','.$_value;
                    else
                        $_values .= ',\''.$_value.'\'';
                }
            }
        }
        # 执行主函数
        $_sql = "insert into {$this->Table} ({$_columns})value({$_values})";
        # 返回数据
        return $this->query($_sql);
    }

    /**
     * @access public
     * @return mixed
     * @context 修改信息函数
     */
    function update()
    {
        $_columns = null;
        for($_i = 0; $_i < count($this->Data); $_i++){
            foreach($this->Data[$_i] as $_key => $_value){
                if($_i == 0){
                    if(is_integer($_value) or is_float($_value) or is_double($_value))
                        $_columns = $_key.'='.$_value;
                    else
                        $_columns = $_key.'=\''.$_value.'\'';
                }else{
                    if(is_integer($_value) or is_float($_value) or is_double($_value))
                        $_columns .= ','.$_key.'='.$_value;
                    else
                        $_columns .= ','.$_key.'=\''.$_value.'\'';
                }
            }
        }
        # 执行主函数
        $_sql = "update {$this->Table} set {$_columns} {$this->Where}";
        # 返回数据
        return $this->query($_sql);
    }

    /**
     * @access public
     * @return mixed
     * @context 删除信息函数
     */
    function delete()
    {
        # 执行主函数
        $_sql = "delete from {$this->Table} {$this->Where}";
        # 返回数据
        return $this->query($_sql);
    }

    /**
     * @access public
     * @param string $query sql语句
     * @return mixed
     * @context 自定义语句执行函数
     */
    function query(string $query)
    {
        # 创建返回信息变量
        $_receipt = null;
        if(is_true($this->SelectCount, strtolower($query)))
            $_select_count = null;
        elseif(is_true($this->From, strtolower($query)))
            $query = 'select * '.strtolower($query);
        if(strpos(strtolower($query),"select ") === 0)
            $_query_type = self::QUERY_SELECT;
        elseif(strpos(strtolower($query),"insert ") === 0)
            $_query_type = self::QUERY_INSERT;
        else
            $_query_type = self::QUERY_UPDATE;
        # 事务状态
        if(boolval(config("DATA_USE_TRANSACTION")) and $_query_type != 'select')
            $this->Connect->beginTransaction();
        # 条件运算结构转义
        foreach(array('/\s+gt\s+/' => '>', '/\s+lt\s+/ ' => '<','/\s+neq\s+/' => '!=', '/\s+eq\s+/'=> '=', '/\s+ge\s+/' => '>=', '/\s+le\s+/' => '<=') as $key => $value)
            $query = preg_replace($key, $value, $query);
        # 接入执行日志
        $_uri = LOG_EXCEPTION.date('Ymd').'.log';
        $_model_msg = date("Y/m/d H:i:s")." [Note]: ".trim($query).PHP_EOL;
        log($_uri,$_model_msg);
        try{
            # 执行查询搜索
            $_statement = $this->Connect->query(trim($query));
            # 返回查询结构
            if($_query_type === self::QUERY_SELECT){
                # 回写select查询条数
                $this->RowCount = $_statement->rowCount();
                if($this->FetchType === self::FETCH_NUMBER_VALUE)
                    $_receipt = $_statement->fetchAll(PDO::FETCH_NUM);
                elseif($this->FetchType === self::FETCH_KEY_VALUE)
                    $_receipt = $_statement->fetchAll(PDO::FETCH_ASSOC);
                else
                    if(isset($_select_count))
                        $_receipt = $_statement->fetchAll(PDO::FETCH_COLUMN)[0];
                    else
                        $_receipt = $_statement->fetchAll();
            }elseif($_query_type === self::QUERY_INSERT)
                $_receipt = $this->Connect->lastInsertId($this->Primary);
            else
                $_receipt = $_statement->rowCount();
            # 释放连接
            $_statement->closeCursor();
        }catch(PDOException $e){
            errorLog($e->getMessage());
            exception("SQL Error",$this->Connect->errorInfo(),debug_backtrace(0,1));
            exit();
        }
        return $_receipt;
    }

    /**
     * @access public
     * @context 执行事务提交
     */
    function getCommit()
    {
        $this->Connect->commit();
    }

    /**
     * @access public
     * @context 执行事务回滚
     */
    function getRollBack()
    {
        $this->Connect->rollBack();
    }

    /**
     * @access public
     * @return int
     * @contact 返回select查询条数信息
     */
    function getRowCount()
    {
        return $this->RowCount;
    }

    /**
     * @access public
     * @param string $url 链接
     * @param int $count 总数
     * @param int $current 当前页
     * @param int $row 分页大小
     * @param string|null $search 搜索条件
     * @return array
     * @contact 分页
     */
    function paging(string $url, int $count, int $current=1, int $row=10, string $search=null){
        $page=array(
            # 基本参数
            'url'=>$url, # 连接地址
            'count'=>0, # 总数
            'current'=>1, # 当前页码
            'begin'=>0, # 当前列表起始位置
            'limit'=>intval($row), # 显示数量
            'page_begin' => ($current-1)*intval($row)+1,
            'page_count' => $current*intval($row),
            'search' => $search, # 搜索内容
            # 连接参数
            'first_url'=>'','first'=>0, # 第一页参数
            'last_url'=>'','last'=>0, # 上一页参数
            'next_url'=>'','next'=>0, # 下一页参数
            'end_url'=>'','end'=>0, # 最后一页参数
            # number结构参数
            'num_begin'=>0, # Number区间显示翻页页码起始位置
            'num_end'=>0, # Number区间显示翻页页码结束位置
        );
        $page['current']=intval($current);
        $page['count']=$count%$page['limit']!=0?intval(($count/$page['limit'])+1):intval($count/$page['limit']);
        # 判断页标状态
        if($page['current']<=0) $page['current']=1;
        if($page['current']>$page['count']) $page['current']=$page['count'];
        if($page['count']<=0) $page['current']=$page['count']=1;
        $page['begin']=$page['limit']*($page['current']-1);//其实点运算
        $page['page_one']=$page['limit']+1;
        $page['page_end']=($page['limit']+$page['size'])>$count?$count:$page['limit']+$page['size'];
        # 判断翻页状态1
        if($page['current']>1)
            $page['last']=$page['current']-1;
        else
            $page['last']=1;
        # 判断翻页状态2
        if($page['current']>=$page['count'])
            $page['next']=$page['count'];
        else
            $page['next']=$page['current']+1;
        $page['first_url']=$page['url'].'?page=1'.$page["search"];//第一页
        $page['last_url']=$page['url'].'?page='.$page['last'].$page["search"];//上一页
        $page['next_url']=$page['url'].'?page='.$page['next'].$page["search"];//下一页
        $page['end_url']=$page['url'].'?page='.$page['count'].$page["search"];//最后一页
        return $page;
    }

    /**
     * @access public
     * @param array $page 分页数组
     * @param int $cols 页码数量
     * @return array
     * @contact 页脚
     */
    function footer(array $page,int  $cols=5){
        //执行数字页码
        $n=array();
        if($page['count']>$cols){
            $k=($cols%2==0)?$cols/2:($cols-1)/2;
            if(($page['current']-$k)>1 && ($page['current']+$k)<$page['count']){
                $page['num_begin']=$page['current']-$k;
                $page['num_end']=$page['current']+$k;
            }else{
                if(($page['current']+$k)>=$page['count']){
                    $page['num_begin']=$page['count']-($cols-1);
                    $page['num_end']=$page['count'];
                }else{
                    $page['num_begin']=1;
                    $page['num_end']=$cols;
                }
            }
            for($i=$page['num_begin'];$i<=$page['num_end'];$i++)
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$page["search"]));
        }else{
            for($i=1;$i<=$page['count'];$i++)
                array_push($n,array('page'=>$i,'url'=>$page['url'].'?page='.$i.$page["search"]));
        }
        return $n;
    }
    /**
     * @access public
     * @contact 析构函数：数据库链接释放
     */
    function __destruct()
    {
        $this->Connect = null;
    }
}