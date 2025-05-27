<?php
// disciplinas.php

require_once 'conexao.php';
require_once 'function.php';

$message = '';
$message_type = '';

// Lógica de manipulação de Disciplinas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Para depuração: veja o que está sendo enviado
    // echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";

    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'cadastrarDisciplina') {
            // AQUI ESTÁ A CORREÇÃO PRINCIPAL:
            // Pegue apenas a string do nome da disciplina do POST
            $nome_disciplina = $_POST['nome_disciplina'] ?? ''; // Garante que a variável é uma string

            $result = cadastrarDisciplina($conn, $nome_disciplina); // AGORA PASSA A STRING!
            $message = $result['message'];
            $message_type = $result['success'] ? 'success' : 'error';

        } elseif ($action === 'editarDisciplina') {
            $id_disciplina = $_POST['id_disciplina'] ?? '';
            $nome_disciplina = $_POST['nome_disciplina'] ?? '';

            $result = editarDisciplina($conn, $id_disciplina, $nome_disciplina);
            $message = $result['message'];
            $message_type = $result['success'] ? 'success' : 'error';

        } elseif ($action === 'excluirDisciplina') {
            $id_disciplina = $_POST['id_disciplina'] ?? '';

            $result = excluirDisciplina($conn, $id_disciplina);
            $message = $result['message'];
            $message_type = $result['success'] ? 'success' : 'error';
        }
    }
}

// Lógica para carregar dados para edição de disciplina
$disciplina_para_editar = null;
if (isset($_GET['action']) && $_GET['action'] === 'editarDisciplina' && isset($_GET['id'])) {
    $id_disciplina = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id_disciplina) {
        $stmt = $conn->prepare("SELECT * FROM disciplina_tb WHERE id_disciplina = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id_disciplina);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $disciplina_para_editar = $result->fetch_assoc();
            }
            $stmt->close();
        }
    }
}

// Lógica de manipulação de Assuntos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'cadastrarAssunto') {
            // Aqui a função cadastrarAssunto espera um array, então $_POST pode ser passado
            $result = cadastrarAssunto($conn, $_POST);
            $message = $result['message'];
            $message_type = $result['success'] ? 'success' : 'error';
        } elseif ($action === 'editarAssunto') {
            $result = editarAssunto($conn, $_POST);
            $message = $result['message'];
            $message_type = $result['success'] ? 'success' : 'error';
        } elseif ($action === 'excluirAssunto') {
            $id_assunto = $_POST['id_assunto'] ?? '';
            $result = excluirAssunto($conn, $id_assunto);
            $message = $result['message'];
            $message_type = $result['success'] ? 'success' : 'error';
        }
    }
}

// Lógica para carregar dados para edição de assunto
$assunto_para_editar = null;
if (isset($_GET['action']) && $_GET['action'] === 'editarAssunto' && isset($_GET['id'])) {
    $id_assunto = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id_assunto) {
        $stmt = $conn->prepare("SELECT * FROM assunto_tb WHERE id_assunto = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id_assunto);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $assunto_para_editar = $result->fetch_assoc();
            }
            $stmt->close();
        }
    }
}

