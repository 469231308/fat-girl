<?php
/**
 * 自定义函数类
 * 添加新函数请自行添加注释
 * !import 添加函数是请先用function_exists 判断，以免与系统函数名冲突
 * @author gaoziang <oygza_zh@163.com>
 * @time 2016/10/13
 *
 *                       _ooOoo_
 *                      o8888888o
 *                      88" . "88
 *                      (| -_- |)
 *                      O\  =  /O
 *                   ____/`---'\____
 *                 .'  \\|     |//  `.
 *                /  \\|||  :  |||//  \
 *               /  _||||| -:- |||||-  \
 *               |   | \\\  -  /// |   |
 *               | \_|  ''\---/''  |   |
 *               \  .-\__  `-`  ___/-. /
 *             ___`. .'  /--.--\  `. . __
 *          ."" '<  `.___\_<|>_/___.'  >'"".
 *         | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *         \  \ `-.   \_ __\ /__ _/   .-` /  /
 *     ======`-.____`-.___\_____/___.-`____.-'======
 *                      `=---='
 *     ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 *               佛祖保佑       永无BUG
 */

/**
 *  格式化打印数据
 * @param [array|string|int|null|bool]$vars
 * @return [string]
 */
if (!function_exists('p')) {
    function p($vars = "")
    {
        if (is_bool($vars)) {
            var_dump($vars);
        } else if (is_null($vars)) {
            var_dump(NULL);
        } else {
            echo "<pre style='position:relative;z-index:1000;padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;opacity:0.9;'>" . print_r($vars, true) . "</pre>";
        }
    }
}

/**
 * 为数组生成索引
 * @param array $data
 * @param int $radix
 * @return array
 */
if (!function_exists('createIndex')) {
    function createIndex(array $data, $radix = 0)
    {
        if (empty($data)) {
            return $data;
        }
        foreach ($data as $k => $v) {
            $data[$k]['i_index'] = intval($k + 1 + $radix);
        }
        return $data;
    }
}

/**
 *
 * 单位转换
 *
 * @param int $num
 * @param int $type
 * @return float|int
 */
if (!function_exists('unitCon')) {
    function unitCon($num = 0, $type = 1)
    {
        switch ($type) {
            case 1:  //元转分
                if (!is_numeric($num) || !is_float($num)) {
                    $num = (float)$num;
                }
                $num = (float)(sprintf('%.2f', substr(sprintf("%.4f", $num), 0, -2)));
                $num = (int)($num * 100);
                return $num;
                break;
            case 2:  //分转元
                if (!is_numeric($num) || !is_float($num)) {
                    $num = (float)$num;
                }
                $num = (float)(sprintf('%.2f', substr(sprintf("%.4f", $num), 0, -2)));
                $num = $num / 100;
                return $num;
                break;
        }
    }
}



/**
 * 文件上传类
 *
 * @param array $files //$_FILES 直接传入
 * @param string $ext_name //$_FILES 的key
 * @param string $dir //文件格式  准许上传的文件格式
 * @return string $url_path //oss 上文件路径
 *
 * [暂不明确上传位置 ]
 *
 */

