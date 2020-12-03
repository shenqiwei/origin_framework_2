<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context Origin通信封装
 */
namespace Origin\Package;

class Socket
{
    /**
     * @acceess protected
     * @var resource $Socket 套字节源
     */
    protected $Socket;

    /**
     * @acceess protected
     * @var string $IP IP地址信息
     */
    protected string $IP;

    /**
     * @acceess protected
     * @var string $IPType IP类型
     */
    protected string $IPType;

    /**
     * @acceess protected
     * @var int $IPDomain 访问域名类型
     */
    protected int $IPDomain;

    /**
     * @acceess protected
     * @var int $Port 端口号
     */
    protected int $Port;

    /**
     * @acceess protected
     * @var int $ErrCode 错误代码
     */
    protected int $ErrCode;

    /**
     * @acceess protected
     * @var string|null $Error 错误信息
    */
    protected ?string $Error=null;

    /**
     * @access public
     * @param string $ip ip地址（ipv4|ipv6）
     * @param int $port 端口号，默认值 0 <无效端口号>，1-1024服务端口<非必要，请勿占用>，1025-65535<可定义端口>
     * @return void
     * @context 构造函数，预创建套字节
     */
    public function __construct(string $ip, int $port=0)
    {
        $this->IP = $ip;
        $_validate = new Validate();
        if($_validate->_ipv4($ip)){
            $this->IPType = AF_INET;
            $this->IPDomain = 1;
        }elseif($_validate->_ipv6($ip)){
            $this->IPType = AF_INET6;
            $this->IPDomain = 2;
        }else{
            $this->IPType= AF_UNIX;
            $this->IPDomain = 0;
        }
        if(intval($port) > 0)
            $this->Port = intval($port);
    }

    /**
     * @access public
     * @return void
     * @context 创建TCP连接
    */
    public function tcp()
    {
        $this->Socket = socket_create($this->IPType,SOCK_STREAM,SOL_TCP);
    }

    /**
     * @access public
     * @return void
     * @context 创建UDP连接
     */
    public function udp()
    {
        $this->Socket = socket_create($this->IPType,SOCK_DGRAM,SOL_UDP);
    }

    /**
     * @access public
     * @return void
     * @context 创建数据包连接
     */
    public function packet()
    {
        $this->Socket = socket_create($this->IPType,SOCK_SEQPACKET,0);
    }

    /**
     * @access public
     * @return void
     * @context 创建icmp连接
     */
    public function icmp()
    {
        $this->Socket = socket_create($this->IPType,SOCK_RAW,0);
    }

    /**
     * @access public
     * @return void
     * @context 创建远程部署管理连接
     */
    public function rdm()
    {
        $this->Socket = socket_create($this->IPType,SOCK_RDM,0);
    }

    /**
     * @access public
     * @return boolean
     * @context 建立连接，函数（IPV4，UNIX）默认对该连接地址进行名称绑定,若绑定失败，可以在getError函数中获取错误信息
    */
    public function connect()
    {
        if($this->IPDomain === 1 and $this->IPDomain === 0){
            socket_bind($this->Socket,$this->IP,$this->Port);
        }
        return socket_connect($this->Socket,$this->IP,$this->Port);
    }

    /**
     * @access public
     * @return resource
     * @context 获取套字节连接
    */
    public function accept()
    {
        return socket_accept($this->Socket);
    }

    /**
     * @access public
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @return boolean
     * @context 执行监听，该函数仅在使用 tcp(),packet()函数生效
    */
    public function listen(int $max=1024)
    {
        return socket_listen($this->Socket,$max);
    }

    /**
     * @access public
     * @param int $port 端口号，1-1024服务端口<非必要，请勿占用>，1025-65535<可定义端口>
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @return boolean
     * @context 标注新的监听端口，创建连接
     */
    public function newListen(int $port, int $max=1024)
    {
        return socket_create_listen($port,$max);
    }

    /**
     * @access public
     * @param int $status 状态默认值 0：非阻塞，1：阻塞
     * @return boolean
     * @context 连接阻塞状态
    */
    public function block(int $status=0)
    {
        if($status === 1)
            return socket_set_block($this->Socket);
        else
            return socket_set_nonblock($this->Socket);
    }

    /**
     * @access public
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @return string|boolean
     * @context 读取数据
    */
    public function read(int $max=1024)
    {
        return socket_read($this->Socket, $max);
    }

    /**
     * @access public
     * @param string $string 发送内容
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @return boolean
     * @context 写入数据
     */
    public function write(string $string, int $max=1024)
    {
        return socket_write($this->Socket,$string,$max);
    }

    /**
     * @access public
     * @param string $buffer 缓冲变量
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @param int $flag 获取方式，默认值 MSG_DONTWAIT
     * MSG_OOB    处理超出边界的数据
     * MSG_PEEK    从接受队列的起始位置接收数据，但不将他们从接受队列中移除。
     * MSG_WAITALL    在接收到至少 len 字节的数据之前，造成一个阻塞，并暂停脚本运行（block）。但是， 如果接收到中断信号，或远程服务器断开连接，该函数将返回少于 len 字节的数据。
     * MSG_DONTWAIT    如果制定了该flag，函数将不会造成阻塞，即使在全局设置中指定了阻塞设置。
     * @return int 返回字节数
     * @context 获取信息
     */
    public function recv(string &$buffer, int $max=1024, int $flag=MSG_DONTWAIT)
    {
        return socket_recv($this->Socket,$buffer,$max,$flag);
    }

