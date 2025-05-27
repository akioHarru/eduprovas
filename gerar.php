<?php
require_once 'conexao.php';
require_once 'function.php';

// Removido session_start() e verificação de login para acesso livre
// session_start();
// if (!verificarLogin()) {
//     header("Location: login.php");
//     exit();
// }

// Este arquivo provavelmente recebe dados de selecionar.php para gerar a prova.
// As bibliotecas para geração de PDF/Word (como TCPDF, PHPWord) não estão incluídas aqui.

// Exemplo básico de como receber os dados e preparar para geração
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questoes_selecionadas_ids = $_POST['questoes_selecionadas'] ?? [];
    $formato = $_POST['formato'] ?? 'pdf'; // 'pdf' ou 'word'

    if (!empty($questoes_selecionadas_ids)) {
        // Buscar detalhes completos das questões selecionadas
        $placeholders = implode(',', array_fill(0, count($questoes_selecionadas_ids), '?'));
        $sql = "SELECT q.*, a.nome_assunto, d.nome_disciplina
                FROM questao_tb q
                JOIN assuntotb a ON q.id_assunto = a.id_assunto
                JOIN disciplinatb d ON a.id_disciplina = d.id_disciplina
                WHERE q.id_questao IN ($placeholders)";
        $stmt = $conn->prepare($sql);

        // Bind_param dinâmico
        $types = str_repeat('i', count($questoes_selecionadas_ids));
        $stmt->bind_param($types, ...$questoes_selecionadas_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        $questoes_para_prova = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Aqui você integraria a lógica para gerar o PDF ou Word
        // Por exemplo:
        if ($formato === 'pdf') {
            // Incluir a biblioteca TCPDF e gerar o PDF
            // require_once('tcpdf/tcpdf.php');
            // ... (código para gerar PDF) ...
            echo "Gerando PDF com " . count($questoes_para_prova) . " questões.";
            // Para demonstração, pode ser um download simples ou exibir o conteúdo
            // header('Content-Type: application/pdf');
            // header('Content-Disposition: attachment; filename="prova.pdf"');
            // echo $pdf_content; // Assumindo que $pdf_content é o PDF gerado
            // exit();
        } elseif ($formato === 'word') {
            // Incluir a biblioteca PHPWord e gerar o Word
            // require_once('phpword/autoload.php');
            // ... (código para gerar Word) ...
            echo "Gerando documento Word com " . count($questoes_para_prova) . " questões.";
            // header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            // header('Content-Disposition: attachment; filename="prova.docx"');
            // echo $word_content; // Assumindo que $word_content é o Word gerado
            // exit();
        } else {
            echo "<p class='error'>Formato de arquivo inválido.</p>";
        }
    } else {
        echo "<p class='error'>Nenhuma questão selecionada para gerar a prova.</p>";
    }
} else {
    echo "<p class='error'>Acesso inválido a esta página.</p>";
}

// Em um ambiente real, você provavelmente não teria muito HTML aqui
// após o processamento, pois o download iniciaria.
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Prova - EduProvas</title>
    <link rel="stylesheet" href="css/style.css">
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
        <h2>Processamento da Geração da Prova</h2>
        <p>Este é o script que processaria as questões selecionadas e geraria o arquivo PDF/Word.</p>
        <p>Se você não viu um download, verifique as mensagens acima para depuração.</p>
        <p><a href="selecionar.php" class="btn btn-secondary">Voltar para Seleção de Questões</a></p>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 EduProvas. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>