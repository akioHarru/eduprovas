<?php
require_once 'conexao.php';
require_once 'function.php';
require_once 'get_assuntos.php'; // Inclui o arquivo que contém a função listarAssuntos
// session_start(); // Removido para desativar a exigência de login

$message_assunto = '';
$edit_mode_assunto = false;
$edit_assunto = null;

$message_disciplina = '';
$edit_mode_disciplina = false;
$edit_disciplina = null;

$message_questao = '';
$edit_mode_questao = false;
$edit_question = null;

// Inicializa variáveis de dados para evitar "Undefined variable"
$data_assunto = [];
$data_disciplina = [];
$data_questao = [];

// Listar disciplinas e assuntos para o formulário
$disciplines = listarDisciplinas($conn); // Usando a função para listar
$assuntos = listarAssuntos($conn);
$questions = mostrarQuestoes($conn); // Carregar todas as questões inicialmente

// Gerenciamento de Disciplinas (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_disciplina'])) {
    // Captura os dados do POST
    $id_disciplina_post = filter_var($_POST['edit_id_disciplina'] ?? null, FILTER_VALIDATE_INT);
    $nome_disciplina_post = $_POST['nome_disciplina'] ?? '';
    $id_professor_post = filter_var($_POST['id_professor'] ?? 1, FILTER_VALIDATE_INT);

    // Validação básica do professor
    if ($id_professor_post === false || $id_professor_post === 0) {
        $message_disciplina = 'Erro: ID de professor inválido. Por favor, insira um número válido.';
    } else {
        // Decide se é cadastro ou edição
        if ($id_disciplina_post !== false && $id_disciplina_post !== null) { // É edição
            $result = editarDisciplina($conn, $id_disciplina_post, $nome_disciplina_post);
        } else { // É cadastro
            $result = cadastrarDisciplina($conn, $nome_disciplina_post);
        }
        $message_disciplina = $result['message'];
    }
    // Atualizar a lista de disciplinas após uma operação
    $disciplines = listarDisciplinas($conn);
}

// Lógica para editar disciplina (GET)
if (isset($_GET['edit_disciplina'])) {
    $edit_id_disciplina = filter_var($_GET['edit_disciplina'], FILTER_VALIDATE_INT);
    if ($edit_id_disciplina !== false) {
        $edit_mode_disciplina = true;
        $stmt = $conn->prepare("SELECT * FROM disciplinatb WHERE id_disciplina = ?");
        $stmt->bind_param("i", $edit_id_disciplina);
        $stmt->execute();
        $result = $stmt->get_result();
        $edit_disciplina = $result->fetch_assoc();
        $stmt->close();
    }
}

// Lógica para excluir disciplina (GET)
if (isset($_GET['delete_disciplina'])) {
    $result = excluirDisciplina($conn, $_GET['delete_disciplina']);
    $message_disciplina = $result['message'];
    $disciplines = listarDisciplinas($conn); // Recarrega a lista de disciplinas
}


// Gerenciamento de Assuntos (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_assunto'])) {
    $data_assunto = [
        'id_assunto' => filter_var($_POST['edit_id_assunto'] ?? null, FILTER_VALIDATE_INT),
        'nome_assunto' => $_POST['nome_assunto'] ?? '', // Garante que é uma string
        'id_disciplina' => filter_var($_POST['id_disciplina'], FILTER_VALIDATE_INT) // Garante que é um int ou false
    ];

    // Validação robusta antes de chamar a função
    if (empty($data_assunto['nome_assunto'])) {
        $message_assunto = 'Erro: Por favor, preencha o nome do assunto.';
    } elseif ($data_assunto['id_disciplina'] === false || $data_assunto['id_disciplina'] === 0) {
        $message_assunto = 'Erro: Por favor, selecione uma disciplina válida.';
    } else {
        if ($data_assunto['id_assunto'] !== false && $data_assunto['id_assunto'] !== null) { // Verifica se é edição
            $result = editarAssunto($conn, $data_assunto);
        } else {
            $result = cadastrarAssunto($conn, $data_assunto);
        }
        $message_assunto = $result['message'];
    }
    // Atualizar a lista de assuntos após uma operação
    $assuntos = listarAssuntos($conn);
}