    /**
     * @access public
     * @param string $buffer 缓冲变量，获取内容
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @param int $flag 获取方式，默认值 MSG_DONTWAIT
     * MSG_OOB    处理超出边界的数据
     * MSG_PEEK    从接受队列的起始位置接收数据，但不将他们从接受队列中移除。
     * MSG_WAITALL    在接收到至少 len 字节的数据之前，造成一个阻塞，并暂停脚本运行（block）。但是， 如果接收到中断信号，或远程服务器断开连接，该函数将返回少于 len 字节的数据。
     * MSG_DONTWAIT    如果制定了该flag，函数将不会造成阻塞，即使在全局设置中指定了阻塞设置。
     * @param string|null $addr 地址信息（ipv4|unix），默认值 null
     * @param int $port 端口号，默认值 0，1-1024服务端口<非必要，请勿占用>，1025-65535<可定义端口>
     * @return int 返回字节数
     * @context 获取信息，忽略连接状态
     */
    public function recvf(string &$buffer, int $max=1024, int $flag=MSG_DONTWAIT, ?string $addr=null, int $port=0)
    {
        return socket_recvfrom($this->Socket,$buffer, $max, $flag,$addr,$port);
    }

    /**
     * @access public
     * @param string $buffer 缓冲变量，获取内容
     * @param int $flag 获取方式，获取内容，默认值 MSG_DONTWAIT
     * MSG_OOB    处理超出边界的数据
     * MSG_PEEK    从接受队列的起始位置接收数据，但不将他们从接受队列中移除。
     * MSG_WAITALL    在接收到至少 len 字节的数据之前，造成一个阻塞，并暂停脚本运行（block）。但是， 如果接收到中断信号，或远程服务器断开连接，该函数将返回少于 len 字节的数据。
     * MSG_DONTWAIT    如果制定了该flag，函数将不会造成阻塞，即使在全局设置中指定了阻塞设置。
     * @return int 返回字节数
     * @context 获取信息
     */
    public function recvm(string &$buffer, int $flag=MSG_DONTWAIT)
    {
        return socket_recvmsg($this->Socket, $buffer,$flag);
    }

    /**
     * @access public
     * @param string $buffer 缓冲变量，获取内容
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @param int $flag 获取方式，默认值 MSG_DONTROUTE
     * MSG_OOB    发送带外数据
     * MSG_EOR    标出一个记录标记。发送的数据完成记录。
     * MSG_EOF    关闭套接字的发送方端，并在发送的数据的末尾包含相应的通知。发送的数据完成事务。
     * MSG_DONTROUTE 绕过路由，使用直接接口。
     * @return int
     * @context 发送信息
     */
    public function send(string $buffer, int $max=1024, int $flag=MSG_DONTROUTE)
    {
        return socket_send($this->Socket,$buffer,$max,$flag);
    }

    /**
     * @access public
     * @param string $buffer 缓冲变量，获取内容
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @param int $flag 获取方式，默认值 MSG_DONTROUTE
     * MSG_OOB    发送带外数据
     * MSG_EOR    标出一个记录标记。发送的数据完成记录。
     * MSG_EOF    关闭套接字的发送方端，并在发送的数据的末尾包含相应的通知。发送的数据完成事务。
     * MSG_DONTROUTE 绕过路由，使用直接接口。
     * @param string|null $addr 地址信息（ipv4|unix），默认值 null
     * @param int $port 端口号，默认值 0，1-1024服务端口<非必要，请勿占用>，1025-65535<可定义端口>
     * @return int
     * @context 发送信息,或略连接状态
     */
    public function sendf(string $buffer, int $max=1024, int $flag=MSG_DONTROUTE, ?string $addr=null, int $port=0)
    {
        return socket_sendto($this->Socket,$buffer,$max,$flag,$addr,$port);
    }

    /**
     * @access public
     * @param array $buffer 缓冲变量，获取内容
     * @param int $flag 获取方式，默认值 MSG_DONTROUTE
     * MSG_OOB    发送带外数据
     * MSG_EOR    标出一个记录标记。发送的数据完成记录。
     * MSG_EOF    关闭套接字的发送方端，并在发送的数据的末尾包含相应的通知。发送的数据完成事务。
     * MSG_DONTROUTE 绕过路由，使用直接接口。
     * @return int
     * @context 发送信息
     */
    public function sendm(array $buffer, int $flag=MSG_DONTROUTE)
    {
        return socket_sendmsg($this->Socket,$buffer,$flag);
    }

    /**
     * @access public
     * @param int $type 默认值 2，注销类型 0：注销读取行为，1：注销写入行为，2：注销全部行为
     * @return boolean
     * @context 注销行为
     */
    public function shutdown(int $type=2)
    {
        if($type === 1 or $type === 2)
            $_type = $type;
        else
            $_type = 2;
        return socket_shutdown($this->Socket,$_type);
    }

    /**
     * @access public
     * @return void
     * @context 关闭套字节请求
     */
    public function close()
    {
        socket_close($this->Socket);
    }

    /**
     * @access public
     * @return void
     * @context 清空错误信息
    */
    public function clear()
    {
        socket_clear_error($this->Socket);
    }

    /**
     * @access public
     * @return mixed
     * @context 获取错误信息
    */
    public function getError()
    {
        $this->ErrCode = socket_last_error();
        $this->Error = socket_strerror($this->ErrCode);
        return $this->Error;
    }
}