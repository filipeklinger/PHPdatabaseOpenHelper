# PHPdatabaseOpenHelper
Database Open Helper
API para para simplificar acesso ao banco de dados

# Funcionalidade

Introduz código SQL e retorna JSON
- Erros de sintaxe são Logados para arquivo (Err<tipoDoErro>.txt);

# CONFIGURAÇÃO

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

# MÉTODOS

## DatabaseOpenHelper.php

###### new Database();

Cria uma nova instancia de PDO utilizando a configuração do arquivo database.ini;

-----------------------------------------------------------------------
###### Select

```
select($columns,$table,$whereClause,$whereArgs,$orderBy);

```

- $columns : String;
- $table : String;
- $whereClause : String; (em branco se nao utilizar)
- $whereArgs : array(string);(em branco se nao utilizar)
- $orderBy : String

###### throw erros

- EmptyColumns
- EmptyTable
- if($whereClause != null) ArrayNotFound

###### Exemplo de uso:

```
$db = new Database();

$columns = "nome,idade,sexo";
$table = "usuario";
$whereClause = "nome LIKE ? and idade < ?";
$whereArgs = array("joão",18);//na mesma ordem do whereClause
$orderBy = "ASC"
try{
	$stringJson = $db->select($columns,$table,$whereClause,$whereArgs,$orderBy);
}catch(Exception e){
	//Todo Handle Exception
}
```

-----------------------------------------------------------------------
###### Insert

```
insert($columns,$table,$params)
```
- $columns : String;
- $table : String;
- $params : array(string)

###### Throw Erros

- EmptyColumns
- EmptyTable
- ArrayNotFound
- EmptyParams

###### Exemplo de uso:

```
$db = new Database();

$columns = "nome,idade,endereco";
$table = "usuario";
$params = array("joão",5,"Rua xyz");//na mesma ordem do columns
try{
	$boolean = $db->insert($columns,$table,$params);
}catch(Exception e){
	//Todo Handle Exception
}

```

-----------------------------------------------------------------------
###### Update

```
update($columns,$table,$params,$whereClause,$whereArgs)
```

- $columns : array(String)
- $table : String;
- $params : array(String)
- $whereClause : String;
- $WhereArgs : String;

###### Exemplo de uso:

```
$db = new Database();

$columns = "nome,idade,endereco";
$table = "usuario";
$params = array("joão",10,"Rua xyz");//na mesma ordem do columns
$whereClause = "id = ?";
$whereArgs = array(1);

try{
	$boolean = $db->update($columns,$table,$params,$whereClause,$whereArgs);
}catch(Exception e){
	//Todo Handle Exception
}

```
-----------------------------------------------------------------------
###### Delete

```
delete($table,$whereClause,$whereArgs)
```

- $table : String;
- $whereClause : String;
- $WhereArgs : array(String);

###### Throw Erros

- EmptyTable


###### Exemplo de uso:

```
$db = new Database();

$columns = "nome,idade,endereco";
$table = "usuario";
$whereClause = "id = ?";
$whereArgs = array(1);

try{
	$boolean = $db->delete($table,$whereClause,$whereArgs);
}catch(Exception e){
	//Todo Handle Exception
}

```

