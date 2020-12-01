<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin框架验证码封装结构
 */
namespace Origin\Package;
/**
 * 验证码主封装类
*/
class Verify
{
    /**
     * @access private
     * @var int $Width 画布宽度
     */
    private int $Width = 120;

    /**
     * @access private
     * @var int $Height 画布高度
     */
    private int $Height = 50;

    /**
     * @access private
     * @var int $WidthFold 初始宽度差值
     */
    private int $WidthFold = 1;

    /**
     * @access private
     * @var int $HeightFold 初始高度差值
    */
    private int $HeightFold = 1;

    /**
     * @access public
     * @param int $width 画布宽度
     * @param int $height 画布高度
     * @context 构造方法，对参数结构进行预设
    */
    function __construct(int $width=120,int $height=50)
    {
        $this->WidthFold = round($width/$this->Width, 2);
        $this->HeightFold = round($height/$this->Height, 2);
        $this->Width = $width;
        $this->Height = $height;
    }

    /**
     * @access public
     * @param string $data 传入值
     * @context 字母混合验证码主执行方法
     */
    private function execute(string $data)
    {
        # 创建图形对象变量
        $_image = imagecreate($this->Width, $this->Height);
        # 设置图像背景
        $_bg = imagecolorallocate($_image, 255, 255, 255);
        # 填充画板
        imagefill($_image, 0, 0, $_bg);
        # 创建随机参数项变量
        $_var = '';
        for($_i=0; $_i<4; $_i++){
            # 设置字体大小
            $_font_size = $this->WidthFold * 12;
            # 字体随机颜色
            $_font_color = imagecolorallocate($_image, rand(0,200), rand(0,200), rand(0,200));
            # 设置参数字符串
            $_rand_data = $data;
            # 获取对象字符
            $_obj = substr($_rand_data,rand(0, strlen($_rand_data)-1), 1);
            # 拼接对象信息参数
            $_var .= $_obj;
            # 设置坐标信息X轴，Y轴
            $_X = ($_i*100/4)*$this->WidthFold+rand(15 * $this->WidthFold, 25 * $this->WidthFold);
            $_Y = rand(15 * $this->HeightFold, 40 * $this->HeightFold);
            $_bool = rand(0,1);
            if($_bool > 0) $_obj = strtoupper($_obj);
            $_font_family = replace(RESOURCE_PUBLIC.'/font/origin001.ttf');
            $_font_angle = rand(-60,60);
            imagettftext($_image, $_font_size,$_font_angle, $_X, $_Y, $_font_color, $_font_family, $_obj);
        }
        # 增加斑点
        for($_i=0; $_i<(rand(200, 300) * $this->WidthFold); $_i++){
            # 设置点颜色
            $_point_color = imagecolorallocate($_image, rand(50,220), rand(50,220), rand(50,220));
            # 创建斑点图像
            imagesetpixel($_image, rand(0, 120 * $this->WidthFold), rand(0,50 * $this->HeightFold), $_point_color);
        }
        # 增加横线
        for($_i=0; $_i<rand(3,6); $_i++){
            # 设置线颜色
            $_line_color = imagecolorallocate($_image, rand(100,230), rand(100,230), rand(100,230));
            # 创建线图像
            imageline($_image, rand(5 * $this->WidthFold,115 * $this->WidthFold), rand(5 * $this->HeightFold,45 * $this->HeightFold),
                rand(5 * $this->WidthFold,115 * $this->WidthFold), rand(5 * $this->HeightFold, 45 * $this->HeightFold), $_line_color);
        }
        # 将对想信息存入session会话中
        $_session = new Session();
        $_session->set('Verify', $_var);
        # 设置页面输出格式
        header('Content-Type: image/png');
        # 以png形式输出图形信息
        imagepng($_image);
        imagedestroy($_image);
    }

    /**
     * @access public
     * @context 字母数字混合验证码主执行方法
     */
    function main()
    {
        $this->execute('abcdefghijklmnopqrstuvwxdyz123456789');
    }

