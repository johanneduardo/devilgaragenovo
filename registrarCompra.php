<?php
session_start();
require 'conexao1.php';

if (isset($_SESSION['idUser']) && !empty($_SESSION['idUser'])) {
    // Recebe os dados da requisição
    $data = json_decode(file_get_contents("php://input"));

    $idUsuario = $_SESSION['idUser'];
    $moto = $data->moto;
    $items = $data->items;

    // Prepara a query para inserir a compra
    $stmt = $pdo->prepare("INSERT INTO compras (id_usuario, item, valor) VALUES (:id_usuario, :item, :valor)");

    // Insere a moto como um item
    $stmt->execute(['id_usuario' => $idUsuario, 'item' => 'Moto Harley', 'valor' => $moto]);

    // Insere os itens adicionais da customização
    foreach ($items as $item) {
        $stmt->execute(['id_usuario' => $idUsuario, 'item' => $item->name, 'valor' => $item->value]);
    }

    // Verifica se a operação foi bem-sucedida
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Falha ao registrar a compra']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
}

?>
