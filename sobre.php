<?php
require_once 'conexao.php';
require_once 'function.php';

// Removido session_start() e verificação de login para acesso livre
// session_start();
// if (!verificarLogin()) {
//     header("Location: login.php");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre o EduProvas</title>
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
        <h2>Sobre o EduProvas</h2>
        <p>O EduProvas é uma ferramenta intuitiva e eficiente desenvolvida para auxiliar educadores na criação e gerenciamento de provas e avaliações.</p>
        <p>Nosso objetivo é simplificar o processo de elaboração de testes, permitindo que professores organizem suas questões por disciplinas e assuntos, e gerem documentos de prova de forma rápida e personalizável.</p>
        <p><strong>Recursos Principais:</strong></p>
        <ul>
            <li>Cadastro e edição de questões detalhadas.</li>
            <li>Organização de questões por disciplina e assunto.</li>
            <li>Seleção flexível de questões para compor a prova.</li>
            <li>Geração de provas em formatos PDF ou Word (requer bibliotecas específicas).</li>
            <li>Interface de usuário amigável e responsiva.</li>
        </ul>
        <p>Acreditamos que, ao otimizar a criação de provas, o EduProvas contribui para que os educadores possam dedicar mais tempo ao que realmente importa: o ensino e o aprendizado dos alunos.</p>
        <p>Para dúvidas, sugestões ou suporte, entre em contato conosco.</p>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 EduProvas. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>