// Carrega as listas para exibir
$disciplinas = listarDisciplinas($conn);
$assuntos = listarAssuntos($conn); // Pode passar filtros se precisar: listarAssuntos($conn, ['id_disciplina' => $id_da_disciplina]);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Disciplinas e Assuntos - EduProvas</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Adicionei alguns estilos básicos para feedback visual */
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .actions .btn {
            margin-right: 5px;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            color: white;
            cursor: pointer;
            border: none;
        }
        .btn-edit { background-color: #007bff; }
        .btn-delete { background-color: #dc3545; }
        .btn-primary {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            display: inline-block;
            margin-left: 10px;
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input[type="text"],
        form select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>EduProvas</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="cadastro.php">Questões</a></li>
                    <li><a href="disciplinas.php">Disciplinas/Assuntos</a></li>
                    <li><a href="selecionar.php">Gerar Prova</a></li>
                    <li><a href="sobre.php">Sobre</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2><?php echo $disciplina_para_editar ? 'Editar Disciplina' : 'Cadastrar Nova Disciplina'; ?></h2>
        <?php if ($message && (strpos($message, 'Disciplina') !== false || strpos($message, 'disciplina') !== false)): ?>
            <p class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST" action="disciplinas.php">
            <?php if ($disciplina_para_editar): ?>
                <input type="hidden" name="action" value="editarDisciplina">
                <input type="hidden" name="id_disciplina" value="<?php echo htmlspecialchars($disciplina_para_editar['id_disciplina']); ?>">
            <?php else: ?>
                <input type="hidden" name="action" value="cadastrarDisciplina">
            <?php endif; ?>
            
            <label for="nome_disciplina">Nome da Disciplina:</label>
            <input type="text" id="nome_disciplina" name="nome_disciplina" value="<?php echo htmlspecialchars($disciplina_para_editar['nome_disciplina'] ?? ''); ?>" required>
            
            <button type="submit" class="btn btn-primary"><?php echo $disciplina_para_editar ? 'Salvar Edição' : 'Cadastrar Disciplina'; ?></button>
            <?php if ($disciplina_para_editar): ?>
                <a href="disciplinas.php" class="btn btn-secondary">Cancelar Edição</a>
            <?php endif; ?>
        </form>

        <hr>

        <h3>Disciplinas Cadastradas</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome da Disciplina</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($disciplinas)): ?>
                        <?php foreach ($disciplinas as $disciplina): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($disciplina['id_disciplina']); ?></td>
                                <td><?php echo htmlspecialchars($disciplina['nome_disciplina']); ?></td>
                                <td class="actions">
                                    <a href="disciplinas.php?action=editarDisciplina&id=<?php echo htmlspecialchars($disciplina['id_disciplina']); ?>" class="btn btn-edit">Editar</a>
                                    <form method="POST" action="disciplinas.php" style="display:inline-block;">
                                        <input type="hidden" name="action" value="excluirDisciplina">
                                        <input type="hidden" name="id_disciplina" value="<?php echo htmlspecialchars($disciplina['id_disciplina']); ?>">
                                        <button type="submit" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir esta disciplina e todos os assuntos e questões associados a ela?');">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Nenhuma disciplina cadastrada ainda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <hr>

        <h2><?php echo $assunto_para_editar ? 'Editar Assunto' : 'Cadastrar Novo Assunto'; ?></h2>
        <?php if ($message && (strpos($message, 'Assunto') !== false || strpos($message, 'assunto') !== false)): ?>
            <p class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST" action="disciplinas.php">
            <?php if ($assunto_para_editar): ?>
                <input type="hidden" name="action" value="editarAssunto">
                <input type="hidden" name="id_assunto" value="<?php echo htmlspecialchars($assunto_para_editar['id_assunto']); ?>">
            <?php else: ?>
                <input type="hidden" name="action" value="cadastrarAssunto">
            <?php endif; ?>
            
            <label for="nome_assunto">Nome do Assunto:</label>
            <input type="text" id="nome_assunto" name="nome_assunto" value="<?php echo htmlspecialchars($assunto_para_editar['nome_assunto'] ?? ''); ?>" required>
            
            <label for="id_disciplina_assunto">Disciplina:</label>
            <select id="id_disciplina_assunto" name="id_disciplina" required>
                <option value="">Selecione uma Disciplina</option>
                <?php foreach ($disciplinas as $disciplina): ?>
                    <option value="<?php echo htmlspecialchars($disciplina['id_disciplina']); ?>"
                        <?php echo ($assunto_para_editar && $assunto_para_editar['id_disciplina'] == $disciplina['id_disciplina']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($disciplina['nome_disciplina']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="btn btn-primary"><?php echo $assunto_para_editar ? 'Salvar Edição' : 'Cadastrar Assunto'; ?></button>
            <?php if ($assunto_para_editar): ?>
                <a href="disciplinas.php" class="btn btn-secondary">Cancelar Edição</a>
            <?php endif; ?>
        </form>

        <hr>

        <h3>Assuntos Cadastrados</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Assunto</th>
                        <th>Disciplina</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($assuntos)): ?>
                        <?php foreach ($assuntos as $assunto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($assunto['id_assunto']); ?></td>
                                <td><?php echo htmlspecialchars($assunto['nome_assunto']); ?></td>
                                <td><?php echo htmlspecialchars($assunto['nome_disciplina'] ?? 'N/A'); ?></td>
                                <td class="actions">
                                    <a href="disciplinas.php?action=editarAssunto&id=<?php echo htmlspecialchars($assunto['id_assunto']); ?>" class="btn btn-edit">Editar</a>
                                    <form method="POST" action="disciplinas.php" style="display:inline-block;">
                                        <input type="hidden" name="action" value="excluirAssunto">
                                        <input type="hidden" name="id_assunto" value="<?php echo htmlspecialchars($assunto['id_assunto']); ?>">
                                        <button type="submit" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este assunto e todas as questões associadas a ele?');">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Nenhum assunto cadastrado ainda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 EduProvas. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>