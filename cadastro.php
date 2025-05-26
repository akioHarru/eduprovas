<?php
require_once 'conexao.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $disciplina = $_POST['disciplina'];
    $enunciado = $_POST['enunciado'];
    $alternativa_a = $_POST['alternativa_a'];
    $alternativa_b = $_POST['alternativa_b'];
    $alternativa_c = $_POST['alternativa_c'];
    $alternativa_d = $_POST['alternativa_d'];
    $resposta = $_POST['resposta'];
    $assunto = $_POST['assunto'];
    $tipo = 'Assertativa';

    $full_enunciado = "$enunciado\na) $alternativa_a\nb) $alternativa_b\nc) $alternativa_c\nd) $alternativa_d";

    $sql = "INSERT INTO questãotb (id_disciplina, enunciado, resposta, tipo, id_assunto) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $disciplina, $full_enunciado, $resposta, $tipo, $assunto);
    if ($stmt->execute()) {
        $message = 'Questão cadastrada com sucesso, minha linda!';
    } else {
        $message = 'Ops, algo deu errado: ' . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Questão</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-container { max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; }
        label { display: block; margin: 10px 0 5px; }
        input, textarea, select { width: 100%; padding: 8px; margin-bottom: 10px; }
        button { padding: 10px 20px; background: #f06292; color: white; border: none; cursor: pointer; }
        button:hover { background: #ec407a; }
        .message { color: #d81b60; margin: 10px 0; }
    </style>
    <script>
        function loadAssuntos() {
            const disciplinaId = document.getElementById('disciplina').value;
            const assuntoSelect = document.getElementById('assunto');
            assuntoSelect.innerHTML = '<option value="">Selecione</option>';

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
        <h1>Cadastrar Questão</h1>
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
            <h2>Nova Questão</h2>
            <?php if ($message): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
            <div class="form-container">
                <form method="POST">
                    <label>Disciplina:
                        <select id="disciplina" name="disciplina" required onchange="loadAssuntos()">
                            <option value="">Selecione</option>
                            <?php
                            $result = $conn->query("SELECT * FROM disciplinatb");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_disciplina']}'>{$row['nome_disciplina']}</option>";
                            }
                            ?>
                        </select>
                    </label>
                    <label>Assunto:
                        <select id="assunto" name="assunto" required>
                            <option value="">Selecione</option>
                        </select>
                    </label>
                    <label>Enunciado:
                        <textarea name="enunciado" required></textarea>
                    </label>
                    <label>Alternativa A: <input type="text" name="alternativa_a" required></label>
                    <label>Alternativa B: <input type="text" name="alternativa_b" required></label>
                    <label>Alternativa C: <input type="text" name="alternativa_c" required></label>
                    <label>Alternativa D: <input type="text" name="alternativa_d" required></label>
                    <label>Resposta Correta:
                        <select name="resposta" required>
                            <option value="a)">A</option>
                            <option value="b)">B</option>
                            <option value="c)">C</option>
                            <option value="d)">D</option>
                        </select>
                    </label>
                    <button type="submit">Cadastrar Questão</button>
                </form>
            </div>
        </section>
    </main>
    <footer>
        <p>© 2025 Banco de Questões</p>
    </footer>
</body>
</html>