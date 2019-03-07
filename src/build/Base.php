<?php
/**
 * Created by PhpStorm.
 * User: 南丞
 * Date: 2019/3/6
 * Time: 17:19
 *
 *
 *                      _ooOoo_
 *                     o8888888o
 *                     88" . "88
 *                     (| ^_^ |)
 *                     O\  =  /O
 *                  ____/`---'\____
 *                .'  \\|     |//  `.
 *               /  \\|||  :  |||//  \
 *              /  _||||| -:- |||||-  \
 *              |   | \\\  -  /// |   |
 *              | \_|  ''\---/''  |   |
 *              \  .-\__  `-`  ___/-. /
 *            ___`. .'  /--.--\  `. . ___
 *          ."" '<  `.___\_<|>_/___.'  >'"".
 *        | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *        \  \ `-.   \_ __\ /__ _/   .-` /  /
 *  ========`-.____`-.___\_____/___.-`____.-'========
 *                       `=---='
 *  ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 *           佛祖保佑       永无BUG     永不修改
 *
 */

namespace pf\view\build;

use houdunwang\middleware\Middleware;
use pf\arr\PFarr;
use pf\config\Config;
use pf\request\Request;

class Base
{
    use Cache, Compile;
    protected $file; //模板文件
    protected $path; //模板目录
    protected static $vars = []; //模板变量集合

    public function instance()
    {
        return new self();
    }

    public function make($file = '', $vars = [])
    {
        $this->setFile($file);
        $this->with($vars);
        return $this;
    }

    public function fetch($file = '', $vars = [])
    {
        return $this->make($file, $vars)->parse();
    }

    protected function setFile($file)
    {
        if ($file && !preg_match('/\.[a-z]+$/i', $file)) {
            $file .= Config::get('view.prefix');
        }
        if (strstr($file, '/') && is_file($file)) {
            $this->file = $file;
        } else {
            $file = $this->path . '/' . $file;
            if (is_file($file)) {
                $this->file = $file;
            }
        }
    }

    protected function with($vars, $value = '')
    {
        self::setVars($vars, $value);
        return $this;
    }

    public static function setVars($vars, $value = '')
    {
        $vars = is_array($vars) ? $vars : [$vars => $value];
        foreach ($vars as $k => $v) {
            self::$vars = PFarr::pf_set(self::$vars, $k, $v);
        }
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getVars()
    {
        return self::$vars;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        $open = Request::get('_cache', 1);
        if ($open && ($this->expire > 0) && ($cache = $this->getCache())) {
            return $cache;
        }
        $content = $this->parse();
        if ($open && $this->expire) {
            $this->setCache($content);
        }
        return $content;
    }

    protected function parse()
    {
        if (!is_file($this->file)) {
            trigger_error('模板文件不存在:' . $this->file, E_USER_ERROR);
        }
        $this->compile();
        ob_start();
        extract(self::getVars());
        include $this->compileFile;
        return ob_get_clean();
    }

}