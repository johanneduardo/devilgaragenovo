<?php
session_start();
require 'conexao1.php';

// Verificar se a requisição é do tipo POST e se existe uma sessão de usuário válida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['idUser']) && !empty($_SESSION['idUser'])) {
    // Receber os dados enviados via POST (formato JSON)
    $data = json_decode(file_get_contents("php://input"));

    // ID do usuário logado
    $idUsuario = $_SESSION['idUser'];

    // Itens a serem excluídos
    $items = $data->items;

    // Preparar a query SQL para inserir as exclusões
    $stmt = $pdo->prepare("INSERT INTO exclusoes (id_usuario, item, valor) VALUES (:id_usuario, :item, :valor)");

    try {
        // Iniciar transação
        $pdo->beginTransaction();

        // Iterar sobre os itens e executar a inserção
        foreach ($items as $item) {
            $stmt->execute(['id_usuario' => $idUsuario, 'item' => $item->name, 'valor' => $item->value]);
        }

        // Confirmar a transação
        $pdo->commit();

        // Verificar se as inserções foram bem-sucedidas
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhum registro foi inserido']);
        }

    } catch (PDOException $e) {
        // Cancelar a transação em caso de erro
        $pdo->rollback();
        echo json_encode(['success' => false, 'message' => 'Erro ao registrar a exclusão: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
}
?>
