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

trait Compile
{
    protected $content;
    protected $compileFile;

    public function setCompileFile()
    {
        $this->compileFile = Config::get('view.compile_dir') . '/' . preg_replace('/[^\w]/', '_', $this->file) . '-' . substr(md5($this->file), 0, 5) . '.php';
        return $this->compileFile;
    }

    public function getCompileContent()
    {
        return file_get_contents($this->compileFile);
    }

    public function compile()
    {
        $this->setCompileFile();
        $status = Config::get('view.debug') || !is_file($this->compileFile) || (filemtime($this->file) > filemtime($this->compileFile));
        if ($status) {
            is_dir(dirname($this->compileFile)) or mkdir(dirname($this->compileFile), 0755, true);
            $this->content = file_get_contents($this->file);
            $this->globalParse();
            file_put_contents($this->compileFile, $this->content);
        }
        return $this;
    }

    public function globalParse()
    {
        $this->content = preg_replace('/(?<!@)\{!!(.*?)!!\}/i', '<?php echo \1?>', $this->content);
        //处理{{}} 转识体
        $this->content = preg_replace('/(?<!@)\{\{(.*?)\}\}/i', '<?php echo htmlspecialchars(\1)?>', $this->content);
        //处理@{{}}
        $this->content = preg_replace('/@(\{\{.*?\}\})/i', '\1', $this->content);
    }


}