if (!function_exists('upload_file')) {
    function upload_file($files = [], $ext_name, $dir = 'image', $opt = 'upload')
    {

        //文件最终返回地址
        $url_path = "";

        //文件保存目录路径
        $save_path = SYS_PATH . "/uploads";  //绝对路径

        //创建存放文件夹
        if (@is_dir($save_path) === false) {
            $pathArray = explode("/", $save_path);
            $tmpPath = array_shift($pathArray);
            foreach ($pathArray as $val) {
                $tmpPath .= "/" . $val;
                if (is_dir($tmpPath)) {
                    continue;
                } else {
                    @mkdir($tmpPath, 0777);
                }
            }
            if (@is_dir($save_path) === false) {
                return json_encode(['code' => 10010, 'msg' => '文件操作失败。', 'data' => []]);
            }
        }
        //最大文件大小
        $max_size = 1024 * 1024 * 3;

        //最终提交数组
        $data = [];

        //定义允许上传的文件名
        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
        );

        //返回格式
        $r = [];

        //检测PHP文件上传失败
        if (!empty($files[$ext_name]['error'])) {
            switch ($files[$ext_name]['error']) {
                case '1':
                    $error = '超过php . ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '不明飞行物。';
            }
            $r = ['code' => '10001', 'msg' => $error, 'data' => []];
            return json_encode($r);
        }

        //有文件上传
        if (empty($files) === false) {
            //原文件名
            $file_name = $files[$ext_name]['name'];
            //服务器上临时文件名
            $tmp_name = $files[$ext_name]['tmp_name'];
            //文件大小
            $file_size = $files[$ext_name]['size'];

            //检查文件
            if (!$file_name) {
                return json_encode(['code' => 10002, 'msg' => '文件为空', 'data' => []]);
            }
            //检查文件大小
            if ($file_size > $max_size) {
                return json_encode(['code' => 10003, 'msg' => '上传文件大小超过限制。', 'data' => []]);
            }
            //获得文件扩展名
            $temp_arr = explode(".", $file_name);
            $file_ext = array_pop($temp_arr);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);

            //检查目录名
            $dir_name = trim($dir);
            if (empty($ext_arr[$dir_name])) {
                return json_encode(['code' => 10004, 'msg' => '目录名不正确', 'data' => []]);
            }

            //检查目录写入权限
            if (@is_writable($save_path) === false) {
                return json_encode(['code' => 10006, 'msg' => '上传目录没有写权限', 'data' => []]);
            }

            //检查是否已上传
            if (@is_uploaded_file($tmp_name) === false) {
                return json_encode(['code' => 10007, 'msg' => '文件上传失败，临时文件夹权限出现问题', 'data' => []]);
            }

            //检查扩展名
            if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
                return json_encode(['code' => 10005, 'msg' => '上传文件扩展名不准许出现', 'data' => []]);
            }

            //更改文件名
            $new_file_name = rand_str(4) . time() . rand(10000, 99999) . "." . $file_ext;
            //文件地址转移
            $file_path = $save_path . "/" . $new_file_name;

            if (move_uploaded_file($tmp_name, $file_path) === false) {
                return json_encode(['code' => 10008, 'msg' => '上传文件失败', 'data' => []]);
            }

            return json_encode(['code' => 200, 'msg' => '上传成功', 'data' => str_replace(SYS_PATH,'',$file_path )]);

        }
    }
}

/**
 * 随机生成字符串
 *
 * 根据传入长度 生成该长度的随机字符串
 * 可用于验证码生成 文件命名
 * 如果不传则默认为4为随机字符串
 * @param $length
 * @return string
 */
if (!function_exists('rand_str')) {
    function rand_str($length = 4)
    {
        $string = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $max = strlen($chars) - 1;
        if (version_compare(PHP_VERSION, '4.2.0') >= 0) {
            for ($i = 0; $i < $length; $i++) {
                $p = rand(0, $max);
                $string .= $chars[$p];
            }
        } else {
            mt_srand((double)microtime(true) * 1000000);
            for ($i = 0; $i < $length; $i++) {
                $p = mt_rand(0, $max);
                $string .= $chars[$p];
            }
        }
        return $string;
    }
}


/**
 * 数组转xml
 * @param $arr
 * @return string
 */
if (!function_exists('arrayToXml')) {
    function arrayToXml($arr)
    {
        if (!is_array($arr))
            return $arr;
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val))
                $xml.="<".$key.">".$val."</".$key.">";
            else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }
}


/**
 * XML转数组
 * @param $xml
 * @return mixed
 */
if (!function_exists('xmlToArray')) {
    function xmlToArray($xml) {
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }
}



/**
 * 友好时间显示
 * @param $sTime
 * @param string $type
 * @param string $alt
 * @return false|string
 */
if (!function_exists('friendlyDate')) {
    function friendlyDate($sTime)
    {
        if (!$sTime)
            return '';
        $sTime = strtotime($sTime);

        $cTime = time();
        $dTime = $cTime - $sTime;
        $dDay = intval(date("z", $cTime)) - intval(date("z", $sTime));
        $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));

        $r = '';
        if ($dYear === 0) {
            //今年
            switch ($dDay) {
                case 0:
                    $r = '今天';
                    break;
                case 1:
                    $r = '昨天';
                    break;
                case -1:
                    $r = '明天';
                    break;
                default:
                    break;
            }
            if (!in_array($dDay, [0, 1, -1]) && $dDay > 0) {
                $r = $dDay . '天前';
            } elseif (!in_array($dDay, [0, 1, -1]) && $dDay < 0) {
                $r = abs($dDay) . '天后';
            }
        } elseif ($dYear > 0) {
            $r = $dYear . '年前';
        } elseif ($dYear < 0) {
            $r = abs($dYear) . '年后';
        }
        return $r;
    }
}