// Lógica para editar assunto (GET)
if (isset($_GET['edit_assunto'])) {
    $edit_id_assunto = filter_var($_GET['edit_assunto'], FILTER_VALIDATE_INT);
    if ($edit_id_assunto !== false) {
        $edit_mode_assunto = true;
        // Buscar o assunto para preencher o formulário
        $stmt = $conn->prepare("SELECT * FROM assuntotb WHERE id_assunto = ?");
        $stmt->bind_param("i", $edit_id_assunto);
        $stmt->execute();
        $result = $stmt->get_result();
        $edit_assunto = $result->fetch_assoc();
        $stmt->close();
    }
}

// Lógica para excluir assunto (GET)
if (isset($_GET['delete_assunto'])) {
    $result = excluirAssunto($conn, $_GET['delete_assunto']);
    $message_assunto = $result['message'];
    // Atualizar a lista de assuntos após uma operação
    $assuntos = listarAssuntos($conn);
}


// Gerenciamento de Questões (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enunciado'])) {
    $data_questao = [
        'id_questao' => filter_var($_POST['edit_id_questao'] ?? null, FILTER_VALIDATE_INT),
        'enunciado' => $_POST['enunciado'] ?? '',
        'alternativas' => [
            $_POST['alternativa_a'] ?? '',
            $_POST['alternativa_b'] ?? '',
            $_POST['alternativa_c'] ?? '',
            $_POST['alternativa_d'] ?? ''
        ],
        'resposta' => $_POST['resposta'] ?? '',
        'disciplina' => filter_var($_POST['disciplina_questao'], FILTER_VALIDATE_INT) ?? 0,
        'assunto' => filter_var($_POST['assunto_questao'], FILTER_VALIDATE_INT) ?? 0
    ];

    // Validação básica para Questões antes de chamar a função
    if (empty($data_questao['enunciado']) || empty($data_questao['resposta']) ||
        $data_questao['disciplina'] === false || $data_questao['assunto'] === false ||
        // Verifica se QUALQUER uma das alternativas está vazia
        in_array('', $data_questao['alternativas'], true)) {
        $message_questao = 'Erro: Por favor, preencha todos os campos da questão (enunciado, alternativas e resposta).';
    } else {
        if ($data_questao['id_questao'] !== false && $data_questao['id_questao'] !== null) { // Verifica se é edição
            $result = editarQuestao($conn, $data_questao);
        } else {
            $result = cadastrarQuestao($conn, $data_questao);
        }
        $message_questao = $result['message'];
    }
    $questions = mostrarQuestoes($conn); // Recarrega as questões
}

// Lógica para editar questão (GET)
if (isset($_GET['edit_questao'])) {
    $edit_id_questao = filter_var($_GET['edit_questao'], FILTER_VALIDATE_INT);
    if ($edit_id_questao !== false) {
        $edit_mode_questao = true;
        // CORREÇÃO: Usando a tabela correta 'questãotb'
        $stmt = $conn->prepare("SELECT * FROM questãotb WHERE id_questao = ?");
        $stmt->bind_param("i", $edit_id_questao);
        $stmt->execute();
        $result = $stmt->get_result();
        $edit_question = $result->fetch_assoc();
        $stmt->close();
    }
}

