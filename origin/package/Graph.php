<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context Origin图形封装
 */
namespace Origin\Package;

class Graph
{
    /**
     * @access protected
     * @var resource $Canvas 画布
     */
    protected $Canvas;

    /**
     * @access protected
     * @var int $CanvasWidth 画布宽度
     */
    protected int $CanvasWidth;

    /**
     * @access protected
     * @var int $CanvasHeight 画布高度
     */
    protected int $CanvasHeight;

    /**
     * @access protected
     * @var false|int|resource $Color 主背景色
     */
    protected $Color;

    /**
     * @access protected
     * @var string $Font 字体
     */
    protected string $Font;

    /**
     * @access protected
     * @var int $FontSzie 字体大小
     */
    protected int $FontSize;

    /**
     * @access protected
     * @var false|int|resource $FontColor 字体颜色
     */
    protected $FontColor;

    /**
     * @access public
     * @param int $width 画布宽度
     * @param int $height 画布高度
     * @param boolean $true 是否使用真彩创建
     * @context 设置画布
     */
    function Canvas(int $width, int $height, bool $true=false)
    {
        $this->CanvasWidth = $width;
        $this->CanvasHeight = $height;
        if($true)
            $this->Canvas = imagecreatetruecolor($width, $height);
        else
            $this->Canvas = imagecreate($width, $height);
        $this->Color = imagecolorallocate($this->Canvas, 255, 255, 255);
        $this->Font = replace(ROOT . "/resource/font/origin001.ttf");
        $this->FontSize = 10;
        $this->FontColor = imagecolorallocate($this->Canvas, 0, 0, 0);
    }

    /**
     * @access public
     * @param int $red 设置色偏值 红（0,225）默认值 225
     * @param int $green 设置色偏值 绿（0,225）默认值 225
     * @param int $blue 设置色偏值 蓝（0,225）默认值 225
     * @context 设置画布背景色
     */
    function setBgColor(int $red = 255, int $green = 255, int $blue = 255)
    {
        $this->Color = imagecolorallocate($this->Canvas, $red, $green, $blue);
    }

    /**
     * @access public
     * @param string $uri
     * @context 设置字体
     */
    function setFont(string $uri)
    {
        if (is_file($_uri = replace(ROOT . DS . $uri)))
            $this->Font = $_uri;
    }

    /**
     * @access public
     * @param int $size
     * @context 设置字体大小
     */
    function setFontSize(int $size)
    {
        if ($size > 0)
            $this->FontSize = intval($size);
    }

    /**
     * @access public
     * @param int $red 设置色偏值 红（0,225）默认值 225
     * @param int $green 设置色偏值 绿（0,225）默认值 225
     * @param int $blue 设置色偏值 蓝（0,225）默认值 225
     * @context 设置字体颜色
     */
    function setFontColor(int $red = 255, int $green = 255, int $blue = 255)
    {
        $this->FontColor = imagecolorallocate($this->Canvas, $red, $green, $blue);
    }

    /**
     * @access public
     * @param string $text
     * @param int $point_x 坐标轴x，默认值 0
     * @param int $point_y 坐标轴y，默认值 0
     * @param int|float $angle 旋转角度（0-90度） 默认值 0
     * @context 引入文字
     */
    function imText(string $text, int $point_x = 0, int $point_y = 0, int $angle = 0)
    {
        imagefttext($this->Canvas, $this->FontSize, $angle, $point_x, $point_y, $this->FontColor, $this->Font, $text);
    }

