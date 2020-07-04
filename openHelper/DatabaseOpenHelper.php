<?php
/**
 * Created by Filipe Klinger.
 * Date: 03/03/18
 * Time: 14:37
 * v2.0.0
 */
namespace OpenHelper;
use Exception;
use PDO;
use PDOException;

const ASC = " ASC ";
const DESC = " DESC ";
class DatabaseOpenHelper{
    /**
     * @var PDO
     */
    private $diretorio;
    private $databaseObj;

    function __construct(){
        $this->diretorio = dirname(__FILE__);
        try {
            $this->conectar();
        } catch (Exception $e) {
            echo 'Database Error: ' . $e->getMessage();
            die();
        }
    }

    /**
     * Getting Database configs
     * @param string $arquivo
     * @throws Exception ErrorOnOpen
     */
    private function conectar($arquivo = 'database.ini')
    {
        if (!$setings = parse_ini_file($arquivo, TRUE)) throw new Exception("ErrorOnOpen");
        $sgbd = $setings['database']['sgbd'];
        $host = $setings['database']['host'];
        $port = $setings['database']['port'];
        $schema = $setings['database']['schema'];
        $username = $setings['database']['username'];
        $password = $setings['database']['password'];

        $dsn = "$sgbd:host=$host;port=$port;dbname=$schema";

        try{
            $con = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->databaseObj = $con;
        }catch(PDOException $e){
            echo 'Connection failed: ' . $e->getMessage();
            die();
        }
    }

//-----------------SELECT-------------------------------------------------------------------

    /**
     * @param String $columns
     * @param String $table
     * @param String $whereClause
     * @param Array $whereArgs
     * @param String $orderBy
     * @param String $sequence
     * @param Integer $limit
     * @param Integer $offset
     * @return Array Bidimensional com Tuplas do banco
     * @throws Exception
     */
    public function select($columns, $table, $whereClause = null, $whereArgs = [], $orderBy = null,$sequence = ASC, $limit = 0,$offset = 0)
    {
        //check
        if (empty($columns)) throw new Exception("EmptyColumns", 1);
        if (empty($table)) throw new Exception("EmptyTable", 1);
        //begin
        $query = "SELECT ";

        //Projection
        $query .= $columns;

        //TABLE
        $query .= " FROM " . $table;

        //RESTRICTION
        if ($whereClause != null and is_string($whereClause) and strlen($whereClause) > 0) {
            $query .= " WHERE ";
            $query .= $whereClause;
        }

        //ORDER
        if ($orderBy != null and is_string($orderBy) and strlen($orderBy) > 0) {
            $query .= " ORDER BY " . $this->antiInjection($orderBy)." ".$this->antiInjection($sequence);
        }

        //Paginator
        if (!empty($limit)) {
            $query .= " LIMIT ". intval($limit);
        }

        if (!empty($offset)) {
            $query .= " OFFSET ".intval($offset);
        }
        //Preparing
        $PDO = $this->databaseObj;
        $stmt = $PDO->prepare($query);
        //Inserting params
        if ($whereArgs != null and sizeof($whereArgs) > 0) {
            for ($i = 0; $i < sizeof($whereArgs); $i++) {
                $whereArgs[$i] = $this->antiInjection($whereArgs[$i]);
                $stmt->bindParam($i + 1, $whereArgs[$i]);

            }
        }

        //Running Query
        try {
            $stmt->execute();
        } catch (Exception $Exception) {
            $this->logError($Exception,$query,"/ErrLogSelect.txt");

            $stmt->closeCursor();
            return false;
        }

        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $dados; //return json_encode($data,JSON_UNESCAPED_UNICODE);//return json
    }

//-------------------------INSERT------------------------------------------------

    /**
     * @param String $columns
     * @param String $table
     * @param Array $params
     * @return Boolean Inserido
     * @throws Exception
     */
    public function insert($columns, $table, $params = [])
    {
        //check
        if (empty($columns)) throw new Exception("EmptyColumns", 1);
        if (empty($table)) throw new Exception("EmptyTable", 1);
        if (!is_array($params)) throw new Exception("ArrayNotFound", 1);
        if (sizeof($params) < 1) throw new Exception("EmptyParams", 1);

        //Begin
        $query = "INSERT INTO " . $table;
        $query .= " ( " . $columns . " ) VALUES (";

        //Inserting placeholders
        for ($i = 0; $i < sizeof($params); $i++) {
            if ($i < sizeof($params) - 1) {
                $query .= " ?, ";
            } else {
                $query .= " ? ";
            }
        }
        //closing placeholders
        $query .= ") ";

        //Preparing
        $PDO = $this->databaseObj;
        $stmt = $PDO->prepare($query);

        //Inserting params
        if ($params != null and sizeof($params) > 0) {
            for ($i = 0; $i < sizeof($params); $i++) {
                $params[$i] = $this->antiInjection($params[$i]);
                $stmt->bindParam($i + 1, $params[$i]);
            }
        }

        //Running Query
        try {
            $stmt->execute();
        } catch (Exception $e) {//logamos os erros em arquivo
            $this->logError($e,$query,"/ErrLogInsert.txt");
            $stmt->closeCursor();
            return false;
        }

        $stmt->closeCursor();

        return true;
    }
//-----------------UPDATE-------------------------------------------------------------------

