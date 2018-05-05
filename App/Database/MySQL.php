<?php

/**
 * Example:
 * $DB = new MySQL(DBName, DBTable);
 * $DB->query("SELECT * FROM rankme WHERE name=?", array($_GET['name']));
 */
namespace App\Database;
class MySQL
{
    private $Host;
    private $DBName;
    private $DBTable;
    private $DBUser;
    private $DBPassword;
    private $DBPort;
    private $PDO;
    private $parameters;
    private $bConnected = false;
    public $querycount = 0;

    function __construct($DBName, $DBTable)
    {
        $this->Host = getenv("DATABASE_HOST");
        $this->DBPort =  getenv("DATABASE_PORT");
        $this->DBUser = getenv("DATABASE_USER");
        $this->DBPassword = getenv("DATABASE_PASSWORD");
        $this->DBName = $DBName;
        $this->DBTable = $DBTable;
        $this->Connect();
        $this->parameters = array();
    }
    private function Connect()
    {
        try{
            $this->PDO = new \PDO("mysql:host={$this->Host};port={$this->DBPort};dbname={$this->DBName}", "root", "password");
            $this->bConnected = true;
        } catch (\PDOException $e){
            echo $e->getMessage();
            die();
        }
    }
    public function ConnectionClose()
    {
        $this->PDO = null;
    }
    private function Init($query, $parameters = "")
    {
        if(!$this->bConnected){
            $this->Connect();            
        }
        try{
            $this->parameters = $parameters;
            $this->sQuery = $this->PDO->prepare($this->BuildParams($query, $this->parameters));
            
            if(!empty($this->parameters)){
                if(array_key_exists(0, $parameters)){
                    $parametersType = true;
                    array_unshift($this->parameters, "");
                    unset($this->parameters[0]);
                } else {
                    $parametersType = false;
                }
                foreach ($this->parameters as $column => $value){
                    $this->sQuery->bindParam($parametersType ? intval($column) : ":" . $column, $this->parameters[$column]);
                }
            }
            $this->succes = $this->sQuery->execute();
            $this->querycount++;
        } catch (\PDOException $e){
            echo $e->getMessage();
            die();
        }
        $this->parameters = array();
    }
    private function BuildParams($query, $params = array())
    {
		if (!empty($params)) {
			$array_parameter_found = false;
			foreach ($params as $parameter_key => $parameter) {
				if (is_array($parameter)){
					$array_parameter_found = true;
					$in = "";
					foreach ($parameter as $key => $value){
						$name_placeholder = $parameter_key."_".$key;
					    	$in .= ":".$name_placeholder.", ";
						$params[$name_placeholder] = $value;
					}
					$in = rtrim($in, ", ");
					$query = preg_replace("/:".$parameter_key."/", $in, $query);
					unset($params[$parameter_key]);
				}
			}
			if ($array_parameter_found) $this->parameters = $params;
		}
		return $query;
    }
    public function query($query, $params = null, $fetchmode = \PDO::FETCH_ASSOC)
	{

		$query = trim($query);
		$rawStatement = explode(" ", $query);
		$this->Init($query, $params);
        $statement = strtolower($rawStatement[0]);
		if ($statement === 'select' || $statement === 'show') {
			return $this->sQuery->fetchAll($fetchmode);
		} elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
			return $this->sQuery->rowCount();
		} else {
			return NULL;
		}
    }    
    public function single($query, $params = null)
    {
        $this->Init($query, $params);
        return $this->sQuery->fetchColumn();
    }
	public function row($query, $params = null, $fetchmode = \PDO::FETCH_ASSOC)
	{
		$this->Init($query, $params);
		$resultRow = $this->sQuery->fetch($fetchmode);
		$this->rowCount = $this->sQuery->rowCount();
		$this->columnCount = $this->sQuery->columnCount();
		$this->sQuery->closeCursor();
		return $resultRow;
	}
}