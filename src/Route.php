<?php
require_once 'ConnectionDB.php';
require_once 'ConsultDB.php';
require_once 'InsertDB.php';
require_once 'Authenticate.php';

$connection = new Connection();
$route = $_GET['route'] ?? null;

switch ($route) {
    case 'consult-questions':
        $setor = $_GET['setor'] ?? null;

        if (is_numeric($setor)) {
            $quetions = ConsultDB::getQuestions((int)$setor);
            echo json_encode($quetions);
        } else {
            http_response_code(400); 
            echo json_encode(['error' => 'Setor inválido ou não informado']);
        }
        break;
    case 'insert-response':
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        $Teste = $data['setor'];
        if (isset($data['setor'], $data['notas'], $data['feedback'])) {
            InsertDB::InsertAvaliacao($connection, $data['setor'], $data['notas'], $data['feedback']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Dados incompletos para inserção']);
        }
        break;
    case 'authenticate':
        $input = json_decode(file_get_contents('php://input'), true);
        $usuario = $input['usuario'] ?? '';
        $senha = $input['senha'] ?? '';
    
        $response = Authenticate::login($connection, $usuario, $senha);
    
        // Converte a resposta para JSON antes de enviá-la
        echo json_encode($response);
        break;
    case 'get-sectors':
        $query = "SELECT idsetor, nome FROM TBSETORES";
        $stmt = $connection->connectionDB->query($query);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;    
    case 'get-questions':
        $setor = $_GET['setor'] ?? null;
    
        $query = "
            SELECT q.idquestao, s.nome AS nome_setor, q.pergunta, q.statuspergunta
            FROM TBQUESTOES q
            INNER JOIN TBSETORES s ON q.idsetor = s.idsetor
        ";
        if ($setor) {
            $query .= " WHERE q.idsetor = :setor";
        }
    
        $stmt = $connection->connectionDB->prepare($query);
        if ($setor) {
            $stmt->bindParam(':setor', $setor, PDO::PARAM_INT);
        }
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
    case 'add-question':
        $input = json_decode(file_get_contents('php://input'), true);
        $setor = $input['setor'];
        $pergunta = $input['pergunta'];
    
        $query = "INSERT INTO TBQUESTOES (idsetor, pergunta, statuspergunta) VALUES (:idsetor, :pergunta, TRUE)";
        $stmt = $connection->connectionDB->prepare($query);
        $stmt->bindParam(':idsetor', $setor, PDO::PARAM_INT);
        $stmt->bindParam(':pergunta', $pergunta, PDO::PARAM_STR);
        $stmt->execute();
    
        echo json_encode(['success' => true, 'message' => 'Pergunta adicionada com sucesso']);
        break;     
    case 'edit-question':
        $input = json_decode(file_get_contents('php://input'), true);
        $idquestao = $input['idquestao'];
        $pergunta = $input['pergunta'];
    
        $query = "UPDATE TBQUESTOES SET pergunta = :pergunta WHERE idquestao = :idquestao";
        $stmt = $connection->connectionDB->prepare($query);
        $stmt->bindParam(':idquestao', $idquestao, PDO::PARAM_INT);
        $stmt->bindParam(':pergunta', $pergunta, PDO::PARAM_STR);
        $stmt->execute();
    
        echo json_encode(['success' => true, 'message' => 'Pergunta atualizada com sucesso']);
        break;     
    case 'delete-question':
        $idquestao = $_GET['idquestao'] ?? null;
    
        if ($idquestao) {
            $query = "DELETE FROM TBQUESTOES WHERE idquestao = :idquestao";
            $stmt = $connection->connectionDB->prepare($query);
            $stmt->bindParam(':idquestao', $idquestao, PDO::PARAM_INT);
            $stmt->execute();
    
            echo json_encode(['success' => true, 'message' => 'Pergunta removida com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID da pergunta não informado']);
        }
        break;  
    case 'get-users':
        $query = "
            SELECT u.iduser, u.name AS nome, s.nome AS setor
            FROM TBUSER u
            INNER JOIN TBSETORES s ON u.idsetor = s.idsetor
        ";
        $stmt = $connection->connectionDB->query($query);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;      
    case 'add-user':
        $input = json_decode(file_get_contents('php://input'), true);
        $nome = $input['nome'];
        $senha = $input['senha'];
        $setor = $input['setor'];
    
        // Inserir usuário
        $query = "INSERT INTO TBUSER (name, idsetor) VALUES (:name, :idsetor) RETURNING iduser";
        $stmt = $connection->connectionDB->prepare($query);
        $stmt->bindParam(':name', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':idsetor', $setor, PDO::PARAM_INT);
        $stmt->execute();
        $iduser = $stmt->fetchColumn();
    
        // Inserir senha no TBADMIN
        $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
        $query = "INSERT INTO tbadmin (iduser, pass) VALUES (:iduser, :pass)";
        $stmt = $connection->connectionDB->prepare($query);
        $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
        $stmt->bindParam(':pass', $hashSenha, PDO::PARAM_STR);
        $stmt->execute();
    
        echo json_encode(['success' => true, 'message' => 'Usuário adicionado com sucesso']);
        break;     
    case 'delete-user':
        $iduser = $_GET['iduser'] ?? null;
    
        if ($iduser) {
            $query = "DELETE FROM tbadmin WHERE iduser = :iduser";
            $stmt = $connection->connectionDB->prepare($query);
            $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
            $stmt->execute();
    
            $query = "DELETE FROM TBUSER WHERE iduser = :iduser";
            $stmt = $connection->connectionDB->prepare($query);
            $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
            $stmt->execute();
    
            echo json_encode(['success' => true, 'message' => 'Usuário removido com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID do usuário não informado']);
        }
        break;                         
    case 'get-evaluations':
        $setor = $_GET['setor'] ?? null;
    
        $query = "
            SELECT q.pergunta, AVG(a.notaquestao) AS media
            FROM TBAVALIACOES a
            INNER JOIN TBQUESTOES q ON a.idquestao = q.idquestao
        ";
        if ($setor) {
            $query .= " WHERE q.idsetor = :setor";
        }
        $query .= " GROUP BY q.pergunta";
    
        $stmt = $connection->connectionDB->prepare($query);
        if ($setor) {
            $stmt->bindParam(':setor', $setor, PDO::PARAM_INT);
        }
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;     
    case 'get-evaluations':
        $setor = $_GET['setor'] ?? null;
    
        $query = "
            SELECT s.nome AS nome_setor, AVG(a.notaquestao) AS media
            FROM TBAVALIACOES a
            INNER JOIN TBSETORES s ON a.idsetor = s.idsetor
        ";
        if ($setor) {
            $query .= " WHERE a.idsetor = :setor";
        }
        $query .= " GROUP BY s.nome";
    
        $stmt = $connection->connectionDB->prepare($query);
        if ($setor) {
            $stmt->bindParam(':setor', $setor, PDO::PARAM_INT);
        }
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
    default:
        http_response_code(404); 
         echo json_encode(['error' => 'Rota não encontrada']);
}
