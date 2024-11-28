<?php
require_once 'ConnectionDB.php';

try {
    $conn = new Connection();

    $username = 'admin';
    $setor = 1; 
    $queryUser = "INSERT INTO TBUSER (name, idsetor) VALUES (:name, :idsetor) RETURNING iduser";
    $stmtUser = $conn->connectionDB->prepare($queryUser);
    $stmtUser->bindParam(':name', $username, PDO::PARAM_STR);
    $stmtUser->bindParam(':idsetor', $setor, PDO::PARAM_INT);
    $stmtUser->execute();

    $idUser = $stmtUser->fetch(PDO::FETCH_ASSOC)['iduser'];

    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $queryAdmin = "INSERT INTO tbadmin (iduser, pass) VALUES (:iduser, :pass)";
    $stmtAdmin = $conn->connectionDB->prepare($queryAdmin);
    $stmtAdmin->bindParam(':iduser', $idUser, PDO::PARAM_INT);
    $stmtAdmin->bindParam(':pass', $password, PDO::PARAM_STR);
    $stmtAdmin->execute();

    echo "Usuário de teste inserido com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao inserir dados de teste: " . $e->getMessage();
}
