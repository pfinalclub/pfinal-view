<?php
/**
 * Created by PhpStorm.
 * User: 南丞
 * Date: 2019/3/7
 * Time: 10:33
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

use pf\config\Config;

trait Cache
{
    //缓存时间
    protected $expire;

    public function setExpire($expire)
    {
        $this->expire = $expire;
        return $this;
    }

    public function cacheName()
    {
        return md5($_SERVER['REQUEST_URI'] . $this->getFile());
    }

    public function isCache()
    {
        $dir = Config::get('view.cache_dir');
        return \pf\cache\Cache::driver('file')->dir($dir)->get($this->cacheName());
    }

    public function setCache($content)
    {
        $dir = Config::get('view.cache_dir');
        return \pf\cache\Cache::driver('file')->dir($dir)->set($this->cacheName(), $content, $this->expire);
    }

    public function delCache($file = '')
    {
        $dir = Config::get('view.cache_dir');
        return \pf\cache\Cache::driver('file')->dir($dir)->del($this->cacheName($file));
    }


}