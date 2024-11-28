<?php
class Authenticate
{
    public static function login($connection, $usuario, $senha)
    {
        if (empty($usuario) || empty($senha)) {
            return ['success' => false, 'message' => 'Usuário ou senha não podem estar vazios'];
        }

        try {
            $query = "
                SELECT tbadmin.pass, tbadmin.iduser 
                FROM tbadmin
                INNER JOIN TBUSER ON tbadmin.iduser = TBUSER.iduser
                WHERE TBUSER.name = :name
            ";
            $stmt = $connection->connectionDB->prepare($query);
            $stmt->bindParam(':name', $usuario, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($senha, $user['pass'])) {
                session_start();
                $_SESSION['user_id'] = $user['iduser'];
                return ['success' => true, 'message' => 'Login bem-sucedido'];
            } else {
                return ['success' => false, 'message' => 'Usuário ou senha incorretos'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro no servidor: ' . $e->getMessage()];
        }
    }
}