/**
 * 验证手机号是否正确
 * 仅支持中国大陆11位手机号
 * 移动：134、135、136、137、138、139、150、151、152、157、158、159、182、183、184、187、188、178(4G)、147(上网卡)；
 * 联通：130、131、132、155、156、185、186、176(4G)、145(上网卡)；
 * 电信：133、153、180、181、189 、177(4G)；
 * 卫星通信：1349
 * 虚拟运营商：170
 * @author lan
 * @param string $mobile
 * @return bool
 */
if (!function_exists('isMobile')) {
    function isMobile($mobile = '')
    {
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }
}


/**
 * 匹配中文字符 或 英文字母 或 数字
 * @param string $mobile
 * @return bool
 */
if (!function_exists('isChinese')) {
    function isChinese($str = '')
    {
        return preg_match('/^([0-9a-zA-Z]|[\x{4e00}-\x{9fa5}])+$/u', $str) ? true : false;
    }
}

/**
 * 获取用户IP
 * @return array|false|string
 */
if (!function_exists('GetIP')) {
    function GetIP()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = "unknown";
        return ($ip);
    }
}

/**
 * 根据经纬度计算两点距离
 * @param $lat_a
 * @param $lng_a
 * @param $lat_b
 * @param $lng_b
 * @return float
 */
if(!function_exists('getDistance')){
    function getDistance($lat_a,$lng_a,$lat_b,$lng_b)
    {
        $earthRadius = 6367000;
        $lat1 = ($lat_a * pi() ) / 180;
        $lng1 = ($lng_a * pi() ) / 180;
        $lat2 = ($lat_b * pi() ) / 180;
        $lng2 = ($lng_b * pi() ) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance);
    }
}


/**
 * 返回json响应头及其数据
 *
 * @param string code 		非http响应状态码, 业务处理响应码
 * @param string message 	响应码对应错误消息
 * @param string data 		响应数据主体
 * @param ing    total      条数总计
 */

function responseJson($code, $message = '', $data = '',$total='')
{
    header( 'Content-Type: application/json' );
    $data = $data == '' ? new stdClass() : $data;
    $body = array('error_code' => $code, 'error_msg' => $message, 'data' => $data);
    if (!empty($total))
        $body['total'] = (int)$total;
    exit(json_encode($body));
}

/**
 * 获取域名
 * @return string
 */
if(!function_exists('getDomain')) {
    function getDomain()
    {
        $domain = $_SERVER['HTTP_HOST'];
        if(FALSE == strpos($domain,'http') && FALSE == strpos($domain,'https'))
            $domain =  'http://'.$domain;

        return $domain;
    }
}

/**
 * 判断是否为时间戳
 * @param int $timestamp
 * @return bool
 */
if(!function_exists('isTimestamp')) {
    function isTimeStamp($timestamp = 0)
    {
        return strtotime(date('Y-m-d H:i:s',(int)$timestamp)) === (int)$timestamp;
    }
}

/**
 * 创建订单号
 * 规则 : 1位订单类型 + 4位随机数 + 3为时期 + 6位时间戳 + 4位用户uid
 * @param int $type 订单类型 1:充值 2:购买
 * @param int $uid  用户uid
 */
if ( !function_exists('createOrderNo')) {
    function createOrderNo($type,$uid)
    {
        $param_uid = strlen($uid);
        if ($param_uid > 4) {
            $uid = substr($uid,-4);
        } else if ($param_uid < 4) {
            $tmp = 4 - (int)$param_uid;
            $temp = '';
            for ($i=0;$i<$tmp;$i++) {
                $temp .= '0';
            }
            $uid = $temp.$uid;
        }
        return (int)$type.rand(1111,9999).date('Ymd').substr(time(),-6).(int)$uid;
    }
}