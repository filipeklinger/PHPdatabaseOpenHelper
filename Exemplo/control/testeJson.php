<?php
/**
 * Created by Filipe
 * Date: 28/03/18
 * Time: 14:18
 */
include "../../openHelper/DatabaseOpenHelper.php";
$db = new Database();

$detalhes = "";
try{
    $db->setVariable("acesso",2);
	$detalhes = $db->select("id,primeiro_nome,sobrenome","usuario", "id = @acesso", null);
}catch(Exception $e){
	echo "Erro: ".$e;
}

//Enviando para o front em JSON
echo json_encode($detalhes,JSON_UNESCAPED_UNICODE);