    /**
     * @access public
     * @context 数字验证码主执行方法
     */
    function number()
    {
        $this->execute('0123456789');
    }

    /**
     * @access public
     * @context 字母验证码主执行方法
     */
    function letter()
    {
        $this->execute('abcdefghijklmnopqrstuvwxdyz');
    }

    /**
     *
     * @access public
     * @context 汉字验证码主执行方法
    */
    function han()
    {
        # 创建一个对象变量
        $_image = imagecreate($this->Width, $this->Height);
        # 设置图像背景
        $_bg = imagecolorallocate($_image, 255, 255, 255);
        # 填充画板
        imagefill($_image, 0, 0, $_bg);
        # 创建随机参数项变量
        $_var = '';
        for($_i=0; $_i<rand(2,4); $_i++){
            # 设置字体大小
            $_font_size = $this->WidthFold * 15;
            # 字体随机颜色
            $_font_color = imagecolorallocate($_image, rand(0,200), rand(0,200), rand(0,200));
            # 设置参数字符串
            $_rand_data = '一乙二十丁厂七卜人入八九几儿了力乃刀又三于干亏士工土才寸下大丈与万上小口巾山千乞川亿个勺久凡及'
            .'夕丸么广亡门义之尸弓己已子卫也女飞刃习叉马乡丰王井开夫天无元专云扎艺木五支厅不太犬区历尤友匹车巨牙屯比互切瓦止'
            .'少日中冈贝内水见午牛手毛气升长仁什片仆化仇币仍仅斤爪反介父从今凶分乏公仓月氏勿欠风丹匀乌凤勾文六方火为斗忆订计'
            .'户认心尺引丑巴孔队办以允予劝双书幻玉刊示末未击打巧正扑扒功扔去甘世古节本术可丙左厉右石布龙平灭轧东卡北占业旧帅'
            .'归且旦目叶甲申叮电号田由史只央兄叼叫另叨叹四生失禾丘付仗代仙们仪白仔他斥瓜乎丛令用甩印乐句匆册犯外处冬鸟务包饥'
            .'主市立闪兰半汁汇头汉宁穴它讨写让礼训必议讯记永司尼民出辽奶奴加召皮边发孕圣对台矛纠母幼丝式刑动扛寺吉扣考托老执'
            .'巩圾扩扫地扬场耳共芒亚芝朽朴机权过臣再协西压厌在有百存而页匠夸夺灰达列死成夹轨邪划迈毕至此贞师尘尖劣光当早吐吓'
            .'虫曲团同吊吃因吸吗屿帆岁回岂刚则肉网年朱先丢舌竹迁乔伟传乒乓休伍伏优伐延件任伤价份华仰仿伙伪自血向似后行舟全会'
            .'杀合兆企众爷伞创肌朵杂危旬旨负各名多争色壮冲冰庄庆亦刘齐交次衣产决充妄闭问闯羊并关米灯州汗污江池汤忙兴宇守宅字'
            .'安讲军许论农讽设访寻那迅尽导异孙阵阳收阶阴防奸如妇好她妈戏羽观欢买红纤级约纪驰巡寿弄麦形进戒吞远违运扶抚坛技坏'
            .'扰拒找批扯址走抄坝贡攻赤折抓扮抢孝均抛投坟抗坑坊抖护壳志扭块声把报却劫芽花芹芬苍芳严芦劳克苏杆杠杜材村杏极李杨'
            .'求更束豆两丽医辰励否还歼来连步坚旱盯呈时吴助县里呆园旷围呀吨足邮男困吵串员听吩吹呜吧吼别岗帐财针钉告我乱利秃秀'
            .'私每兵估体何但伸作伯伶佣低你住位伴身皂佛近彻役返余希坐谷妥含邻岔肝肚肠龟免狂犹角删条卵岛迎饭饮系言冻状亩况床库'
            .'疗应冷这序辛弃冶忘闲间闷判灶灿弟汪沙汽沃泛沟没沈沉怀忧快完宋宏牢究穷灾良证启评补初社识诉诊词译君灵即层尿尾迟局'
            .'改张忌际陆阿陈阻附妙妖妨努忍劲鸡驱纯纱纳纲驳纵纷纸纹纺驴纽奉玩环武青责现表规抹拢拔拣担坦押抽拐拖拍者顶拆拥抵拘'
            .'势抱垃拉拦拌幸招坡披拨择抬其取苦若茂苹苗英范直茄茎茅林枝杯柜析板松枪构杰述枕丧或画卧事刺枣雨卖矿码厕奔奇奋态欧'
            .'垄妻轰顷转斩轮软到非叔肯齿些虎虏肾贤尚旺具果味昆国昌畅明易昂典固忠咐呼鸣咏呢岸岩帖罗帜岭凯败贩购图钓制知垂牧物'
            .'乖刮秆和季委佳侍供使例版侄侦侧凭侨佩货依的迫质欣征往爬彼径所舍金命斧爸采受乳贪念贫肤肺肢肿胀朋股肥服胁周昏鱼兔'
            .'狐忽狗备饰饱饲变京享店夜庙府底剂郊废净盲放刻育闸闹郑券卷单炒炊炕炎炉沫浅法泄河沾泪油泊沿泡注泻泳泥沸波泼泽治怖'
            .'性怕怜怪学宝宗定宜审宙官空帘实试郎诗肩房诚衬衫视话诞询该详建肃录隶居届刷屈弦承孟孤陕降限妹姑姐姓始驾参艰线练组'
            .'细驶织终驻驼绍经贯奏春帮珍玻毒型挂封持项垮挎城挠政赴赵挡挺括拴拾挑指垫挣挤拼挖按挥挪某甚革荐巷带草茧茶荒茫荡荣'
            .'故胡南药标枯柄栋相查柏柳柱柿栏树要咸威歪研砖厘厚砌砍面耐耍牵残殃轻鸦皆背战点临览竖省削尝是盼眨哄显哑冒映星昨畏'
            .'趴胃贵界虹虾蚁思蚂虽品咽骂哗咱响哈咬咳哪炭峡罚贱贴骨钞钟钢钥钩卸缸拜看矩怎牲选适秒香种秋科重复竿段便俩贷顺修保'
            .'促侮俭俗俘信皇泉鬼侵追俊盾待律很须叙剑逃食盆胆胜胞胖脉勉狭狮独狡狱狠贸怨急饶蚀饺饼弯将奖哀亭亮度迹庭疮疯疫疤姿'
            .'亲音帝施闻阀阁差养美姜叛送类迷前首逆总炼炸炮烂剃洁洪洒浇浊洞测洗活派洽染济洋洲浑浓津恒恢恰恼恨举觉宣室宫宪突穿'
            .'窃客冠语扁袄祖神祝误诱说诵垦退既屋昼费陡眉孩除险院娃姥姨姻娇怒架贺盈勇怠柔垒绑绒结绕骄绘给络骆绝绞统耕耗艳泰珠'
            .'班素蚕顽盏匪捞栽捕振载赶起盐捎捏埋捉捆捐损都哲逝捡换挽热恐壶挨耻耽恭莲莫荷获晋恶真框桂档桐株桥桃格校核样根索哥'
            .'速逗栗配翅辱唇夏础破原套逐烈殊顾轿较顿毙致柴桌虑监紧党晒眠晓鸭晃晌晕蚊哨哭恩唤啊唉罢峰圆贼贿钱钳钻铁铃铅缺氧特'
            .'牺造乘敌秤租积秧秩称秘透笔笑笋债借值倚倾倒倘俱倡候俯倍倦健臭射躬息徒徐舰舱般航途拿爹爱颂翁脆脂胸胳脏胶脑狸狼逢'
            .'留皱饿恋桨浆衰高席准座脊症病疾疼疲效离唐资凉站剖竞部旁旅畜阅羞瓶拳粉料益兼烤烘烦烧烛烟递涛浙涝酒涉消浩海涂浴浮'
            .'流润浪浸涨烫涌悟悄悔悦害宽家宵宴宾窄容宰案请朗诸读扇袜袖袍被祥课谁调冤谅谈谊剥恳展剧屑弱陵陶陷陪娱娘通能难预桑'
            .'绢绣验继球理捧堵描域掩捷排掉堆推掀授教掏掠培接控探据掘职基著勒黄萌萝菌菜萄菊萍菠营械梦梢梅检梳梯桶救副票戚爽聋'
            .'袭盛雪辅辆虚雀堂常匙晨睁眯眼悬野啦晚啄距跃略蛇累唱患唯崖崭崇圈铜铲银甜梨犁移笨笼笛符第敏做袋悠偿偶偷您售停偏假'
            .'得衔盘船斜盒鸽悉欲彩领脚脖脸脱象够猜猪猎猫猛馅馆凑减毫麻痒痕廊康庸鹿盗章竟商族旋望率着盖粘粗粒断剪兽清添淋淹渠'
            .'渐混渔淘液淡深婆梁渗情惜惭悼惧惕惊惨惯寇寄宿窑密谋谎祸谜逮敢屠弹随蛋隆隐婚婶颈绩绪续骑绳维绵绸绿琴斑替款堪搭塔'
            .'越趁趋超提堤博揭喜插揪搜煮援裁搁搂搅握揉斯期欺联散惹葬葛董葡敬葱落朝辜葵棒棋植森椅椒棵棍棉棚棕惠惑逼厨厦硬确雁'
            .'殖裂雄暂雅辈悲紫辉敞赏掌晴暑最量喷晶喇遇喊景践跌跑遗蛙蛛蜓喝喂喘喉幅帽赌赔黑铸铺链销锁锄锅锈锋锐短智毯鹅剩稍程'
            .'稀税筐等筑策筛筒答筋筝傲傅牌堡集焦傍储奥街惩御循艇舒番释禽腊脾腔鲁猾猴然馋装蛮就痛童阔善羡普粪尊道曾焰港湖渣湿'
            .'温渴滑湾渡游滋溉愤慌惰愧愉慨割寒富窜窝窗遍裕裤裙谢谣谦属屡强粥疏隔隙絮嫂登缎缓编骗缘瑞魂肆摄摸填搏塌鼓摆携搬摇'
            .'搞塘摊蒜勤鹊蓝墓幕蓬蓄蒙蒸献禁楚想槐榆楼概赖酬感碍碑碎碰碗碌雷零雾雹输督龄鉴睛睡睬鄙愚暖盟歇暗照跨跳跪路跟遣蛾'
            .'蜂嗓置罪罩错锡锣锤锦键锯矮辞稠愁筹签简毁舅鼠催傻像躲微愈遥腰腥腹腾腿触解酱痰廉新韵意粮数煎塑慈煤煌满漠源滤滥滔'
            .'溪溜滚滨粱滩慎誉塞谨福群殿辟障嫌嫁叠缝缠静碧璃墙撇嘉摧截誓境摘摔聚蔽慕暮蔑模榴榜榨歌遭酷酿酸磁愿需弊裳颗嗽蜻蜡'
            .'蝇蜘赚锹锻舞稳算箩管僚鼻魄貌膜膊膀鲜疑馒裹敲豪膏遮腐瘦辣竭端旗精歉熄熔漆漂漫滴演漏慢寨赛察蜜谱嫩翠熊凳骡缩慧撕'
            .'撒趣趟撑播撞撤增聪鞋蕉蔬横槽樱橡飘醋醉震霉瞒题暴瞎影踢踏踩踪蝶蝴嘱墨镇靠稻黎稿稼箱箭篇僵躺僻德艘膝膛熟摩颜毅糊'
            .'遵潜潮懂额慰劈操燕薯薪薄颠橘整融醒餐嘴蹄器赠默镜赞篮邀衡膨雕磨凝辨辩糖糕燃澡激懒壁避缴戴擦鞠藏霜霞瞧蹈螺穗繁辫'
            .'赢糟糠燥臂翼骤鞭覆蹦镰翻鹰警攀蹲颤瓣爆疆壤耀躁嚼嚷籍魔灌蠢霸露囊罐';
            # 获取对象字符
            $_obj = mb_substr($_rand_data, rand(0, mb_strlen($_rand_data)), 1, 'utf-8');
            # 拼接对象信息参数
            $_var .= $_obj;
            # 设置坐标信息X轴，Y轴
            $_X = ($_i*100/4)*$this->WidthFold+rand(8 * $this->WidthFold, 20 * $this->WidthFold);
            $_Y = rand(20 * $this->HeightFold, 35 * $this->HeightFold);
            $_font_family = replace(RESOURCE_PUBLIC.'/font/origin001.ttf');
            $_font_angle = rand(-60,60);
            imagettftext($_image, $_font_size,$_font_angle, $_X, $_Y, $_font_color, $_font_family, $_obj);
        }
        # 增加斑点
        for($_i=0; $_i<(rand(200, 300) * $this->WidthFold); $_i++){
            # 设置点颜色
            $_point_color = imagecolorallocate($_image, rand(50,220), rand(50,220), rand(50,220));
            # 创建斑点图像
            imagesetpixel($_image, rand(0, 120 * $this->WidthFold), rand(0,50 * $this->HeightFold), $_point_color);
        }
        # 增加横线
        for($_i=0; $_i<rand(3,6); $_i++){
            # 设置线颜色
            $_line_color = imagecolorallocate($_image, rand(100,230), rand(100,230), rand(100,230));
            # 创建线图像
            imageline($_image, rand(5 * $this->WidthFold,115 * $this->WidthFold), rand(5 * $this->HeightFold,45 * $this->HeightFold),
                rand(5 * $this->WidthFold,115 * $this->WidthFold), rand(5 * $this->HeightFold, 45 * $this->HeightFold), $_line_color);
        }
        # 将对想信息存入session会话中
        $_session = new Session();
        $_session->set('Verify', $_var);
        # 设置页面输出格式
        header('Content-Type: image/png');
        # 以png形式输出图形信息
        imagepng($_image);
        imagedestroy($_image);
    }

