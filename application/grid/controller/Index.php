<?php
namespace app\grid\controller;

class Index extends Base
{
    public function index()
    {
        return $this->fetch('index');
    }

    public function get_member()
    {
        $sql = "select 
                    ID,
                    MEMB_BH,
                    MEMB_MC,
                    MEMB_LEVEL,
                    MEMB_SEX,
                    to_char(MEMB_BDAY,'yyyy-mm-dd') AS MEMB_BDAY,
                    MEMB_PHONE,
                    MEMB_BZ,
                    MEMB_STATUS 
                from t_ds_member
                ORDER BY MEMB_BH,MEMB_MC";
        $res = self::$db->execFetchAll($sql,'select member');
        return $res;
    }

    public function create_member()
    {
        $get_models = input('get');
        var_dump($get_models);
    }
}