    /**
     * @access public
     * @param string $uri 图片地址（相对地址）
     * @param int $point_x 坐标轴x
     * @param int $point_y 坐标轴y
     * @param int $percent 缩小比例，相对于画布大小
     * @return boolean
     * @context 引入图片
     */
    function imPic(string $uri, int $point_x = 0, int $point_y = 0, int $percent = 100)
    {
        $_receipt = false;
        list($_width, $_height) = getimagesize($uri);
        if (is_file($_uri = replace(ROOT . DS . $uri))) {
            # 设置默认图片类型
            $_type = "jpg";
            if (strrpos(".", $uri)) {
                $_type = strtolower(substr($uri, strrpos(".", $uri) + 1));
            }
            switch ($_type) {
                case "png":
                    $_pic = imagecreatefrompng($_uri);
                    break;
                case "bmp":
                    $_pic = imagecreatefrombmp($_uri);
                    break;
                case "gif":
                    $_pic = imagecreatefromgif($_uri);
                    break;
                case "gd":
                    $_pic = imagecreatefromgd($_uri);
                    break;
                case "gd2":
                    $_pic = imagecreatefromgd2($_uri);
                    break;
                case "wbmp":
                    $_pic = imagecreatefromwbmp($_uri);
                    break;
                case "webp":
                    $_pic = imagecreatefromwebp($_uri);
                    break;
                case "xbm":
                    $_pic = imagecreatefromxbm($_uri);
                    break;
                case "xpm":
                    $_pic = imagecreatefromxpm($_uri);
                    break;
                case "jpeg":
                case "jpg":
                default:
                    $_pic = imagecreatefromjpeg($_uri);
                    break;
            }
            if (isset($_pic))
                $_receipt = imagecopyresized($this->Canvas, $_pic, $point_x, $point_y = 0, 0, 0, intval($_width * $percent / 100), intval($_height * $percent / 100), $_width, $_height);
        }
        return $_receipt;
    }

    /**
     * @access public
     * @param int $red 设置色偏值 红（0,225）默认值 225
     * @param int $green 设置色偏值 绿（0,225）默认值 225
     * @param int $blue 设置色偏值 蓝（0,225）默认值 225
     * @return false|int|resource
     * @context 设置填充颜色
     */
    function setColor(int $red = 255, int $green = 255, int $blue = 255)
    {
        return imagecolorallocate($this->Canvas, $red, $green, $blue);
    }

    /**
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param int $radius 圆半径,初始值 5
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充
     * 1.IMG_ARC_PIE 只用直线连接了起始和结束点
     * 2.IMG_ARC_CHORD 产生圆形边界，IMG_ARC_PIE 和 IMG_ARC_CHORD 是互斥的
     * 3.IMG_ARC_NOFILL 指明弧或弦只有轮廓，不填充
     * 4.MG_ARC_EDGED 指明用直线将起始和结束点与中心点相连，和 IMG_ARC_NOFILL 一起使用是画饼状图轮廓的好方法（而不用填充）
     * @return boolean
     * @context 画圆
     */
    function circle(int $point_x = 0, int $point_y = 0, int $radius = 5, $color = null, int $type = 0)
    {
        return $this->arc($point_x, $point_y, $radius, $radius, 0, 360, $color, $type);
    }