    /**
     * @access public
     * @context 数学一元运算验证码
    */
    function math()
    {
        # 创建一个图形对象
        $_image = imagecreate($this->Width, $this->Height);
        # 设置背景颜色
        $_bg = imagecolorallocate($_image, 255, 255, 255);
        # 填充画板
        imagefill($_image, 0 , 0, $_bg);
        # 创建一元方程成员变量
        $_second_number = rand(1,9);
        $_symbol = array('+','-','*','/');
        $_math_symbol = $_symbol[rand(0,3)];
        # 根据不能有余数，不能有负数的原则，对运算符号进行运算值范围限定
        # 对值进行运算，并装入Session对象中
        switch($_math_symbol){
            case '/':
                $_first_symbol = $_second_number * rand(0,5);
                $_result = ($_first_symbol/$_second_number) > 0 ? strval($_first_symbol/$_second_number) : '0';
                break;
            case '*':
                $_first_symbol = rand(0,9);
                $_result = ($_first_symbol*$_second_number) > 0 ? strval($_first_symbol*$_second_number): '0';
                break;
            case '-':
                $_first_symbol = $_second_number + rand(0,9);
                $_result = ($_first_symbol-$_second_number) > 0 ? strval($_first_symbol-$_second_number) : '0';
                break;
            case '+':
            default:
            $_first_symbol = rand(0,9);
            $_result = ($_first_symbol+$_second_number) > 0 ? strval($_first_symbol+$_second_number) : '0';
            break;
        }
        $_session = new Session();
        $_session->set('Verify', $_result);
        # 创建比对数组
        $_han_lower = array('0'=>'零','1'=>'一','2'=>'二','3'=>'三','4'=>'四','5'=>'五','6'=>'六','7'=>'七','8'=>'八','9'=>'九');
        $_han_upper = array('0'=>'零','1'=>'壹','2'=>'贰','3'=>'叁','4'=>'肆','5'=>'伍','6'=>'陆','7'=>'七','8'=>'捌','9'=>'玖');
        $_han_symbol = array('+'=>'加','-'=>'减','*'=>'乘','/'=>'除');
        # 进行显示结构组装
        # 设置字体参数
        $_font_size = $this->WidthFold * 15;
        $_font_family = replace(RESOURCE_PUBLIC.'/font/origin001.ttf');
        # 设置字体颜色
        $_color = imagecolorallocate($_image, rand(50,220), rand(50,220), rand(50,220));
        if($_first_symbol < 10){
            $_num = rand(0, 2);
            if($_num == 1) $_first_symbol = $_han_lower[$_first_symbol];
            if($_num == 2) $_first_symbol = $_han_upper[$_first_symbol];
        }
        # 设置坐标信息X轴，Y轴
        $_X = rand(15 * $this->WidthFold, 20 * $this->WidthFold);
        $_Y = rand(20 * $this->HeightFold, 35 * $this->HeightFold);
        # 输出文字信息
        imagettftext($_image, $_font_size, rand(-60,60), $_X, $_Y, $_color, $_font_family, $_first_symbol);
        $_color = imagecolorallocate($_image, rand(50,220), rand(50,220), rand(50,220));
        if(rand(0,1) == 1) $_math_symbol = $_han_symbol[$_math_symbol];
        # 设置坐标信息X轴，Y轴
        $_X = rand(25,35)*$this->WidthFold+rand(8 * $this->WidthFold, 20 * $this->WidthFold);
        $_Y = rand(20 * $this->HeightFold, 35 * $this->HeightFold);
        # 输出文字信息
        imagettftext($_image, $_font_size, 0, $_X, $_Y, $_color, $_font_family, $_math_symbol);
        $_color = imagecolorallocate($_image, rand(50,220), rand(50,220), rand(50,220));
        if($_second_number < 10){
            $_num = rand(0, 2);
            if($_num == 1) $_second_number = $_han_lower[$_second_number];
            if($_num == 2) $_second_number = $_han_upper[$_second_number];
        }
        # 设置坐标信息X轴，Y轴
        $_X = rand(50,60)*$this->WidthFold+rand(8 * $this->WidthFold, 20 * $this->WidthFold);
        $_Y = rand(20 * $this->HeightFold, 35 * $this->HeightFold);
        # 输出文字信息
        imagettftext($_image, $_font_size, rand(-60,60), $_X, $_Y, $_color, $_font_family, $_second_number);
        if(rand(0, 1) == 1){
            $_color = imagecolorallocate($_image, rand(50,220), rand(50,220), rand(50,220));
            # 设置坐标信息X轴，Y轴
            $_X = 75*$this->WidthFold+rand(10 * $this->WidthFold, 20 * $this->WidthFold);
            $_Y = rand(22 * $this->HeightFold, 35 * $this->HeightFold);
            # 输出文字信息
            imagettftext($_image, $_font_size, rand(-30,30), $_X, $_Y, $_color, $_font_family, '=?');
        }
        # 增加斑点
        for($_i=0; $_i<(rand(200, 300) * $this->WidthFold); $_i++){
            # 设置点颜色
            $_point_color = imagecolorallocate($_image, rand(50,220), rand(50,220), rand(50,220));
            # 创建斑点图像
            imagesetpixel($_image, rand(0, 120 * $this->WidthFold), rand(0,50 * $this->HeightFold), $_point_color);
        }
        # 增加横线
        for($_i=0; $_i<rand(3,6); $_i++){
            # 设置线颜色
            $_line_color = imagecolorallocate($_image, rand(210,255), rand(210,255), rand(210,255));
            # 创建线图像
            imageline($_image, rand(5 * $this->WidthFold,115 * $this->WidthFold), rand(5 * $this->HeightFold,45 * $this->HeightFold),
                rand(5 * $this->WidthFold,115 * $this->WidthFold), rand(5 * $this->HeightFold, 45 * $this->HeightFold), $_line_color);
        }
        # 设置页面输出格式
        header('Content-Type: image/png');
        # 以png形式输出图形信息
        imagepng($_image);
        imagedestroy($_image);
    }
}