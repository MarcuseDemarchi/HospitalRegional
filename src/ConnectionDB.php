<?php

require_once 'ReadConfig.php';

class Connection extends readConfig
{
    private $arrayDB = [];
    public $connectionDB;

    public function __construct() 
    {
        parent::__construct();
        $this->setVariables($this->configDB);
    }

    private function setVariables($arrayconfigDB)
    {
        $this->arrayDB['Server']   = $arrayconfigDB['Server'];
        $this->arrayDB['Port']     = $arrayconfigDB['Port'];
        $this->arrayDB['DataBase'] = $arrayconfigDB['DataBase'];
        $this->arrayDB['User']     = $arrayconfigDB['User'];
        $this->arrayDB['Password'] = $arrayconfigDB['Password'];

        $this->connectDataBase($this->arrayDB);
    }

    private function connectDataBase($arrayDB)
    {
        // Monta o DSN (Data Source Name) para o driver PDO PostgreSQL
        $dsn = "pgsql:host={$arrayDB['Server']};port={$arrayDB['Port']};dbname={$arrayDB['DataBase']}";
        $user = $arrayDB['User'];
        $password = $arrayDB['Password'];
  
        try {
            // Cria a conexão PDO
            $this->connectionDB = new PDO($dsn, $user, $password);
            // Configura o PDO para lançar exceções em caso de erro
            $this->connectionDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Lança um erro amigável em caso de falha na conexão
            die(json_encode(['success' => false, 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]));
        }
    }
}