// Lógica para excluir questão (GET)
if (isset($_GET['delete_questao'])) {
    $result = excluirQuestao($conn, $_GET['delete_questao']);
    $message_questao = $result['message'];
    $questions = mostrarQuestoes($conn); // Recarrega as questões
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Disciplinas, Assuntos e Questões</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">EduProvas</a>
            <ul class="nav-links">
                <li><a href="index.php">Início</a></li>
                <li><a href="cadastro.php">Questões</a></li>
                <li><a href="disciplinas.php">Disciplinas</a></li>
                <li><a href="selecionar.php">Selecionar Questões</a></li>
                <li><a href="sobre.php">Sobre</a></li>
            </ul>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <h1>Gerenciamento</h1>
            <p>Gerencie suas disciplinas, assuntos e questões aqui.</p>
        </div>
    </section>

    <section class="main-content">
        <div class="container">
            <div class="tab-nav">
                <button class="tab-button active" onclick="openTab(event, 'disciplinas')">Disciplinas</button>
                <button class="tab-button" onclick="openTab(event, 'assuntos')">Assuntos</button>
                <button class="tab-button" onclick="openTab(event, 'questoes')">Questões</button>
            </div>

            <div id="disciplinas" class="tab-content active">
                <h2><?php echo $edit_mode_disciplina ? 'Editar Disciplina' : 'Cadastrar Nova Disciplina'; ?></h2>
                <?php if (!empty($message_disciplina)): ?>
                    <p class="message"><?php echo htmlspecialchars($message_disciplina); ?></p>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="edit_id_disciplina" value="<?php echo htmlspecialchars($edit_disciplina['id_disciplina'] ?? ''); ?>">
                    <div class="form-group">
                        <label for="nome_disciplina">Nome da Disciplina:</label>
                        <input type="text" id="nome_disciplina" name="nome_disciplina" value="<?php echo htmlspecialchars($edit_disciplina['nome_disciplina'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="id_professor">Professor (ID):</label>
                        <input type="number" id="id_professor" name="id_professor" value="<?php echo htmlspecialchars($edit_disciplina['id_professor'] ?? 1); ?>" required>
                        <small>Preencha com o ID do professor (Ex: 1)</small>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode_disciplina ? 'Atualizar Disciplina' : 'Cadastrar Disciplina'; ?></button>
                </form>

                <h2>Disciplinas Cadastradas</h2>
                <?php if (empty($disciplines)): ?>
                    <p>Nenhuma disciplina cadastrada.</p>
                <?php else: ?>
                    <div class="discipline-list">
                        <?php foreach ($disciplines as $discipline): ?>
                            <div class="discipline-item">
                                <span><?php echo htmlspecialchars($discipline['nome_disciplina']); ?> (ID: <?php echo htmlspecialchars($discipline['id_disciplina']); ?>)</span>
                                <div>
                                    <a href="?edit_disciplina=<?php echo $discipline['id_disciplina']; ?>">Editar</a>
                                    <a href="?delete_disciplina=<?php echo $discipline['id_disciplina']; ?>" onclick="return confirm('Confirmar exclusão? Esta ação também pode afetar assuntos e questões vinculadas se não houver restrições no DB.')">Excluir</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div id="assuntos" class="tab-content">
                <h2><?php echo $edit_mode_assunto ? 'Editar Assunto' : 'Cadastrar Novo Assunto'; ?></h2>
                <?php if (!empty($message_assunto)): ?>
                    <p class="message"><?php echo htmlspecialchars($message_assunto); ?></p>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="edit_id_assunto" value="<?php echo htmlspecialchars($edit_assunto['id_assunto'] ?? ''); ?>">
                    <div class="form-group">
                        <label for="nome_assunto">Nome do Assunto:</label>
                        <input type="text" id="nome_assunto" name="nome_assunto" value="<?php echo htmlspecialchars($edit_assunto['nome_assunto'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="id_disciplina_assunto">Disciplina:</label>
                        <select id="id_disciplina_assunto" name="id_disciplina" required>
                            <option value="">Selecione uma Disciplina</option>
                            <?php foreach ($disciplines as $discipline): ?>
                                <option value="<?php echo htmlspecialchars($discipline['id_disciplina']); ?>"
                                    <?php echo ($edit_assunto['id_disciplina'] ?? '') == $discipline['id_disciplina'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($discipline['nome_disciplina']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode_assunto ? 'Atualizar Assunto' : 'Cadastrar Assunto'; ?></button>
                </form>

                <h2>Assuntos Cadastrados</h2>
                <?php if (empty($assuntos)): ?>
                    <p>Nenhum assunto cadastrado.</p>
                <?php else: ?>
                    <div class="discipline-list">
                        <?php foreach ($assuntos as $assunto_item): ?>
                            <div class="discipline-item">
                                <span><?php echo htmlspecialchars($assunto_item['nome_assunto']); ?> (Disciplina: <?php echo htmlspecialchars($assunto_item['nome_disciplina']); ?>)</span>
                                <div>
                                    <a href="?edit_assunto=<?php echo $assunto_item['id_assunto']; ?>">Editar</a>
                                    <a href="?delete_assunto=<?php echo $assunto_item['id_assunto']; ?>" onclick="return confirm('Confirmar exclusão?')">Excluir</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div id="questoes" class="tab-content">
                <h2><?php echo $edit_mode_questao ? 'Editar Questão' : 'Cadastrar Nova Questão'; ?></h2>
                <?php if (!empty($message_questao)): ?>
                    <p class="message"><?php echo htmlspecialchars($message_questao); ?></p>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="edit_id_questao" value="<?php echo htmlspecialchars($edit_question['id_questao'] ?? ''); ?>">
                    <div class="form-group">
                        <label for="disciplina_questao">Disciplina:</label>
                        <select id="disciplina_questao" name="disciplina_questao" onchange="loadAssuntosQuestoes()" required>
                            <option value="">Selecione uma Disciplina</option>
                            <?php foreach ($disciplines as $discipline): ?>
                                <option value="<?php echo htmlspecialchars($discipline['id_disciplina']); ?>"
                                    <?php echo ($edit_question['id_disciplina'] ?? '') == $discipline['id_disciplina'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($discipline['nome_disciplina']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assunto_questao">Assunto:</label>
                        <select id="assunto_questao" name="assunto_questao" required>
                            <option value="">Selecione</option>
                            <?php // Os assuntos serão carregados via JavaScript ou aqui se já houver um $edit_question
                            if ($edit_mode_questao && $edit_question && $edit_question['id_disciplina']) {
                                $assuntos_questao_edit = listarAssuntos($conn, ['id_disciplina' => $edit_question['id_disciplina']]);
                                foreach ($assuntos_questao_edit as $assunto_q_edit) {
                                    echo '<option value="' . htmlspecialchars($assunto_q_edit['id_assunto']) . '"' .
                                            (($edit_question['id_assunto'] ?? '') == $assunto_q_edit['id_assunto'] ? ' selected' : '') . '>' .
                                            htmlspecialchars($assunto_q_edit['nome_assunto']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="enunciado">Enunciado da Questão:</label>
                        <textarea id="enunciado" name="enunciado" rows="5" required><?php echo htmlspecialchars($edit_question['enunciado'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="alternativa_a">Alternativa A:</label>
                        <input type="text" id="alternativa_a" name="alternativa_a" value="<?php echo htmlspecialchars($edit_question['alternativa_a'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="alternativa_b">Alternativa B:</label>
                        <input type="text" id="alternativa_b" name="alternativa_b" value="<?php echo htmlspecialchars($edit_question['alternativa_b'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="alternativa_c">Alternativa C:</label>
                        <input type="text" id="alternativa_c" name="alternativa_c" value="<?php echo htmlspecialchars($edit_question['alternativa_c'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="alternativa_d">Alternativa D:</label>
                        <input type="text" id="alternativa_d" name="alternativa_d" value="<?php echo htmlspecialchars($edit_question['alternativa_d'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="resposta">Resposta Correta:</label>
                        <select id="resposta" name="resposta" required>
                            <option value="">Selecione</option>
                            <option value="a" <?php echo ($edit_question['resposta'] ?? '') == 'a' ? 'selected' : ''; ?>>A</option>
                            <option value="b" <?php echo ($edit_question['resposta'] ?? '') == 'b' ? 'selected' : ''; ?>>B</option>
                            <option value="c" <?php echo ($edit_question['resposta'] ?? '') == 'c' ? 'selected' : ''; ?>>C</option>
                            <option value="d" <?php echo ($edit_question['resposta'] ?? '') == 'd' ? 'selected' : ''; ?>>D</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode_questao ? 'Atualizar Questão' : 'Cadastrar Questão'; ?></button>
                </form>

                <h2>Questões Cadastradas</h2>
                <?php if (empty($questions)): ?>
                    <p>Nenhuma questão cadastrada.</p>
                <?php else: ?>
                    <div class="discipline-list">
                        <?php foreach ($questions as $question): ?>
                            <div class="discipline-item">
                                <span><?php echo htmlspecialchars($question['enunciado']); ?></span>
                                <div>
                                    <a href="?edit_questao=<?php echo $question['id_questao']; ?>">Editar</a>
                                    <a href="?delete_questao=<?php echo $question['id_questao']; ?>" onclick="return confirm('Confirmar exclusão?')">Excluir</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>© 2025 EduProvas. Todos os direitos reservados.</p>
            <ul class="footer-links">
                <li><a href="sobre.php">Política de Privacidade</a></li>
                <li><a href="sobre.php">Termos de Uso</a></li>
                <li><a href="sobre.php">Suporte</a></li>
            </ul>
        </div>
<footer class="footer">
        </footer>

    <script>
        // Função para controle das abas (que você já tem)
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tab-button");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";

            // Se for a aba de questões e não estiver em modo de edição, carrega assuntos
            if (tabName === 'questoes' && !<?php echo json_encode($edit_mode_questao); ?>) {
                const disciplinaSelect = document.getElementById('disciplina_questao');
                if (disciplinaSelect.value) {
                    loadAssuntosQuestoes();
                } else {
                    document.getElementById('assunto_questao').innerHTML = '<option value="">Selecione</option>';
                }
            }
        }

        // Função para carregar assuntos dinamicamente para o formulário de Questões
        function loadAssuntosQuestoes() {
            const disciplinaId = document.getElementById('disciplina_questao').value;
            const assuntoSelect = document.getElementById('assunto_questao');
            assuntoSelect.innerHTML = '<option value="">Selecione</option>'; // Limpa opções anteriores

            if (disciplinaId) {
                // O fetch fará a requisição para get_assuntos.php
                fetch('get_assuntos.php?disciplina=' + disciplinaId)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(assunto => {
                            const option = document.createElement('option');
                            option.value = assunto.id_assunto;
                            option.textContent = assunto.nome_assunto;
                            assuntoSelect.appendChild(option);
                        });
                        <?php if ($edit_mode_questao && isset($edit_question['id_assunto'])): ?>
                            assuntoSelect.value = <?php echo json_encode($edit_question['id_assunto']); ?>;
                        <?php endif; ?>
                    })
                    .catch(error => console.error('Erro ao carregar assuntos para questões:', error));
            }
        }

        // Ativar a aba correta ao carregar a página (que você já tem)
        document.addEventListener('DOMContentLoaded', (event) => {
            let activeTab = 'disciplinas';
            if (<?php echo json_encode(!empty($message_assunto) || $edit_mode_assunto); ?>) {
                activeTab = 'assuntos';
            } else if (<?php echo json_encode(!empty($message_questao) || $edit_mode_questao); ?>) {
                activeTab = 'questoes';
            } else if (<?php echo json_encode(!empty($message_disciplina) || $edit_mode_disciplina); ?>) {
                activeTab = 'disciplinas';
            }

            const initialTabButton = document.querySelector(`.tab-button[onclick*="'${activeTab}'"]`);
            if (initialTabButton) {
                initialTabButton.click();
            } else {
                document.querySelector('.tab-button').click();
            }

            <?php if ($edit_mode_questao && isset($edit_question['id_disciplina'])): ?>
                loadAssuntosQuestoes();
            <?php endif; ?>
        });
    </script>
</body>
</html>