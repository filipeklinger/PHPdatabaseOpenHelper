<?php
/**
 * Created by PhpStorm.
 * User: filipe
 * Date: 28/03/18
 * Time: 14:18
 */
include "../model/DatabaseOpenHelper.php";
$db = new Database();

//echo $db->select("*","perguntas");

$jsonDetalhes = $db->select("id,pergunta,tipo,prioridade,respostas", "perguntas", "id = ?", array(1));
$obj = json_decode($jsonDetalhes);
echo "<pre>";
echo "Tipo".$obj[0]->tipo;
echo "</pre>";
