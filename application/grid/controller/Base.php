<?php
namespace app\grid\controller;

class Base extends \think\Controller
{
    protected static $db = null;

    public function _initialize()
    {
        //连接数据库
		if (is_null(self::$db)) {
            self::$db = new \Org\Util\Esundb('Base','ini connect');
		}
    }
}