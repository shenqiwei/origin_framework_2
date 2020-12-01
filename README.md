# Origin PHP Framework
Origin PHP framework 2 主要是用于解决PHP开发过程中关于公共结构功能重复编写封装等繁杂的无效行为所以编写和开发的简单程序封装结构；

Origin PHP framework 2 单一入口方式实现各应用功能访问，并利用MVC特性将程序与界面内容完全分开；

Origin PHP framework 2 使用内封装结构创建一套简单的web标签工具包，以方便开发者在不使用PHP程序结构的前提下简单实现对数据内容的展示；

Origin PHP framework 2 是基于Origin framework结构使用php7.4及以上版本语法升级出的新版本，此版本不再兼容php7.4以下所有版本    
    
<table>
    <tr>
        <th align="left">快速访问 -- menu</th>
    </tr>
    <tr>
        <td><a href="#welcome">欢迎</a> -- welcome</td>
    </tr>
    <tr>
        <td><a href="#tree">文件目录</a> -- tree</td>
    </tr>
    <tr>
        <td><a href="#basic">基础功能</a> -- basic function</td>
    </tr>
    <tr>
        <td><a href="#config">基础配置</a> -- configuration</td>
    </tr>
    <tr>
        <td><a href="#iif">web标签</a> -- include & if & for</td>
    </tr>
    <tr>
        <td><a href="#validate">对比函数</a> -- validate package</td>
    </tr>
    <tr>
        <td><a href="#dao">数据库</a> -- DAO</td>
    </tr>
    <tr>
        <td><a href="#history">历史版本</a> -- history</td>
    </tr>
</table>
    
<span id='welcome'></span>
##### 欢迎
感谢您能进一步了解origin，虽然他并不是您所期望最好的选择，不过对于您做出决定，我们感到十分荣幸！再次感谢您的支持   

<span id='tree'></span>
##### 文件目录    
<table style="border:0;">
    <tr>
        <td>#</td>
        <td colspan="4">application</td>
        <td>应用主目录，该目录由系统初始加载时生成</td>
        <td><a href="https://github.com/shenqiwei/origin_readme/tree/master/application">访问文档</a></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗common</td>
        <td colspan="2">功能函数目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗home</td>
        <td colspan="2">默认访问地址目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="2">┗classes</td>
        <td colspan="2">控制器目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="2">┗common</td>
        <td colspan="2">默认访问应用功能函数目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="2">┗template</td>
        <td colspan="2">应用视图模板目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>┗home</td>
        <td colspan="2">默认访问视图模板目录</td>
    </tr>
    <tr>
        <td>#</td>
        <td colspan="4">common</td>
        <td>功能文件目录，该目录由系统初始加载时生成</td>
        <td><a href="https://github.com/shenqiwei/origin_readme/tree/master/common/config">访问文档</a></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗config</td>
        <td colspan="2">系统配置文件目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗log</td>
        <td colspan="2">系统日志目录</td>
    </tr>
    <tr>
        <td>#</td>
        <td colspan="4">origin</td>
        <td>origin framework功能封装目录</td>
        <td><a href="https://github.com/shenqiwei/origin_readme/tree/master/origin">访问文档</a></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗library</td>
        <td colspan="2">实例函数目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗package</td>
        <td colspan="2">功能类封装目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗template</td>
        <td colspan="2">内核信息视图模板目录</td>
    </tr>
    <tr>
        <td>#</td>
        <td colspan="4">resource</td>
        <td colspan="2">web文件资源目录，该目录由系统初始加载时生成</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗public</td>
        <td colspan="2">功能文件目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>┗cache</td>
        <td colspan="3">缓存文件目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>┗font</td>
        <td colspan="3">字体目录，内核封装的验证码字体初始化位置</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>┗queue</td>
        <td colspan="3">队列缓冲文件目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>┗template</td>
        <td colspan="3">自定义模板目录</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3">┗upload</td>
        <td colspan="2">上传文件目录</td>
    </tr>
</table>

<span id='basic'></span>
##### 基础功能    
使用origin非常简单,将origin所有文件放入项目目录后，直接访问地址，origin会自动在第一次访问自动创建应用文件结构。
初始化完成后在应用文件夹（application）会出现默认访问文件目录（home），应用类对象存储格式（application/应用名/classes/类名.php）
类对象需标明命名空间，命名与文件地址名相同，但首字母大写参照classes/Index.php文件中的格式，就可以调用origin的基础功能     

Index.php文件    

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
    
