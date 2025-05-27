<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
error_log("Iniciando selecionar.php");

try {
    require_once 'conexao.php';
    require_once 'function.php';
    error_log("Arquivos incluídos com sucesso");
} catch (Exception $e) {
    error_log("Erro ao incluir arquivos: " . $e->getMessage());
    die("Erro ao carregar o sistema. Verifique os logs.");
}

// Removido session_start() e verificação de login para acesso livre
// session_start();
// if (!verificarLogin()) {
//     header("Location: login.php");
//     exit();
// }

$message = '';
$selected_questions = [];

try {
    $disciplinas = listarDisciplinas($conn);
    $assuntos = listarAssuntos($conn);
    $filters = array_map('intval', $_GET); // Sanitiza filtros
    $questoes = mostrarQuestoes($conn, $filters);
    error_log("Carregados: " . count($disciplinas) . " disciplinas, " . count($assuntos) . " assuntos, " . count($questoes) . " questões");
} catch (Exception $e) {
    error_log("Erro ao carregar dados: " . $e->getMessage());
    $disciplinas = $assuntos = $questoes = [];
    $message = 'Erro ao carregar dados. Verifique o banco ou cont...'; // Mensagem abreviada, estava cortada
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Prova - EduProvas</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Estilos adicionais para layout de seleção */
        .filter-section, .question-list-section {
            background-color: #f9f9f9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .filter-section label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .filter-section select, .filter-section input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .question-item {
            border: 1px solid #eee;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 6px;
            background-color: #fff;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .question-item input[type="checkbox"] {
            margin-top: 5px;
            transform: scale(1.2);
        }
        .question-content {
            flex-grow: 1;
        }
        .question-content p {
            margin: 0 0 5px 0;
        }
        .question-content small {
            color: #666;
            font-style: italic;
        }
        .generate-actions {
            text-align: right;
            margin-top: 20px;
        }
        .selected-count {
            font-weight: bold;
            margin-top: 10px;
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
        <h2>Gerar Prova</h2>

        <div class="filter-section">
            <h3>Filtrar Questões</h3>
            <label for="filterDisciplina">Disciplina:</label>
            <select id="filterDisciplina">
                <option value="">Todas as Disciplinas</option>
                <?php foreach ($disciplinas as $disciplina): ?>
                    <option value="<?php echo $disciplina['id_disciplina']; ?>">
                        <?php echo htmlspecialchars($disciplina['nome_disciplina']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="filterAssunto">Assunto:</label>
            <select id="filterAssunto">
                <option value="">Todos os Assuntos</option>
                <?php foreach ($assuntos as $assunto): ?>
                    <option value="<?php echo $assunto['id_assunto']; ?>" class="assunto-option" data-disciplina="<?php echo $assunto['id_disciplina']; ?>">
                        <?php echo htmlspecialchars($assunto['nome_assunto']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="numQuestions">Número de Questões (opcional):</label>
            <input type="number" id="numQuestions" min="1" placeholder="Deixe em branco para todas">
        </div>

        <form action="gerar.php" method="POST" id="generateForm">
            <div class="question-list-section">
                <h3>Questões Disponíveis</h3>
                <div class="selected-count">Questões selecionadas: <span id="selectedCount">0</span></div>
                <div id="questionsContainer">
                    <?php if ($questoes):
                        foreach ($questoes as $questao):
                            $assunto_info = null;
                            foreach ($assuntos as $a) {
                                if ($a['id_assunto'] == $questao['id_assunto']) {
                                    $assunto_info = $a;
                                    break;
                                }
                            }
                            $disciplina_id = $assunto_info['id_disciplina'] ?? '';
                            $assunto_nome = $assunto_info['nome_assunto'] ?? 'N/A';
                            $disciplina_nome = 'N/A';
                            if ($disciplina_id) {
                                foreach ($disciplinas as $d) {
                                    if ($d['id_disciplina'] == $disciplina_id) {
                                        $disciplina_nome = $d['nome_disciplina'];
                                        break;
                                    }
                                }
                            }
                    ?>
                            <div class="question-item"
                                 data-id="<?php echo htmlspecialchars($questao['id_questao']); ?>"
                                 data-assunto="<?php echo htmlspecialchars($questao['id_assunto']); ?>"
                                 data-disciplina="<?php echo htmlspecialchars($disciplina_id); ?>">
                                <input type="checkbox" name="questoes_selecionadas[]" value="<?php echo htmlspecialchars($questao['id_questao']); ?>" />
                                <div class="question-content">
                                    <p><strong>Questão:</strong> <?php echo htmlspecialchars($questao['pergunta']); ?></p>
                                    <small>Disciplina: <?php echo htmlspecialchars($disciplina_nome); ?> | Assunto: <?php echo htmlspecialchars($assunto_nome); ?></small><br>
                                    <small>A) <?php echo htmlspecialchars($questao['resposta_a']); ?> | B) <?php echo htmlspecialchars($questao['resposta_b']); ?> | C) <?php echo htmlspecialchars($questao['resposta_c']); ?> | D) <?php echo htmlspecialchars($questao['resposta_d']); ?></small><br>
                                    <small style="color: green;">Correta: <?php echo htmlspecialchars($questao['resposta_correta']); ?></small>
                                </div>
                            </div>
                        <?php endforeach;
                    else: ?>
                        <p>Nenhuma questão disponível para seleção. Cadastre questões primeiro.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="generate-actions">
                <label for="formatoOutput">Formato:</label>
                <select id="formatoOutput" name="formato">
                    <option value="pdf">PDF</option>
                    <option value="word">Word</option>
                </select>
                <button type="submit" class="btn btn-primary">Gerar Prova</button>
            </div>
        </form>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 EduProvas. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        $(document).ready(function() {
            var allQuestions = $('#questionsContainer .question-item');

            function filterQuestions() {
                var selectedDisciplina = $('#filterDisciplina').val();
                var selectedAssunto = $('#filterAssunto').val();
                var numQuestions = parseInt($('#numQuestions').val());

                allQuestions.hide().removeClass('filtered-match'); // Esconde tudo e remove a classe de match

                var matchingQuestions = allQuestions.filter(function() {
                    var questionDisciplina = $(this).data('disciplina');
                    var questionAssunto = $(this).data('assunto');

                    var disciplinaMatch = (selectedDisciplina === '' || questionDisciplina == selectedDisciplina);
                    var assuntoMatch = (selectedAssunto === '' || questionAssunto == selectedAssunto);

                    return disciplinaMatch && assuntoMatch;
                });

                if (!isNaN(numQuestions) && numQuestions > 0) {
                    matchingQuestions.slice(0, numQuestions).show().addClass('filtered-match');
                    matchingQuestions.slice(numQuestions).hide().removeClass('filtered-match');
                } else {
                    matchingQuestions.show().addClass('filtered-match');
                }

                // Desmarcar questões que não estão mais visíveis
                allQuestions.each(function() {
                    if (!$(this).is(':visible')) {
                        $(this).find('input[type="checkbox"]').prop('checked', false);
                    }
                });

                updateSelectedCount();
            }

            function updateAssuntoOptions() {
                var selectedDisciplina = $('#filterDisciplina').val();
                var assuntoOptions = $('.assunto-option');

                assuntoOptions.hide();
                $('#filterAssunto').val(''); // Reseta o assunto selecionado

                if (selectedDisciplina === '') {
                    assuntoOptions.show();
                } else {
                    assuntoOptions.filter('[data-disciplina="' + selectedDisciplina + '"]').show();
                }
            }

            function updateSelectedCount() {
                var count = $('#questionsContainer input[type="checkbox"]:checked').length;
                $('#selectedCount').text(count);
            }

            $('#filterDisciplina').change(function() {
                updateAssuntoOptions();
                filterQuestions();
            });
            $('#filterAssunto').change(filterQuestions);
            $('#numQuestions').on('input', filterQuestions);

            $('#questionsContainer').on('change', 'input[type="checkbox"]', updateSelectedCount);

            // Inicializar filtros e contagem
            updateAssuntoOptions(); // Para esconder assuntos não relacionados à disciplina inicial
            filterQuestions();
        });
    </script>
</body>
</html>