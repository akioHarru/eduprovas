<?php
require_once 'conexao.php';
session_start();

$message = '';
$edit_mode = false;
$edit_discipline = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_disciplina = $_POST['nome_disciplina'];
    $id_professor = 1;

    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        $sql = "UPDATE disciplinatb SET nome_disciplina = ? WHERE id_disciplina = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nome_disciplina, $_POST['edit_id']);
        if ($stmt->execute()) {
            $message = 'Disciplina atualizada com sucesso, minha linda!';
        } else {
            $message = 'Ops, algo deu errado: ' . $conn->error;
        }
        $stmt->close();
    } else {
        $sql = "INSERT INTO disciplinatb (nome_disciplina, id_professor) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nome_disciplina, $id_professor);
        if ($stmt->execute()) {
            $message = 'Disciplina cadastrada com sucesso, minha diva!';
        } else {
            $message = 'Ops, algo deu errado: ' . $conn->error;
        }
        $stmt->close();
    }
}

if (isset($_GET['delete'])) {
    $sql = "DELETE FROM disciplinatb WHERE id_disciplina = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['delete']);
    if ($stmt->execute()) {
        $message = 'Disciplina excluída com sucesso, minha rainha!';
    } else {
        $message = 'Ops, não deu pra excluir (talvez tenha questões vinculadas): ' . $conn->error;
    }
    $stmt->close();
}

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $sql = "SELECT * FROM disciplinatb WHERE id_disciplina = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_discipline = $result->fetch_assoc();
    $stmt->close();
}

$result = $conn->query("SELECT * FROM disciplinatb");
$disciplines = [];
while ($row = $result->fetch_assoc()) {
    $disciplines[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Disciplinas</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; }
        .discipline-list { margin-top: 20px; }
        .discipline-item { padding: 10px; border-bottom: 1px solid #eee; }
        .discipline-item a { margin-left: 10px; color: #d81b60; }
        label { display: block; margin: 10px 0 5px; }
        input { width: 100%; padding: 8px; margin-bottom: 10px; }
        button { padding: 10px 20px; background: #f06292; color: white; border: none; cursor: pointer; }
        button:hover { background: #ec407a; }
        .message { color: #d81b60; margin: 10px 0; }
    </style>
</head>
<body>
    <header>
        <h1>Gerenciar Disciplinas</h1>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="cadastro.php">Cadastrar</a></li>
                <li><a href="selecionar.php">Selecionar</a></li>
                <li><a href="gerar.php">Gerar Arquivo</a></li>
                <li><a href="disciplinas.php">Disciplinas</a></li>
                <li><a href="sobre.php">Sobre</a></li>
                <li><a href="contato.php">Contato</a></li>
                <li><a href="ajuda.php">Ajuda</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <h2><?php echo $edit_mode ? 'Editar Disciplina' : 'Cadastrar Nova Disciplina'; ?></h2>
            <?php if ($message): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
            <div class="form-container">
                <form method="POST">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $edit_discipline['id_disciplina']; ?>">
                    <?php endif; ?>
                    <label>Nome da Disciplina:
                        <input type="text" name="nome_disciplina" value="<?php echo $edit_mode ? htmlspecialchars($edit_discipline['nome_disciplina']) : ''; ?>" required>
                    </label>
                    <button type="submit"><?php echo $edit_mode ? 'Atualizar Disciplina' : 'Cadastrar Disciplina'; ?></button>
                </form>
            </div>

            <h2>Disciplinas Cadastradas</h2>
            <div class="discipline-list">
                <?php if (empty($disciplines)): ?>
                    <p>Nenhuma disciplina cadastrada, minha querida! Vamos criar algumas? 💕</p>
                <?php else: ?>
                    <?php foreach ($disciplines as $discipline): ?>
                        <div class="discipline-item">
                            <p><?php echo htmlspecialchars($discipline['nome_disciplina']); ?></p>
                            <a href="?edit=<?php echo $discipline['id_disciplina']; ?>">Editar</a>
                            <a href="?delete=<?php echo $discipline['id_disciplina']; ?>" onclick="return confirm('Tem certeza, minha diva?')">Excluir</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <footer>
        <p>© 2025 Banco de Questões</p>
    </footer>
</body>
</html>
