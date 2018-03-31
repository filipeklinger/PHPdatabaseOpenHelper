# PHPdatabaseOpenHelper
Database Open Helper
API para para simplificar acesso ao banco de dados

# Funcionalidade

Introduz código SQL e retorna JSON

# METODOS "/model"

## database.ini

```
[database]
sgbd = mysql
host = seuHost
port = 3306
schema = suaBaseDeDados
username = usuarioDoSGBD
password = senha
```

## DatabaseOpenHelper.php

###### new Database();

Cria uma nova instancia de PDO utilizando a configuração do arquivo database.ini;

###### Select

```
select($columns,$table,$whereClause,$whereArgs,$orderBy);

```

- $columns : String;
- $table : String;
- $whereClause : String; (em branco se nao utilizar)
- $whereArgs : array(null);(em branco se nao utilizar)
- $orderBy : String

###### throw erros

- EmptyColumns
- EmptyTable
- if($whereClause != null) ArrayNotFound

-----------------------------------------------------------------------
###### Exemplo de uso:

```
$db = new Database();

$columns = "nome,idade,sexo";
$table = "usuario";
$whereClause = "nome LIKE ? and idade < ?";
$whereArgs = array("joão",18);//na mesma ordem do whereClause
$orderBy = "ASC"

$stringJson = $db->select($columns,$table,$whereClause,$whereArgs,$orderBy = null);
```

