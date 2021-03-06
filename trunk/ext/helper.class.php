<?php 
/**
 * 助手类
 * 提供了常用的辅助功能，如操作成功提示，操作失败提示，操作错误返回，调试功能等！
 * 此类是静态类，不需要初始化就可以直接调用！
 * 例如：helper::success("恭喜你，登录成功了");
 */
class helper {

    /**
     * 调试信息
     * 根据提供的对象，数组等，打印调试信息
     * @param object $dd 需要调试的内容，可以是数组，对象和其他的
     */
     
    public static function dump($dd) {
    
        echo "<pre style=\"font-size:10pt;border:#003377 1px dashed;padding:10px;\">";
        var_dump($dd);
        echo "</pre>";
    }
    /**
     * 提示信息
     * 只需要指定信息内容，跳转地址，自动调用消息模版输出提示信息
     * @param string $msg 成功提示信息
     * @param string $url 返回跳转的url
     * @param string $enableRedirect 设置是否启用跳转，默认启用 :enableRedirect ,不启用：disableRedirect
     */
     
    public static function message($msg, $url = "", $enableRedirect = "enableRedirect") {
    
        if (! empty($url)) {
            $jumpTo = $url;
        } else {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $jumpTo = $_SERVER['HTTP_REFERER'];
            } else {
                $jumpTo = "/";
            }
            
        }
        include (zvc_path."/tpl/zvcmsg.html");
        exit(1);
    }
    /**
     * 当前导航菜单高亮显示
     * 根据Cotroller和Action来匹配URL地址
     * @param object $controller
     * @param object $action
     * @param object $activeClass [optional]
     * @return
     */
    public static function currentNav($controller, $action = null, $activeClass = "active") {
        if (!isset($action) || empty($action)) {
            $action = "index";
        }
        if (is_array($action)) {
            if ($_REQUEST["controller"] == $controller && in_array($_REQUEST["action"], $action)) {
                echo " class=\"active\" ";
            }
        } else {
            if ($_REQUEST["controller"] == $controller && $_REQUEST["action"] == $action) {
                echo " class=\"active\" ";
            }
        }
        
    }
    /**
     * 设置cookie
     * 需要提供cookie名，cookie的值,过期时间(非必须),域名(非必须)
     * @param object $name    cookie名
     * @param object $cookie    cookie的值
     * @param object $exp    cookie失效的时间
     */
    public static function zvc_set_cookie($name, $cookie, $exp = 3600) {
        setcookie($name, $cookie, time() + $exp, '/');
    }
    /**
     * 删除cookie
     * 需要提供cookie名
     * @param object $name    cookie名
     */
    public static function zvc_delete_cookie($name) {
        setcookie($name, '');
    }
    /**
     * 检查.htaccess文件是否存在，不存在，则手动给它写入
     */
    public static function checkHTAC() {
        $protected_dir = array('cache', 'config', 'controllers', 'data', 'lang', 'views', 'zvcore');
        foreach ($protected_dir as $val) {
            if (is_dir($val) && !file_exists($val."/.htaccess")) {
                $protected_put = "Deny from all ";
                file_put_contents($val."/.htaccess", $protected_put);
            }
        }
        
    }
    /**
     * 转向新的地址
     */
    public static function redirect($url = "/") {
        header('location:'.$url);
        exit(1);
    }
    /**
     * 预处理数组
     * @param object $array [optional]
     * @return
     */
    public static function preArray($array = array()) {
        foreach ($array as $key=>$val) {
            if (is_numeric($val)) {
                $array[$key] = intval($val);
            }
            if (is_string($val)) {
                $array[$key] = trim($val);
            }
            
        }
        return $array;
    }
    /**
     * 检测gbk编码，并转换至utf-8
     * @param string $string
     * @return string $string
     */
    public static function gbk2utf8($string) {
        if (mb_check_encoding($string, "gbk")) {
            return mb_convert_encoding($string, "utf8", "gbk");
        } else {
            return $string;
        }
        
    }
    /**
     * 检测utf8编码，转换至gbk
     * @param string $string
     * @return  string $string
     */
    public static function utf82gbk($string) {
        if (mb_check_encoding($string, "utf8")) {
            return mb_convert_encoding($string, "gbk", "utf8");
        } else {
            return $string;
        }
    }
    /**
     * Tracer错误信息
     * @param object $e
     * @return
     */
    public static function traceOut(Exception $e) {
        if (!file_exists('./config/global.php')) {
            self::throwTrace($e);
        } else {
            $global_setting = registry::getRegistry('global');
            
            switch ($global_setting['run_mode']) {
                case 'product':
                    self::message($e->getMessage(), null, 'disableRedirect');
                    break;
                case 'dev':
                    self::throwTrace($e);
                    break;
            }
            
        }
    }
    /**
     * 打印出错信息
     * @param object $e
     * @return
     */
    public static function throwTrace(Exception $e) {
        echo '<html><head><title>出错啦！</title></head><body>';
        echo "<h3>出错信息:</h3><pre>";
        echo $e->getMessage()."&nbsp;(出错代码:".$e->getCode().")";
        echo "</pre><h3>出错位置:</h3><pre>";
        echo $e->getFile()."&nbsp;";
        echo "第".$e->getLine()."行</h3>";
        echo "</pre><h3>出错trace信息</h3><pre>";
        echo $e->getTraceAsString();
        echo "</pre><h3>REQUEST信息</h3><pre>";
        echo self::dump($_REQUEST);
        echo "</pre><h3>POST信息</h3><pre>";
        echo self::dump($_POST);
        echo '</pre></body></html>';
    }
}
