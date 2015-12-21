<?php
require "cpdo.class.php";
$pdo = new CPdo;

//通过sql语句获得值
$sql = "select * from `dede_feedback` where `id` < 20";
$res = $pdo->getValueBySelfCreateSql($sql);
echo '<pre>';
var_dump($res);