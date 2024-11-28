<?php
require_once 'ConnectionDB.php';

class ConsultDB
{
    public static function getQuestions($setor)
    {
        $conn = new Connection();
        $query = "SELECT QTS.PERGUNTA 
                  FROM TBQUESTOES QTS
                  WHERE IDSETOR = :setor
                    AND STATUSPERGUNTA = TRUE";

        try {
            $stmt = $conn->connectionDB->prepare($query); 
            $stmt->bindParam(':setor', $setor, PDO::PARAM_INT); 
            $stmt->execute();

            $questions = $stmt->fetchAll(PDO::FETCH_COLUMN); 
            return $questions; 
        } catch (PDOException $e) {
            http_response_code(500); 
            echo json_encode(['error' => 'Erro ao consultar perguntas: ' . $e->getMessage()]);
        }
    }
}