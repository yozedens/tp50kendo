<?php
namespace app\grid\controller;

class Curd extends Base
{
    public function index()
    {
        return $this->fetch('index');
    }

    public function read()
    {

        $sql        = "select 
                            ID,
                            MEMB_BH,
                            MEMB_MC,
                            FK_DP_ID,
                            MEMB_LEVEL,
                            to_char(MEMB_BDAY,'yyyy-mm-dd') as MEMB_BDAY,
                            IS_ONLINE
                        from T_DS_MEMBER
                        ORDER BY MEMB_BH,MEMB_MC";
        $res        = self::$db->execFetchAll($sql,'select member');
        return $res;
    }
    public function read_ed1()
    {
        $tblname    = input('get.tblname');
        $post_col_list   = input('get.col_list');
        $order_list = input('get.order_list');

        $sql_tbl_tr = "select 
                            field,field_type 
                        from t_ds_table_zd 
                        where type_en=upper('$tblname')";
        $tbl_tr     = self::$db->execFetchAll($sql_tbl_tr,'select table tr'); //获取数据库表的列名及列类型
        
        $col_list   = "ID,";         //开始拼接属性列表
        foreach ($tbl_tr as $k => $v) {
            $tbl_key = $v['FIELD'];  //列名
            $col_type = $v['FIELD_TYPE'];   //列类型
            if(!empty($tbl_key)&& strpos($post_col_list,$tbl_key) && $tbl_key!='ID'){
                switch ($col_type) {  //考虑列类型为日期的情况
                    case 'DATE':      //拼接单insert语句的值列表
                        $col_list .= "to_char($tbl_key,'yyyy-mm-dd') $tbl_key, ";
                        break;
                    default:
                        $col_list .= "$tbl_key, ";
                        break;
                }
            }
        }

        $col_list   = rtrim($col_list,', ');

        $sql        = "select 
                            $col_list 
                        from $tblname
                        ORDER BY $order_list";
        $res        = self::$db->execFetchAll($sql,'select member');
        return $res;
    }
    public function read_ed2()
    {
        $tblname    = input('get.tblname');
        $order_list = input('get.order_list');

        $sql_tbl_tr = "select 
                            field,field_type 
                        from t_ds_table_zd 
                        where type_en=upper('$tblname')";
        $tbl_tr     = self::$db->execFetchAll($sql_tbl_tr,'select table tr'); //获取数据库表的列名及列类型
        
        $col_list   = "ID,";         //开始拼接属性列表
        foreach ($tbl_tr as $k => $v) {
            $tbl_key = $v['FIELD'];  //列名
            $col_type = $v['FIELD_TYPE'];   //列类型
            if(!empty($tbl_key)&&$tbl_key!='ID'){
                switch ($col_type) {  //考虑列类型为日期的情况
                    case 'DATE':      //拼接单insert语句的值列表
                        $col_list .= "to_char($tbl_key,'yyyy-mm-dd') $tbl_key, ";
                        break;
                    default:
                        $col_list .= "$tbl_key, ";
                        break;
                }
            }
        }

        $col_list   = rtrim($col_list,', ');

        $sql        = "select 
                            $col_list 
                        from $tblname
                        ORDER BY $order_list";
        $res        = self::$db->execFetchAll($sql,'select member');
        return $res;
    }

    public function update()
    {
        $get_models = input('get.models');
        $get_list   = json_decode($get_models);//获取更新的数据

        $tblname    = input('get.tblname');//获取表名
        $sql_tbl_tr = "select 
                            field,field_type 
                        from t_ds_table_zd 
                        where type_en=upper('$tblname')";
        $tbl_tr     = self::$db->execFetchAll($sql_tbl_tr,'select table tr'); //获取数据库表的列名及列类型
        
        $sql_pro_update = "begin ";  //开始构造update过程

        foreach ($get_list as $key => $value) {
            $value = (array)$value;   //object转换为array
            $sql_update = "update $tblname set ";   //开始拼接一个update语句
            foreach ($tbl_tr as $k => $v) {
                $tbl_key = $v['FIELD'];        //列名
                $col_type = $v['FIELD_TYPE'];  //列类型
                if(!empty($tbl_key)&&isset($value[$tbl_key])&&$tbl_key!='ID'){
                    //bool值的处理还有问题
                    switch ($col_type) {   //考虑列类型为日期的情况
                        case 'DATE':
                            $sql_update .= "$tbl_key = to_date('$value[$tbl_key]','yyyy-mm-dd'), ";
                            break;
                        default:
                            $sql_update .= "$tbl_key = '$value[$tbl_key]', ";
                            break;
                    }
                }
            }
            $sql_update = rtrim($sql_update,', ');  //处理拼接字符串最后的逗号和空格
            $sql_update .= " where id=$value[ID]; ";//拼接最后的where语句
            
            $sql_pro_update .= $sql_update;  //将单update语句拼接到begin end中      
        }

        $sql_pro_update .= " end;";  //结束拼接begin end过程
        echo $sql_pro_update;
        // self::$db->execute($sql_pro_update,"update $tblname");
    }

    public function destroy()
    {
        $get_models = input('get.models');
        $get_list   = json_decode($get_models);

        $tblname    = input('get.tblname');

        $sql_detele = "delete from $tblname where id in ( ";
        foreach ($get_list as $key => $value) {
            $value  = (array)$value;
            
            $sql_detele .= $value['ID'] .", ";           
        }
        $sql_detele = rtrim($sql_detele,', ');
        $sql_detele .= " )";

        echo $sql_detele;
        // self::$db->execute($sql_detele,"delete from  $tblname");
    }

    public function create()
    {
        $get_models = input('get.models');
        $get_list   = json_decode($get_models);

        $tblname    = input('get.tblname');
        $sql_tbl_tr = "select 
                            field,field_type 
                        from t_ds_table_zd 
                        where type_en=upper('$tblname')";
        $tbl_tr     = self::$db->execFetchAll($sql_tbl_tr,'select table tr');

        $sql_pro_insert = "begin ";     //开始构造insert过程
        foreach ($get_list as $key => $value) {
            $value = (array)$value;     //object转换为array
            if($value['ID']==0||trim($value['ID'],' ')==""){ //开始拼接一个insert语句
                $col_list = "";         //开始拼接单insert语句的属性列表
                $col_val_list = "";     //开始拼接单insert语句的值列表
                foreach ($tbl_tr as $k => $v) {
                    $tbl_key = $v['FIELD'];  //列名
                    $col_type = $v['FIELD_TYPE'];   //列类型
                    if(!empty($tbl_key)&&isset($value[$tbl_key])&&$tbl_key!='ID'){
                        $col_list .= "$tbl_key, ";  //拼接单insert语句的属性列表
                        switch ($col_type) {  //考虑列类型为日期的情况
                            case 'DATE':      //拼接单insert语句的值列表
                                $col_val_list .= "to_date('$value[$tbl_key]','yyyy-mm-dd'), ";
                                break;
                            default:
                                $col_val_list .= "'$value[$tbl_key]', ";
                                break;
                        }
                    }
                }
                //拼接单insert语句
                $sql_insert  = "insert into $tblname ( ";
                
                $sql_insert .= rtrim($col_list,', ');

                $sql_insert .= ") values (";

                $sql_insert .= rtrim($col_val_list,', ');

                $sql_insert .= "); ";
                //将单insert语句拼接到begin end中
                $sql_pro_insert .= $sql_insert;
            }
        }

        $sql_pro_insert .= " end;";//结束拼接begin end过程
        echo $sql_pro_insert;
        // self::$db->execute($sql_pro_insert,"insert into $tblname");
    }
}