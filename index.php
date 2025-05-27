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
    <title>Bem-vindo ao EduProvas</title>
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

    <main class="container hero">
        <section class="hero-content">
            <h2>Crie Provas e Avaliações de Forma Simples e Eficiente</h2>
            <p>Com o EduProvas, você organiza suas questões por disciplina e assunto, e gera provas personalizadas em poucos cliques.</p>
            <a href="cadastro.php" class="btn btn-primary">Começar Agora</a>
            <a href="sobre.php" class="btn btn-secondary">Saiba Mais</a>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 EduProvas. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>