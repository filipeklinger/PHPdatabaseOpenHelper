# PHPdatabaseOpenHelper
Database Open Helper \
API para para simplificar acesso ao banco de dados\
Criada para ser extremamente leve e não possuir qualquer dependencia externa.\
seu uso se resume a incluir o Arquivo "DatabaseOpenHelper.php" e instanciar a classe de manupulação do SGBD\
Possui internamente métodos anti Sql Injection

# Proprosito

Camada de comunicação com banco de dados
- Retorna os dados no formato JSON
- Erros de sintaxe são Logados para arquivo (Err< tipoDoErro >.txt); 


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
select($columns,$table,$whereClause,$whereArgs,$orderBy,$sequence, $limit,$offset);

```

- $columns : String;
- $table : String;
- $whereClause : String; (em branco se nao utilizar)
- $whereArgs : array(string);(em branco se nao utilizar)
- $orderBy : String;(em branco se nao utilizar)
- $sequence : constante (ASC/DESC);
- $limit : Integer (Restringe quantidade de resultados retornados)
- $offset : Integer (Seta quantidade de resultados pulados);

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
$orderBy = "nome"

try{
    //select em ordem Decrescente
	$stringJson = $db->select($columns,$table,$whereClause,$whereArgs,$orderBy,DESC);
	
	//Select em ordem crescente na 5ª página com 25 resultados por página
	
	$limit = 25;
	$pagina = 5;
	$offset = $pagina*$limit;
	
	$stringJson2 = $db->select($columns,$table,$whereClause,$whereArgs,$orderBy,ASC,$limit,$offset);
	
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

$columns = array("nome","idade","endereco");
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
###### Last ID

```
getLastId()
```

- Sem parâmetros

###### Throw Erros

- Não


###### Exemplo de uso:

```
$db = new Database();

$columns = "nome,idade,endereco";
$table = "usuario";
$params = array("joão",5,"Rua xyz");//na mesma ordem do columns
try{
	$boolean = $db->insert($columns,$table,$params);
	$lasId = $db->getLastId();//Id do insert acima
}catch(Exception e){
	//Todo Handle Exception
}

```

-----------------------------------------------------------------------
###### SetVariable

```
setVariable(string $name,mixed $value);
```

- $name : String;
- $mixed : value;

###### Throw Erros

- Sem parâmetros


###### Exemplo de uso:

```
$db = new Database();

//calculamos a idade dos usuarios 
$columns = "nome,idade,sexo,TIMESTAMPDIFF(YEAR, c.nascimento, NOW()) AS idade";
$table = "usuario";


try{
    //setando uma idade de comparacao 
    $db->setVariable("nascimento","2020-01-20");
    
    //buscando pessoas que terao 18 anos em 2020
    $whereClause = "TIMESTAMPDIFF(YEAR, usuario.nascimento, @nascimento) = 18";
    
	$stringJson = $db->select($columns,$table,$whereClause,null);
	
}catch(Exception e){
	//Todo Handle Exception
}
```