    /**
     * @param Array $columns
     * @param String $table
     * @param Array $params
     * @param String $whereClause
     * @param Array $whereArgs
     * @return Boolean atualizado
     * @throws Exception
     */
    public function update($columns = [], $table, $params = [], $whereClause = null, $whereArgs = array(null))
    {
        //check
        if (!is_array($columns)) throw new Exception("ArrayNotFound", 1);
        if (sizeof($columns) < 1) throw new Exception("EmptyColumns", 1);
        if (empty($table)) throw new Exception("EmptyTable", 1);
        if (!is_array($params)) throw new Exception("ArrayNotFound", 1);
        if (sizeof($params) < 1) throw new Exception("EmptyParams", 1);
        if (!is_array($whereArgs)) throw new Exception("ArrayNotFound", 1);

        $query = "UPDATE " . $table . " SET ";

        //binding VALUES
        for ($i = 0; $i < sizeof($columns); $i++) {
            if ($i < sizeof($columns) - 1) {
                $query .= $columns[$i] . " = ? ,";
            } else {
                $query .= $columns[$i] . " = ? ";
            }
        }

        //RESTRICTION
        if ($whereClause != null and strlen($whereClause) > 0) {
            $query .= " WHERE ";
            $query .= $whereClause;
        }

        //Preparing
        $PDO = $this->databaseObj;
        $stmt = $PDO->prepare($query);

        //Inserting params
        $i = 0;
        if (sizeof($params) > 0) {
            for ($j = 0; $j < sizeof($params); $j++) {
                //somente os parametros vem do usuario entao testamos
                $params[$j] = $this->antiInjection($params[$j]);
                $stmt->bindParam($i + 1, $params[$j]);
                $i++;
            }
        }
        //Inserting RESTRICTION params
        if (sizeof($whereArgs) > 0) {
            for ($j = 0; $j < sizeof($whereArgs); $j++) {
                $whereArgs[$j] = $this->antiInjection($whereArgs[$j]);
                $stmt->bindParam($i + 1, $whereArgs[$j]);
                $i++;
            }
        }

        //Running Query
        try {
            $stmt->execute();
        } catch (Exception $Exception) {
            $this->logError($Exception,$query,"/ErrLogUpdate.txt");
            $stmt->closeCursor();
            return false;
        }

        $stmt->closeCursor();

        return true;
    }
//----DELETE--------------------------------------

    /**
     * @param String $table
     * @param String $whereClause
     * @param Array $whereArgs
     * @return Boolean apagado
     * @throws Exception
     */
    public function delete($table, $whereClause = null, $whereArgs = array(null))
    {
        //Check
        if (empty($table)) throw new Exception("EmptyTable", 1);
        if (!is_array($whereArgs)) throw new Exception("ArrayNotFound", 1);

        //Begin
        $query = "DELETE FROM " . $table;


        //RESTRICTION
        if ($whereClause != null and strlen($whereClause) > 0) {
            $query .= " WHERE ";
            $query .= $whereClause;
        }

        //Preparing
        $PDO = $this->databaseObj;
        $stmt = $PDO->prepare($query);

        //Inserting params
        if (sizeof($whereArgs) > 0) {
            for ($i = 0; $i < sizeof($whereArgs); $i++) {
                $whereArgs[$i] = $this->antiInjection($whereArgs[$i]);
                $stmt->bindParam($i + 1, $whereArgs[$i]);
            }
        }

        //Running query
        try {
            $stmt->execute();
        } catch (Exception $Exception) {
            $this->logError($Exception,$query,"/ErrLogDelete.txt");
            $stmt->closeCursor();
            return false;
        }

        $stmt->closeCursor();

        return true;
    }

    /**
     * antiInjection
     * realiza validações dos valores para evitar inserção de
     * codigo malicioso com SQL Injection
     * @param $dados
     * @return string
     * @throws Exception SQLInjectionError
     */
    private function antiInjection($dados)
    {
        $dados = trim($dados);
        $dados = stripslashes($dados);
        //buscando se possui caracteres invalidos e substituindo
        $dados = str_replace(";" , ". " , $dados );
        $dados = htmlspecialchars($dados);
        return $dados;
    }

//-------------------LastId---------------------------------------------------------------------------------------------
    /**
     * @return Integer Last Id Inserted || False
     */
    public function getLastId()
    {
        $query = "SELECT LAST_INSERT_ID()";

        //Preparing
        $PDO = $this->databaseObj;
        $stmt = $PDO->prepare($query);

        //Running query
        try {
            $stmt->execute();
        } catch (Exception $Exception) {
            $this->logError($Exception,$query,"/ErrLogLastId.txt");
            $stmt->closeCursor();
            return false;
        }

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $data = $data[0]['LAST_INSERT_ID()'];
        return $data;
    }
//-----------------------------------SET---Variable---------------------------------------------------------------------
    /**
     * @param string $name name of variable
     * @param $value mixed value of variable
     */
    public function setVariable(string $name, $value){
        $value = $this->antiInjection($value);
        $name = $this->antiInjection($name);
        $this->databaseObj->query("Set @".$name.":=".$value);
    }

//---------------------------LOG--------------------------------

    /**
     * Loga os Erros em arquivo com detalhes
     * @param Exception $Exception
     * @param String $query
     * @param String $arqName
     */
    private function logError($Exception,$query,$arqName){
        $err = $Exception->getMessage();
        $trace = explode("\n", $Exception->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = [];
    
        for ($i = 0; $i < $length; $i++){
            $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // set the right ordering
        }
   
        $trace = implode("; ", $result);

        $err = "[" . date("d/m/Y h:i A") . "]" . " ERRO: " . $err ." StackTrace: " . $trace. " QUERY: " . $query . "\n";
        $arquivo = fopen($this->diretorio."{$arqName}", "a+");
        fwrite($arquivo, $err);
        fclose($arquivo);
    }
}

