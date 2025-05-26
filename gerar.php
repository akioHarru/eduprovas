<?php
require_once 'conexao.php';
$questions = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_questions'])) {
    $selected_ids = $_POST['selected_questions'];
    if (!empty($selected_ids)) {
        $placeholders = implode(',', array_fill(0, count($selected_ids), '?'));
        $sql = "SELECT q.*, d.nome_disciplina FROM questãotb q 
                JOIN disciplinatb d ON q.id_disciplina = d.id_disciplina 
                WHERE q.id_questão IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat('i', count($selected_ids)), ...$selected_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Prova - Banco de Questões</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .exam-container { max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; }
        .exam-header { text-align: center; margin-bottom: 20px; }
        .question { margin-bottom: 20px; }
        .question p { margin: 5px 0; }
        .button-group { margin-top: 20px; text-align: center; }
        button { padding: 10px 20px; margin: 0 10px; cursor: pointer; }
        @media print {
            nav, .button-group { display: none; }
            .exam-container { border: none; }
        }
    </style>
    <script>
        function saveAsWord() {
            const content = document.querySelector('.exam-container').innerHTML;
            const blob = new Blob(['<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' + content + '</body></html>'], 
                { type: 'application/msword' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'prova.doc';
            link.click();
        }
        function printAsPDF() {
            window.print();
        }
    </script>
</head>
<body>
    <header>
        <h1>Gerar Prova</h1>
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
            <h2>Prova Gerada</h2>
            <?php if (!empty($questions)): ?>
                <div class="exam-container">
                    <div class="exam-header">
                        <h2>Prova - Banco de Questões</h2>
                        <p>Disciplina: <?php echo htmlspecialchars($questions[0]['nome_disciplina']); ?></p>
                    </div>
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="question">
                            <p><strong><?php echo ($index + 1) . '. '; ?></strong><?php echo nl2br(htmlspecialchars($question['enunciado'])); ?></p>
                            <p><strong>Resposta:</strong> <?php echo htmlspecialchars($question['resposta']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="button-group">
                    <button onclick="printAsPDF()">Salvar como PDF</button>
                    <button onclick="saveAsWord()">Salvar como Word</button>
                </div>
            <?php else: ?>
                <p>Nenhuma questão selecionada, minha querida! Volte para <a href="selecionar.php">Selecionar</a> e escolha algumas. 💕</p>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>© 2025 Banco de Questões</p>
    </footer>
</body>
</html>