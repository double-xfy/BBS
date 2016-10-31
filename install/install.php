
<?php

@header("content-type:text/html;charset=utf8");
//可能有错


//设置默认主站目录
define('DS',DIRECTORY_SEPARATOR);
define('WEB_ROOT_2',dirname(dirname(__FILE__)).DS);


if(isset($_POST['install'])){
    $host = isset($_POST['host'])?$_POST['host']:"";
    $user = isset($_POST['user'])?$_POST['user']:"";
    $passw = isset($_POST['passw'])?$_POST['passw']:"";
    $db_name = isset($_POST['db_name'])?$_POST['db_name']:"";
    $admin = isset($_POST['admin'])?md5($_POST['admin']):"";
    $db_prefix = isset($_POST['db_prefix'])?$_POST['db_prefix']:"";


    //判断是否存在config.php文件
    //if(file_exists(WEB_ROOT.'/public/furyx/config.php'))
    //{
    //    //require_once WEB_ROOT.'/public/furyx/config.php';
    //}


    //***
    //*创建config连接数据库的信息
    //*
    //*

    //var_dump(WEB_ROOT_2);

    if(!is_writable(WEB_ROOT_2.'app'.DS.'config'.DS.'config.php')){
        echo ("配置文件不可写，请检查配置文件的权限！");
    }

    $configStr=<<<php
<?php
return array(
    'database'=>array(
        'host'=>'{$host}',
        'user'=>'{$user}',
        'passw'=>'{$passw}',
        'dbname'=>'{$db_name}',
        'db_prefix'=>'{$db_prefix}'
    ),
    'app'=>array(),
    'back'=>array(),
    'front'=>array(),
);

php;

//
// $configStr="
//            <?php\n\$
//            params= array(
//            \n'host'=>'{$host}',
//            \n'user'=>'{$user}',
//            \n'passw'=>'{$passw}',
//            \n'dbname'=>'{$db_name}',
//            \n'db_prefix'=>'{$db_prefix}'
//            \n);";



    $fp = fopen(WEB_ROOT_2.'app'.DS.'config'.DS.'config.php',"w+");
    fwrite($fp,$configStr);
    fclose($fp);

    //****创建config连接数据库的信息结束


    //********
    //*连接数据库
    //*创建数据库
    //*生成表
    //********

    //require_once WEB_ROOT.'/public/furyx/Model.class.php';
    //连接sjk
    if(@!$link=mysql_connect("{$host}",$user,$passw)){
        echo '连接数据库失败';
        die();
    }


    $sql_array = array();

    $sql_array [] = "set names 'utf8'";
    $sql_array [] = "create database {$db_name}";
    $sql_array [] = "use {$db_name}";

    $sql_array [] ="create table {$db_prefix}_user_info(
	                id int  unsigned auto_increment primary key,
	                username varchar(20) not null,
	                password varchar(50) not null,
	                power int unsigned not null,
	                score int unsigned not null,
	                email varchar(25),
	                tele varchar(25),
	                intro text
                    )engine=innodb charset=utf8;";
    /// intro text 这个地方修改





    $sql_array [] ="create table {$db_prefix}_message_board(
                    id int unsigned auto_increment primary key,
                    usernameid varchar(20) ,
                    mess text,
                    posttime date
                    )engine=innodb charset=utf8;";

//


//    $sql_array [] ="insert into {$this->db_prefix}_message_board values (null,'xfy','123456',{now()});";
//    echo "insert into {$this->db_prefix}_message_board values (null,'xfy','123456',{now()})";


    if(!$admin==''){
        $sql_array [] ="insert into {$db_prefix}_user_info values (null,'admin','{$admin}',1,100,null,null,null);";
    }


    foreach($sql_array as $value){
        if(!$result=mysql_query($value,$link)){
            echo 'sql执行失败'.'<br />';
            echo '错误语句：'.$value.'<br />';
            echo '错误代码：'.mysql_errno($link).'<br />';
            echo '错误信息：'.mysql_error($link).'<br />';
            die();
        }

    }



    //********createEND



    //********
    //*lock install
    //*
    //*
    //********

    rename(WEB_ROOT_2.'install'.DS.'install.php',WEB_ROOT_2.'install'.DS.'install.lock');
    
    echo "<script>alert('安装成功!');location.href='index.php'</script>";


    //********lock install end



}










////==================================
//include_once ("data/config.php");
//if(!@$link = mysqli_connect($host,$user,$password)){
//    echo "数据库连接失败!";
//}else{
//    echo "数据库连接成功！";
//    $createSql = "create database `$db_name`";
//    mysqli_query($link, $createSql);
//    mysqlI_select_db($link,$db_name);
//    $sql_query[] = "create table `".$db_prefix."_admin_log1`(
//                           `id` int(8) unsigned not null auto_increment key
//    );";
//    $sql_query[] = "create table `".$db_prefix."_admin_log2`(
//                           `id` int(8) unsigned not null auto_increment key
//    );";
//    foreach($sql_query as $sql){
//        mysqli_query($link,$sql);
//        //echo $sql;
//        echo "导入成功..."."<br>";
//    }
//}



?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <form name="myform" action="" method ="post" onsubmit="return check()">
        <label for="host"></label>填写主机：<input id="host" type="text" name="host" value="127.0.0.1"/><br />
        <label for="user"></label>连接数据库用户名：<input id="user" type="text" name="user" value="root" /><br />
        <label for="passw"></label>密码：<input id="passw" type="text" name="passw" /><br />
        <label for="db_name"></label>数据库名：<input id="db_name" type="text" name="db_name" /><br />
        <label for="db_prefix"></label>数据前缀：<input id="db_prefix" type="text" name="db_prefix" /><br />
        <label for="admin">是否添加超级用户（用户名和密码为admin）</label><input id="admin" type="radio" name="admin" value="admin" ><br />
        <button type="submit" name="install">提交</button>
    </form>

</body>

<script type="text/javascript">
    function check() {
        if (myform.host.value==""||myform.user.value==""||
            myform.passw.value==""||myform.db_name.value==""
            ||myform.db_prefix.value=="") {
            alert("输入错误");
            return false;
        }
        return true;
    }


</script>

</html>

