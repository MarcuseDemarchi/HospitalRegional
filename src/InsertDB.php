<?php

require_once 'ConnectionDB.php';

class InsertDB
{
    public static function InsertAvaliacao($connection, $setor, $notas, $feedback)
    {
        try {
            $columns = ['idsetor', 'idquestao', 'iddispositivo', 'notaquestao', 'feedback'];

            for ($i = 0; $i < count($notas); $i++) {
                $idQuestao = self::consultIdQuestao($connection, $setor, $i + 1);
                if (!$idQuestao) {
                    throw new Exception("Questão não encontrada para setor $setor e número de questão " . ($i + 1));
                }

                $values = [$setor, $idQuestao, $setor, $notas[$i], $feedback[$i]];
                self::insertSQL($connection, 'TBAVALIACOES', $columns, $values);
            }

            echo json_encode(['success' => true, 'message' => 'Avaliação inserida com sucesso!']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao inserir avaliação: ' . $e->getMessage()]);
        }
    }

    private static function consultIdQuestao($connection, $setor, $numqt)
    {
        $query = "SELECT idquestao FROM TBQUESTOES WHERE idsetor = :setor AND numquestao = :numqt";

        try {
            $stmt = $connection->connectionDB->prepare($query);
            $stmt->bindParam(':setor', $setor, PDO::PARAM_INT);
            $stmt->bindParam(':numqt', $numqt, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? intval($result['idquestao']) : null;
        } catch (PDOException $e) {
            throw new Exception("Erro ao consultar idquestao: " . $e->getMessage());
        }
    }

    private static function insertSQL($connection, $table, $columns, $values)
    {
        $columnsStr = implode(', ', $columns);
        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        $query = "INSERT INTO $table ($columnsStr) VALUES ($placeholders)";
        try {
            $stmt = $connection->connectionDB->prepare($query);
            $stmt->execute($values);
        } catch (PDOException $e) {
            throw new Exception("Erro ao inserir dados: " . $e->getMessage());
        }
    }
}
