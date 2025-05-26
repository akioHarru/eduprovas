<?php
require_once 'conexao.php';
session_start();

$message = '';
$edit_mode = false;
$edit_question = null;
$disciplineId = isset($_POST['discipline']) ? $_POST['discipline'] : null;
$assuntoId = isset($_POST['assunto']) ? $_POST['assunto'] : null;

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $disciplina = $_POST['disciplina'];
    $enunciado = $_POST['enunciado'];
    $alternativa_a = $_POST['alternativa_a'];
    $alternativa_b = $_POST['alternativa_b'];
    $alternativa_c = $_POST['alternativa_c'];
    $alternativa_d = $_POST['alternativa_d'];
    $resposta = $_POST['resposta'];
    $assunto = $_POST['assunto'];

    $full_enunciado = "$enunciado\na) $alternativa_a\nb) $alternativa_b\nc) $alternativa_c\nd) $alternativa_d";

    $sql = "UPDATE questãotb SET id_disciplina = ?, enunciado = ?, resposta = ?, id_assunto = ? WHERE id_questão = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $disciplina, $full_enunciado, $resposta, $assunto, $_POST['edit_id']);
    if ($stmt->execute()) {
        $message = 'Questão atualizada com sucesso, minha diva!';
    } else {
        $message = 'Ops, algo deu errado ao atualizar: ' . $conn->error;
    }
    $stmt->close();
}

// Delete
if (isset($_GET['delete'])) {
    $sql = "DELETE FROM questãotb WHERE id_questão = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['delete']);
    if ($stmt->execute()) {
        $message = 'Questão excluída com sucesso, minha rainha!';
    } else {
        $message = 'Ops, não deu pra excluir: ' . $conn->error . '. Verifique se a questão tá vinculada a alguma prova!';
    }
    $stmt->close();
}

// Edit mode
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $sql = "SELECT * FROM questãotb WHERE id_questão = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_question = $result->fetch_assoc();
    $stmt->close();
}

// Read
$sql = "SELECT q.*, d.nome_disciplina, a.nome_assunto 
        FROM questãotb q 
        JOIN disciplinatb d ON q.id_disciplina = d.id_disciplina 
        JOIN assuntotb a ON q.id_assunto = a.id_assunto";