    /**
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param int $width 弧宽,默认值 5
     * @param int $height 弧高，默认值 5
     * @param int $start 起始角度，默认值 0
     * @param int $end 结束角度，默认值 360
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充
     * 1.IMG_ARC_PIE 只用直线连接了起始和结束点
     * 2.IMG_ARC_CHORD 产生圆形边界，IMG_ARC_PIE 和 IMG_ARC_CHORD 是互斥的
     * 3.IMG_ARC_NOFILL 指明弧或弦只有轮廓，不填充
     * 4.MG_ARC_EDGED 指明用直线将起始和结束点与中心点相连，和 IMG_ARC_NOFILL 一起使用是画饼状图轮廓的好方法（而不用填充）
     * @return boolean
     * @context 画弧
     */
    function arc(int $point_x = 0, int $point_y = 0, int $width = 5, int $height = 5, int $start = 0, int $end = 360, $color = null, int $type = 0)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type > 0)
            return imagefilledarc($this->Canvas, $point_x, $point_y, $width, $height, $start, $end, $_color, $type);
        else
            return imagearc($this->Canvas, $point_x, $point_y, $width, $height, $start, $end, $_color);
    }

    /**
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param int $width 宽,默认值 5
     * @param int $height 高，默认值 5
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充，1 填充
     * @return boolean
     * @context 画椭圆
     */
    function ellipse(int $point_x = 0, int $point_y = 0, int $width = 5, int $height = 5, $color = null, int $type = 0)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type > 0)
            return imagefilledellipse($this->Canvas, $point_x, $point_y, $width, $height, $_color);
        else
            return imageellipse($this->Canvas, $point_x, $point_y, $width, $height, $_color);
    }

    /**
     * @access public
     * @param array $points 坐标信息数组 array(
     *     array($point_x,$point_y),
     * )
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充，1 填充，2 非闭合多边划线
     * @return boolean
     * @context 画多边形
     */
    function polygon(array $points, $color = null, int $type = 0)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type === 1)
            return imagefilledpolygon($this->Canvas, $points, count($points), $_color);
        elseif ($type === 2)
            return imageopenpolygon($this->Canvas, $points, count($points), $_color);
        else
            return imagepolygon($this->Canvas, $points, count($points), $_color);
    }

    /**
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param int $long 边长，初始值 5
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充，1 填充
     * @return boolean
     * @context 画正方形
     */
    function square(int $point_x = 0, int $point_y = 0, int $long = 5, $color = null, int $type = 0)
    {
        return $this->rectangle($point_x = 0, $point_y = 0, $long = 5, $long = 5, $color = null, $type = 0);
    }

    /**
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param int $width 宽 初始值 5
     * @param int $height 高 初始值 5
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $type 填充状态，默认值 0 不填充，1 填充
     * @return boolean
     * @context 画矩形
     */
    function rectangle(int $point_x = 0, int $point_y = 0, int $width = 5, int $height = 5, $color = null, int $type = 0)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type > 0)
            return imagefilledrectangle($this->Canvas, $point_x, $point_y, $width, $height, $_color);
        else
            return imagerectangle($this->Canvas, $point_x, $point_y, $width, $height, $_color);
    }

    /**
     * @access public
     * @param int $start_x 定位坐标x，默认值 0
     * @param int $start_y 定位坐标y，默认值 0
     * @param int $end_x 定位坐标x，默认值 5
     * @param int $end_y 定位坐标y，默认值 5
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @return boolean
     * @context 画直线
     */
    function line(int $start_x = 0, int $start_y = 0, int $end_x = 5, int $end_y = 5, $color = null)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        return imageline($this->Canvas, $start_x, $start_y, $end_x, $end_y, $_color);
    }

    /**
     * @access public
     * @param array $style
     * @param int $start_x 定位坐标x，默认值 0
     * @param int $start_y 定位坐标y，默认值 0
     * @param int $end_x 定位坐标x，默认值 5
     * @param int $end_y 定位坐标y，默认值 5
     * @return boolean
     * @context 画虚线，需要画布创建为真彩
     */
    function dotted(array $style, int $start_x = 0, int $start_y = 0, int $end_x = 5, int $end_y = 5)
    {
        if(imagesetstyle($this->Canvas,$style))
            return imageline($this->Canvas, 0, 0, 100, 100, IMG_COLOR_STYLED);
        else
            return false;
    }

    /**
     * @access public
     * @param string $str 输入字符串
     * @param int $font 字体参数 默认值 1 （1-5）为内部字体
     * @param int $type 排列方向 0 横向，1 竖向
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @return boolean
     * @context 画字符串
     */
    function string(string $str, int $font = 1, int $type = 0, int $point_x = 0, int $point_y = 0, $color = null)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type > 0)
            return imagestring($this->Canvas, $font, $point_x, $point_y, $str, $_color);
        else
            return imagestringup($this->Canvas, $font, $point_x, $point_y, $str, $_color);
    }

    /**
     * @access public
     * @param string $str 输入字符串
     * @param int $font 字体参数 默认值 1 （1-5）为内部字体
     * @param int $type 排列方向 0 横向，1 竖向
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @return boolean
     * @context 画字符串
     */
    function char(string $str, int $font = 1, int $type = 0, int $point_x = 0, int $point_y = 0, int $color = null)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        if ($type > 0)
            return imagechar($this->Canvas, $font, $point_x, $point_y, $str, $_color);
        else
            return imagecharup($this->Canvas, $font, $point_x, $point_y, $str, $_color);
    }

    /**
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @return boolean
     * @context 画字符串
     */
    function pixel(int$point_x = 0, int $point_y = 0, $color = null)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        return imagesetpixel($this->Canvas, $point_x, $point_y, $_color);
    }

    /**
     * @access public
     * @param resource $pic 引入图片源 imPic返回值
     * @param int $type 反转类型
     * IMG_FILTER_NEGATE：将图像中所有颜色反转。
     * IMG_FILTER_GRAYSCALE：将图像转换为灰度的。
     * IMG_FILTER_BRIGHTNESS：改变图像的亮度。用 arg1 设定亮度级别。
     * IMG_FILTER_CONTRAST：改变图像的对比度。用 arg1 设定对比度级别。
     * IMG_FILTER_COLORIZE：与 IMG_FILTER_GRAYSCALE 类似，不过可以指定颜色。用 arg1，arg2 和 arg3 分别指定 red，blue 和 green。每种颜色范围是 0 到 255。
     * IMG_FILTER_EDGEDETECT：用边缘检测来突出图像的边缘。
     * IMG_FILTER_EMBOSS：使图像浮雕化。
     * IMG_FILTER_GAUSSIAN_BLUR：用高斯算法模糊图像。
     * IMG_FILTER_SELECTIVE_BLUR：模糊图像。
     * IMG_FILTER_MEAN_REMOVAL：用平均移除法来达到轮廓效果。
     * IMG_FILTER_SMOOTH：使图像更柔滑。用 arg1 设定柔滑级别。
     * @return boolean
     * @context 图片渲染（反转）
     */
    function filter($pic, int $type)
    {
        return imagefilter($pic,$type);
    }

    /**
     * @access public
     * @param resource $pic 引入图片源 imPic返回值
     * @param int $start_x 定位坐标x，默认值 0
     * @param int $start_y 定位坐标y，默认值 0
     * @param int $width 宽，默认值 5
     * @param int $height 高，默认值 5
     * @param int $canvas_x 图像显示位置坐标x，默认值 0
     * @param int $canvas_y 图像显示位置坐标y，默认值 0
     * @return boolean
     * @context 图片截取
    */
    function cut($pic, int $start_x=0, int $start_y=0, int $width=5, int $height=5, int $canvas_x=0, int $canvas_y=0)
    {
        return imagecopy($this->Canvas,$pic,$start_x,$start_y,$width,$height,$canvas_x,$canvas_y);
    }

    /**
     * @access public
     * @param resource $pic 引入图片源 imPic返回值
     * @param float $angle 旋转角度 默认值 0.0
     * @param resource|int|null $color 初始值 null，默认填充色 RGB(255,255,255)
     * @param int $transparent 是否支持透明默认值 0 不支持，1支持
     * @return boolean
     * @context 旋转
    */
    function rotate($pic, float $angle=0.0, $color=null, int $transparent=0)
    {
        $_color = $this->Color;
        if (!is_null($color) and (is_resource($color) or is_int($color)))
            $_color = $color;
        return imagerotate($pic,$angle,$_color,$transparent);
    }

    /**
     * @access public
     * @param int $point_x 定位坐标x，默认值 0
     * @param int $point_y 定位坐标y，默认值 0
     * @return boolean
     * @context 设置空间填充
    */
    function fill(int $point_x=0, int $point_y=0)
    {
        return imagefill($this->Canvas, $point_x, $point_y, $this->Color);
    }

    /**
     * @access public
     * @param string $type 图片类型
     * @param string|null $uri 存储（相对，根地址：项目根目录）路径 默认值 null
     * @context 输出图像
     */
    function output(string $type="jpg", ?string $uri=null)
    {
        $_uri = null;
        if(!is_null($uri))
            $_uri = replace(ROOT.DS.$_uri);
        switch($type){
            case "png":
                imagepng($this->Canvas,$_uri);
                break;
            case "bmp":
                imagebmp($this->Canvas,$_uri);
                break;
            case "gif":
                imagegif($this->Canvas,$_uri);
                break;
            case "gd":
                imagegd($this->Canvas,$_uri);
                break;
            case "gd2":
                imagegd2($this->Canvas,$_uri);
                break;
            case "wbmp":
                imagewbmp($this->Canvas,$_uri);
                break;
            case "webp":
                imagewebp($this->Canvas,$_uri);
                break;
            case "xbm":
                imagexbm($this->Canvas,$_uri);
                break;
            default:
                imagejpeg($this->Canvas,$_uri);
                break;
        }
        imagedestroy($this->Canvas);
    }
}