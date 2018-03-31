<?php
/**
 * Created by PhpStorm.
 * User: filipe
 * Date: 28/03/18
 * Time: 14:18
 */
include "../../openHelper/DatabaseOpenHelper.php";
$db = new Database();

$jsonDetalhes = "";
try{
	$jsonDetalhes = $db->select("id,primeiro_nome,sobrenome", "usuario", "id = ?", array(1));
}catch(Exception $e){
	echo "Erro: ".$e;
}

echo $jsonDetalhes;