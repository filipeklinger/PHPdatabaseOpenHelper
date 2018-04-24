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
    $db->setVariable("acesso",2);
	$jsonDetalhes = $db->select("id,primeiro_nome,sobrenome","usuario", "id = @acesso", null);
}catch(Exception $e){
	echo "Erro: ".$e;
}

echo $jsonDetalhes;