index.html文件
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>{$title}</title>
        <style>
            html,body{ background-color: #115990; height: 100%; top:0; left: 0; padding: 0; margin: 0; color: #ffffff; }
            .main{ width:40rem; }
            .orange{ width: 4rem; font-size:4rem; padding:1rem 0 0 1rem; }
            .o{ background-color: #009999; border-radius: 0.5rem; filter:alpha(opacity=94); -moz-opacity:0.94; opacity:0.94; }
            .list ul li{ line-height: 1.5rem; }
            .mark{ clear:both; list-style: none; line-height: 4rem; font-size:1rem; text-align: right; }
        </style>
    </head>
    <body>
    <div class="main">
        <div class="orange"><span class="o">O</span>rigin.</div>
        <div class="list">
            <ul>
                <for operation="$welcome">
                <li>{$welcome.statement}</li>
                </for>
            </ul>
        </div>
        <div class="mark">origin framework {$year}.</div>
    </div>
    </body>
    </html>
    
`use Origin\Package\Unit`用于引用origin内部封装出口文件，使用父类集成方式      
`$this->param()`用于调用前端变量数据交换方法,方法参数（$key:变量名，$value:变量值）    
`$this->template()`用于调用前端模板视图模板文件方法，结构视图模板地址(application/应用名/template/类名/方法名.html)    
使用上述，就可以简单设计一个web主页


<span id='config'></span>
##### 基础配置    
编写无数据交互的web服务时，配置结构完全可以忽略，而配置主要达成功能是对数据库，缓存单元，session，cookie功能使用的基本控制条件进行设定。
文件存储地址（common/config/config.php）    
数据库（mysql）配置：    
>`DATA_NAME`  数据源名称，用于数据库封装调用指定配置内容
`DATA_TYPE` 选择数据库类型,当前版本支持Mysql,MariaDB,SQL server(mssql),PostgreSQL(pgsql),sqlite,Oracle,
`DATA_HOST`  服务访问地址  
`DATA_USER`  登录用户  
`DATA_PWD` 登录密码  
`DATA_PORT`  默认访问端口 mysql:3306,mariadb:3306,PostgreSQL:5432,Redis:6379,MongoDB:27017    
`DATA_DB` 访问数据库  
`DATA_P_CONNECT` 启用长连接  
`DATA_ATUO` 自动提交，默认设置为启用  
`DATA_TIMEOUT` 请求超时时间
`DATA_USE_TRANSACTION` 数据驱动类型为innodb时，事务操作设置才会生效(暂不支持)   
`DATA_USE_BUFFER` mysql是否使用memcache进行数据缓冲,默认值是0（不启用）,启用memcache需要在部署服务器上搭建memcache环境(暂时取消该功能支持)  

<span id='iif'></span>
##### web标签    
origin ver 1.0支持4种独立标签     
>include：引用     
 `<include href="src/html/page.html"/>`用于引用（resource/public）中公共模板文件,该标签内容仅用html文件引用    
 
> if：判断，功能与if逻辑结构功能一直    
 `<if condition = 'variable eq conditions_variable'>`   
 `<elseif condition = 'variable eq conditions_variable'/>`    
 `<else/>`    
 `</if>`    
 
> for：循环    
 `<for operation = 'variable to circulation_count'>`    
 `</for>`    
 
> foreach：迭代循环     
 `<foreach operation = 'variable (as mark_variable)'>`    
 `</foreach>`    

<span id='validate'></span>
##### 对比函数   
origin功能结构中封装了3个基本比对条件(是否为空，大小，正则结构)函数，
并且将三个基本功能统一到一个基本函数(`is_true($regular,$param)` )中:    
> $regular (string)正则表达式    
> $param (string)对象参数      

origin在初期版本中预设了15个基本比对函数，由于新版本计划所以删除原有函数内容支持（但是保留了正则表达式，以帮助使用者会更好的完成开发工作）：     
> '/^[^\s\w\!\@\#\%\^\&\*\(\)\-\+\=\/\'\\"\$\:\;\,\.\<\>\`\~]+$/' 验证中文姓名方法，支持中文英文混写，也可以用来支持名字中出现单字母的名字    
> '/^([A-Za-z]+[\.\s]?)+$/' 验证英文姓名方法    
> '/^([0]{1}\d{2,3})?([^0]\d){1}\d{2,10}$/' 验证固定电话方法，支持加区号电话号码    
> '/^(800|400){1}[\-\s]?\d{4,6}[\-\s]?\d{4}$/' 验证400和800固定电话方法    
> '/^[1][3|4|5|7|8]{1}\d{9}$/' 验证移动电话号码    
> '/^([^\_\W]+[\.\-]*)+\@(([^\_\W]+[\.\-]*)+\.)+[^\_\W]{2,8}$/' 邮箱验证方法    
> '/^((http|https):\/\/)?(www.)?([\w\-]+\.)+[a-zA-z]+(\/[\w\-\.][a-zA-Z]+)*(\?([^\W\_][\w])+[\w\*\&\@\%\-=])?$/' host地址验证方法    
> '/^[\x{4e00}-\x{9fa5}]+$/u' 中文验证方法    
> '/^[^\_\d\W]+$/' 英文验证方法     
> '/^[\w\.\-\@\+\$\#\*\~\%\^\&]+$/' 验证用户名方法    
> '/^([^\_\W]+([\_\.\-\@\+\$\#\*\~\%\^\&]*))+$/' 弱密码验证方法    
> '/^([A-Z]+[a-z]+[0-9]+[\.\_\-\@\+\$\#\*\~\%\^\&]*)+$/' 强密码验证方法    
> '/^([A-Z]+[a-z]+[0-9]+[\.\_\-\@\+\$\#\*\~\%\^\&]+)+$/' 调用验证结构包，并声明验证对象   

    function is_true($regular,$param)
    {
        $_validate = new Origin\Package\Validate($param);
        return $_validate->_type($regular);
    } 
上述方法为简单验证实例，如果使用相同功能，可以参照代码格式

<span id='dao'></span>
##### 数据库    
origin ver 1.0后数据支持Mysql，MariaDB，SQL server，PostgreSQL，Sqlite，Oracle，Mongodb（基础功能支持），Redis（部分结构支持）。
功能实现封装在DB类中，使用时需预先在配置文件（common/config/config.php）中设置数据参数    

    $_mysql = DB::mysql("origin");
    $_select = $_mysql->table("member")->where(array("member_mobile"=>$mobile))->select();    
这是一个简单的mysql select 实例，也可以用更简单的写法

    $_select = $_mysql->query("select * from member where member_mobile = '{$mobile}'");

<span id='history'></span>
##### 历史版本    
2020 origin framework ver 1.0 bate online