$params = [];
$types = '';
if ($disciplineId && $assuntoId) {
    $sql .= " WHERE q.id_disciplina = ? AND q.id_assunto = ?";
    $params = [$disciplineId, $assuntoId];
    $types = "ii";
} elseif ($disciplineId) {
    $sql .= " WHERE q.id_disciplina = ?";
    $params = [$disciplineId];
    $types = "i";
} elseif ($assuntoId) {
    $sql .= " WHERE q.id_assunto = ?";
    $params = [$assuntoId];
    $types = "i";
}
$questions = [];
if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
    $stmt->close();
} else {
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecionar Questões</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-container { max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; }
        .question-list { margin-top: 20px; }
        .question-item { padding: 10px; border-bottom: 1px solid #eee; }
        .question-item a { margin-left: 10px; color: #d81b60; }
        label { display: block; margin: 10px 0 5px; }
        input, textarea, select { width: 100%; padding: 8px; margin-bottom: 10px; }
        button { padding: 10px 20px; background: #f06292; color: white; border: none; cursor: pointer; }
        button:hover { background: #ec407a; }
        .message { color: #d81b60; margin: 10px 0; }
        .filter-form { margin-bottom: 20px; }
        .filter-form select { width: 48%; display: inline-block; margin-right: 4%; }
    </style>
    <script>
        function loadAssuntos() {
            const disciplinaId = document.getElementById('disciplina').value;
            const assuntoSelect = document.getElementById('assunto');
            assuntoSelect.innerHTML = '<option value="">Todos</option>';

            if (disciplinaId) {
                fetch('get_assuntos.php?disciplina=' + disciplinaId)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(assunto => {
                            const option = document.createElement('option');
                            option.value = assunto.id_assunto;
                            option.textContent = assunto.nome_assunto;
                            assuntoSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Erro ao carregar assuntos:', error));
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Selecionar Questões</h1>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="cadastro.php">Cadastrar</a></li>
                <li><a href="selecionar.php">Selecionar</a></li>
                <li><a href="gerar.php">Gerar Arquivo</a></li>
                <li><a href="disciplinas.php">Disciplinas</a></li>
                <li><a href="assuntos.php">Assuntos</a></li>
                <li><a href="sobre.php">Sobre</a></li>
                <li><a href="contato.php">Contato</a></li>
                <li><a href="ajuda.php">Ajuda</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <?php if ($edit_mode): ?>
                <h2>Editar Questão</h2>
                <?php if ($message): ?>
                    <p class="message"><?php echo $message; ?></p>
                <?php endif; ?>
                <div class="form-container">
                    <form method="POST">
                        <input type="hidden" name="edit_id" value="<?php echo $edit_question['id_questão']; ?>">
                        <label>Disciplina:
                            <select id="disciplina" name="disciplina" required onchange="loadAssuntos()">
                                <option value="">Selecione</option>
                                <?php
                                $result = $conn->query("SELECT * FROM disciplinatb");
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($edit_question['id_disciplina'] == $row['id_disciplina']) ? 'selected' : '';
                                    echo "<option value='{$row['id_disciplina']}' $selected>{$row['nome_disciplina']}</option>";
                                }
                                ?>
                            </select>
                        </label>
                        <label>Assunto:
                            <select id="assunto" name="assunto" required>
                                <option value="">Selecione</option>
                                <?php
                                $sql = "SELECT * FROM assuntotb WHERE id_disciplina = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $edit_question['id_disciplina']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($edit_question['id_assunto'] == $row['id_assunto']) ? 'selected' : '';
                                    echo "<option value='{$row['id_assunto']}' $selected>{$row['nome_assunto']}</option>";
                                }
                                $stmt->close();
                                ?>
                            </select>
                        </label>
                        <label>Enunciado:
                            <textarea name="enunciado" required><?php echo htmlspecialchars(preg_replace("/^(.*?\n.*?)\n*$/", "$1", $edit_question['enunciado'])); ?></textarea>
                        </label>
                        <?php
                        $alternativas = explode("\n", $edit_question['enunciado']);
                        $alt_a = isset($alternativas[1]) ? trim(substr($alternativas[1], 3)) : '';
                        $alt_b = isset($alternativas[2]) ? trim(substr($alternativas[2], 3)) : '';
                        $alt_c = isset($alternativas[3]) ? trim(substr($alternativas[3], 3)) : '';
                        $alt_d = isset($alternativas[4]) ? trim(substr($alternativas[4], 3)) : '';
                        ?>
                        <label>Alternativa A: <input type="text" name="alternativa_a" value="<?php echo htmlspecialchars($alt_a); ?>" required></label>
                        <label>Alternativa B: <input type="text" name="alternativa_b" value="<?php echo htmlspecialchars($alt_b); ?>" required></label>
                        <label>Alternativa C: <input type="text" name="alternativa_c" value="<?php echo htmlspecialchars($alt_c); ?>" required></label>
                        <label>Alternativa D: <input type="text" name="alternativa_d" value="<?php echo htmlspecialchars($alt_d); ?>" required></label>
                        <label>Resposta Correta:
                            <select name="resposta" required>
                                <option value="a)" <?php echo $edit_question['resposta'] == 'a)' ? 'selected' : ''; ?>>A</option>
                                <option value="b)" <?php echo $edit_question['resposta'] == 'b)' ? 'selected' : ''; ?>>B</option>
                                <option value="c)" <?php echo $edit_question['resposta'] == 'c)' ? 'selected' : ''; ?>>C</option>
                                <option value="d)" <?php echo $edit_question['resposta'] == 'd)' ? 'selected' : ''; ?>>D</option>
                            </select>
                        </label>
                        <button type="submit">Atualizar Questão</button>
                    </form>
                </div>
            <?php else: ?>
                <h2>Selecionar Questões para Prova</h2>
                <?php if ($message): ?>
                    <p class="message"><?php echo $message; ?></p>
                <?php endif; ?>
                <div class="filter-form">
                    <form method="POST">
                        <label>Filtrar por Disciplina:
                            <select id="disciplina" name="disciplina" onchange="loadAssuntos()">
                                <option value="">Todas</option>
                                <?php
                                $result = $conn->query("SELECT * FROM disciplinatb");
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($disciplineId == $row['id_disciplina']) ? 'selected' : '';
                                    echo "<option value='{$row['id_disciplina']}' $selected>{$row['nome_disciplina']}</option>";
                                }
                                ?>
                            </select>
                        </label>
                        <label>Filtrar por Assunto:
                            <select id="assunto" name="assunto">
                                <option value="">Todos</option>
                                <?php
                                if ($disciplineId) {
                                    $sql = "SELECT * FROM assuntotb WHERE id_disciplina = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("i", $disciplineId);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        $selected = ($assuntoId == $row['id_assunto']) ? 'selected' : '';
                                        echo "<option value='{$row['id_assunto']}' $selected>{$row['nome_assunto']}</option>";
                                    }
                                    $stmt->close();
                                }
                                ?>
                            </select>
                        </label>
                        <button type="submit">Filtrar</button>
                    </form>
                </div>
                <form method="POST" action="gerar.php">
                    <div class="question-list">
                        <?php if (empty($questions)): ?>
                            <p>Nenhuma questão cadastrada, minha querida! Vamos criar algumas em <a href="cadastro.php">Cadastrar</a>? 💕</p>
                        <?php else: ?>
                            <?php foreach ($questions as $question): ?>
                                <div class="question-item">
                                    <input type="checkbox" name="selected_questions[]" value="<?php echo $question['id_questão']; ?>">
                                    <p><strong><?php echo htmlspecialchars($question['nome_disciplina']); ?> - <?php echo htmlspecialchars($question['nome_assunto']); ?></strong></p>
                                    <p><?php echo nl2br(htmlspecialchars($question['enunciado'])); ?></p>
                                    <p><strong>Resposta:</strong> <?php echo htmlspecialchars($question['resposta']); ?></p>
                                    <a href="?edit=<?php echo $question['id_questão']; ?>">Editar</a>
                                    <a href="?delete=<?php echo $question['id_questão']; ?>" onclick="return confirm('Tem certeza, minha diva?')">Excluir</a>
                                </div>
                            <?php endforeach; ?>
                            <button type="submit">Gerar Prova com Selecionadas</button>
                        <?php endif; ?>
                    </div>
                </form>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>© 2025 Banco de Questões</p>
    </footer>
</body>
</html>