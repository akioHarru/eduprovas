<?php
/**
 * Funções do sistema EduProvas
 */

// ... (todas as funções de Usuário - admintb, professortb - permanecem as mesmas)

// --- Funções de Questões ---

function cadastrarQuestao($conn, $data) {
    // Validação de dados
    if (!isset($data['enunciado'], $data['alternativas'], $data['resposta'], $data['disciplina'], $data['assunto']) ||
        !is_array($data['alternativas']) || count($data['alternativas']) < 4) {
        error_log("cadastrarQuestao: Dados incompletos - " . json_encode($data));
        return ['success' => false, 'message' => 'Dados incompletos. Preencha todos os campos.'];
    }

    // Sanitize the input
    $enunciado = htmlspecialchars($data['enunciado'], ENT_QUOTES, 'UTF-8');
    $alternativa_a = htmlspecialchars($data['alternativas'][0], ENT_QUOTES, 'UTF-8');
    $alternativa_b = htmlspecialchars($data['alternativas'][1], ENT_QUOTES, 'UTF-8');
    $alternativa_c = htmlspecialchars($data['alternativas'][2], ENT_QUOTES, 'UTF-8');
    $alternativa_d = htmlspecialchars($data['alternativas'][3], ENT_QUOTES, 'UTF-8');
    $resposta = htmlspecialchars($data['resposta'], ENT_QUOTES, 'UTF-8');
    $id_disciplina = filter_var($data['disciplina'], FILTER_VALIDATE_INT);
    $id_assunto = filter_var($data['assunto'], FILTER_VALIDATE_INT);

    if ($id_disciplina === false || $id_assunto === false) {
        return ['success' => false, 'message' => 'IDs de Disciplina ou Assunto inválidos.'];
    }

    $tipo = 'Múltipla Escolha';

    // AQUI O AJUSTE PARA 'questãotb'
    $sql = "INSERT INTO questãotb (id_disciplina, id_assunto, enunciado, alternativa_a, alternativa_b, alternativa_c, alternativa_d, resposta, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("cadastrarQuestao: Erro ao preparar: " . $conn->error);
        return ['success' => false, 'message' => 'Erro ao preparar a query: ' . $conn->error];
    }
    $stmt->bind_param("iisssssss", $id_disciplina, $id_assunto, $enunciado, $alternativa_a, $alternativa_b, $alternativa_c, $alternativa_d, $resposta, $tipo);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Questão cadastrada com sucesso.'];
    } else {
        error_log("cadastrarQuestao: Erro ao executar: " . $stmt->error);
        $stmt->close();
        return ['success' => false, 'message' => 'Erro ao cadastrar questão: ' . $stmt->error];
    }
}

function editarQuestao($conn, $data) {
    // Validação de dados
    if (!isset($data['id_questao'], $data['enunciado'], $data['alternativas'], $data['resposta'], $data['disciplina'], $data['assunto']) ||
        !is_array($data['alternativas']) || count($data['alternativas']) < 4) {
        error_log("editarQuestao: Dados incompletos - " . json_encode($data));
        return ['success' => false, 'message' => 'Dados incompletos para edição. Preencha todos os campos.'];
    }

    // Sanitize the input
    $id_questao = filter_var($data['id_questao'], FILTER_VALIDATE_INT);
    $enunciado = htmlspecialchars($data['enunciado'], ENT_QUOTES, 'UTF-8');
    $alternativa_a = htmlspecialchars($data['alternativas'][0], ENT_QUOTES, 'UTF-8');
    $alternativa_b = htmlspecialchars($data['alternativas'][1], ENT_QUOTES, 'UTF-8');
    $alternativa_c = htmlspecialchars($data['alternativas'][2], ENT_QUOTES, 'UTF-8');
    $alternativa_d = htmlspecialchars($data['alternativas'][3], ENT_QUOTES, 'UTF-8');
    $resposta = htmlspecialchars($data['resposta'], ENT_QUOTES, 'UTF-8');
    $id_disciplina = filter_var($data['disciplina'], FILTER_VALIDATE_INT);
    $id_assunto = filter_var($data['assunto'], FILTER_VALIDATE_INT);

    if ($id_questao === false || $id_disciplina === false || $id_assunto === false) {
        return ['success' => false, 'message' => 'IDs de Questão, Disciplina ou Assunto inválidos.'];
    }

    // AQUI O AJUSTE PARA 'questãotb'
    $sql = "UPDATE questãotb SET id_disciplina = ?, id_assunto = ?, enunciado = ?, alternativa_a = ?, alternativa_b = ?, alternativa_c = ?, alternativa_d = ?, resposta = ? WHERE id_questao = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("editarQuestao: Erro ao preparar: " . $conn->error);
        return ['success' => false, 'message' => 'Erro ao preparar a query de edição: ' . $conn->error];
    }
    $stmt->bind_param("iissssssi", $id_disciplina, $id_assunto, $enunciado, $alternativa_a, $alternativa_b, $alternativa_c, $alternativa_d, $resposta, $id_questao);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Questão atualizada com sucesso.'];
    } else {
        error_log("editarQuestao: Erro ao executar: " . $stmt->error);
        $stmt->close();
        return ['success' => false, 'message' => 'Erro ao atualizar questão: ' . $stmt->error];
    }
}

function excluirQuestao($conn, $id) {
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if ($id === false) {
        return ['success' => false, 'message' => 'ID de questão inválido.'];
    }

    // AQUI O AJUSTE PARA 'questãotb'
    $sql = "DELETE FROM questãotb WHERE id_questao = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("excluirQuestao: Erro ao preparar: " . $conn->error);
        return ['success' => false, 'message' => 'Erro ao preparar a query de exclusão: ' . $conn->error];
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Questão excluída com sucesso.'];
    } else {
        error_log("excluirQuestao: Erro ao executar: " . $stmt->error);
        $stmt->close();
        return ['success' => false, 'message' => 'Erro ao excluir questão: ' . $stmt->error];
    }
}


function mostrarQuestoes($conn, $filters = []) {
    // AQUI OS AJUSTES PARA 'questãotb', 'disciplinatb', 'assuntotb'
    $sql = "SELECT q.*, d.nome_disciplina, a.nome_assunto
            FROM questãotb q
            JOIN disciplinatb d ON q.id_disciplina = d.id_disciplina
            JOIN assuntotb a ON q.id_assunto = a.id_assunto";
    $params = [];
    $types = "";
    $where_clauses = [];

    if (isset($filters['disciplina_id']) && $filters['disciplina_id'] !== '') {
        $where_clauses[] = "q.id_disciplina = ?";
        $params[] = $filters['disciplina_id'];
        $types .= "i";
    }
    if (isset($filters['assunto_id']) && $filters['assunto_id'] !== '') {
        $where_clauses[] = "q.id_assunto = ?";
        $params[] = $filters['assunto_id'];
        $types .= "i";
    }
    if (isset($filters['search_term']) && $filters['search_term'] !== '') {
        $where_clauses[] = "(q.enunciado LIKE ? OR q.alternativa_a LIKE ? OR q.alternativa_b LIKE ? OR q.alternativa_c LIKE ? OR q.alternativa_d LIKE ?)";
        $search_term = "%" . $filters['search_term'] . "%";
        $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term, $search_term]);
        $types .= "sssss";
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("mostrarQuestoes: Erro ao preparar: " . $conn->error);
        return [];
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        error_log("mostrarQuestoes: Erro ao executar: " . $stmt->error);
        $stmt->close();
        return [];
    }
    $result = $stmt->get_result();
    $questions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $questions;
}

function gerarProva($conn, $selected_ids) {
    if (empty($selected_ids)) {
        return [];
    }

    // Garante que todos os IDs são inteiros
    $clean_ids = array_map('intval', $selected_ids);
    $placeholders = implode(',', array_fill(0, count($clean_ids), '?'));
    $types = str_repeat('i', count($clean_ids));

    // AQUI OS AJUSTES PARA 'questãotb', 'disciplinatb', 'assuntotb'
    $sql = "SELECT q.*, d.nome_disciplina, a.nome_assunto
            FROM questãotb q
            JOIN disciplinatb d ON q.id_disciplina = d.id_disciplina
            JOIN assuntotb a ON q.id_assunto = a.id_assunto
            WHERE q.id_questao IN ($placeholders)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("gerarProva: Erro ao preparar: " . $conn->error);
        return [];
    }

    $stmt->bind_param($types, ...$clean_ids);

    if (!$stmt->execute()) {
        error_log("gerarProva: Erro ao executar: " . $stmt->error);
        $stmt->close();
        return [];
    }
    $result = $stmt->get_result();
    $questions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $questions;
}


// --- Funções de Disciplinas ---

function cadastrarDisciplina($conn, $nome_disciplina) {
    if (!is_string($nome_disciplina) || empty(trim($nome_disciplina))) {
        return ['success' => false, 'message' => 'O nome da disciplina não pode ser vazio.'];
    }
    
    $nome_disciplina = htmlspecialchars(trim($nome_disciplina), ENT_QUOTES, 'UTF-8');

    // AQUI O AJUSTE PARA 'disciplinatb'
    $sql = "INSERT INTO disciplinatb (nome_disciplina) VALUES (?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("cadastrarDisciplina: Erro ao preparar: " . $conn->error);
        return ['success' => false, 'message' => 'Erro ao preparar a query de cadastro de disciplina: ' . $conn->error];
    }
    $stmt->bind_param("s", $nome_disciplina);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Disciplina cadastrada com sucesso.'];
    } else {
        error_log("cadastrarDisciplina: Erro ao executar: " . $stmt->error);
        $stmt->close();
        return ['success' => false, 'message' => 'Erro ao cadastrar disciplina: ' . $stmt->error];
    }
}

function editarDisciplina($conn, $id_disciplina, $nome_disciplina) {
    $id_disciplina = filter_var($id_disciplina, FILTER_VALIDATE_INT);
    
    if (!is_string($nome_disciplina) || empty(trim($nome_disciplina)) || $id_disciplina === false) {
        return ['success' => false, 'message' => 'Dados inválidos para edição da disciplina.'];
    }

    $nome_disciplina = htmlspecialchars(trim($nome_disciplina), ENT_QUOTES, 'UTF-8');

    // AQUI O AJUSTE PARA 'disciplinatb'
    $sql = "UPDATE disciplinatb SET nome_disciplina = ? WHERE id_disciplina = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("editarDisciplina: Erro ao preparar: " . $conn->error);
        return ['success' => false, 'message' => 'Erro ao preparar a query de edição de disciplina: ' . $conn->error];
    }
    $stmt->bind_param("si", $nome_disciplina, $id_disciplina);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Disciplina atualizada com sucesso.'];
    } else {
        error_log("editarDisciplina: Erro ao executar: " . $stmt->error);
        $stmt->close();
        return ['success' => false, 'message' => 'Erro ao atualizar disciplina: ' . $stmt->error];
    }
}

function excluirDisciplina($conn, $id_disciplina) {
    $id_disciplina = filter_var($id_disciplina, FILTER_VALIDATE_INT);
    if ($id_disciplina === false) {
        return ['success' => false, 'message' => 'ID de disciplina inválido.'];
    }

    // AQUI O AJUSTE PARA 'disciplinatb'
    $sql = "DELETE FROM disciplinatb WHERE id_disciplina = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("excluirDisciplina: Erro ao preparar: " . $conn->error);
        return ['success' => false, 'message' => 'Erro ao preparar a query de exclusão de disciplina: ' . $conn->error];
    }
    $stmt->bind_param("i", $id_disciplina);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Disciplina excluída com sucesso.'];
    } else {
        error_log("excluirDisciplina: Erro ao executar: " . $stmt->error);
        $stmt->close();
        return ['success' => false, 'message' => 'Erro ao excluir disciplina: ' . $stmt->error];
    }
}

function listarDisciplinas($conn) {
    // AQUI O AJUSTE PARA 'disciplinatb'
    $sql = "SELECT * FROM disciplinatb ORDER BY nome_disciplina ASC";
    $result = $conn->query($sql);
    if (!$result) {
        error_log("listarDisciplinas: Erro ao executar: " . $conn->error);
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}


// --- Funções de Assuntos ---

function cadastrarAssunto($conn, $data) {
    // Validação para garantir que $data é um array e contém as chaves necessárias
    if (!is_array($data) || !isset($data['nome_assunto']) || !isset($data['id_disciplina'])) {
        return ['success' => false, 'message' => 'Dados incompletos ou inválidos para cadastro de assunto.'];
    }

    $nome_assunto = htmlspecialchars(trim($data['nome_assunto']), ENT_QUOTES, 'UTF-8');
    $id_disciplina = filter_var($data['id_disciplina'], FILTER_VALIDATE_INT);

    if (empty($nome_assunto) || $id_disciplina === false) {
        return ['success' => false, 'message' => 'Dados inválidos para cadastro de assunto.'];
    }

    // AQUI O AJUSTE PARA 'assuntotb'
    $sql = "INSERT INTO assuntotb (nome_assunto, id_disciplina) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("cadastrarAssunto: Erro ao preparar: " . $conn->error);
        return ['success' => false, 'message' => 'Erro ao preparar a query de cadastro de assunto: ' . $conn->error];
    }
    $stmt->bind_param("si", $nome_assunto, $id_disciplina);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Assunto cadastrado com sucesso.'];
    } else {
        error_log("cadastrarAssunto: Erro ao executar: " . $stmt->error);
        $stmt->close();
        return ['success' => false, 'message' => 'Erro ao cadastrar assunto: ' . $stmt->error];
    }
}

function editarAssunto($conn, $data) {
    // Validação para garantir que $data é um array e contém as chaves necessárias
    if (!is_array($data) || !isset($data['id_assunto']) || !isset($data['nome_assunto']) || !isset($data['id_disciplina'])) {
        return ['success' => false, 'message' => 'Dados incompletos ou inválidos para edição de assunto.'];
    }

    $id_assunto = filter_var($data['id_assunto'], FILTER_VALIDATE_INT);
    $nome_assunto = htmlspecialchars(trim($data['nome_assunto']), ENT_QUOTES, 'UTF-8');
    $id_disciplina = filter_var($data['id_disciplina'], FILTER_VALIDATE_INT);

    if ($id_assunto === false || empty($nome_assunto) || $id_disciplina === false) {
        return ['success' => false, 'message' => 'Dados inválidos para edição de assunto.'];
    }

    // AQUI O AJUSTE PARA 'assuntotb'
    $sql = "UPDATE assuntotb SET nome_assunto = ?, id_disciplina = ? WHERE id_assunto = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("editarAssunto: Erro ao preparar: " . $conn->error);
        return ['success' => false, 'message' => 'Erro ao preparar a query de edição de assunto: ' . $conn->error];
    }
    $stmt->bind_param("sii", $nome_assunto, $id_disciplina, $id_assunto);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Assunto atualizado com sucesso.'];
    } else {
        error_log("editarAssunto: Erro ao executar: " . $stmt->error);
        $stmt->close();
        return ['success' => false, 'message' => 'Erro ao atualizar assunto: ' . $stmt->error];
    }
}

function excluirAssunto($conn, $id) {
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if ($id === false) {
        return ['success' => false, 'message' => 'ID de assunto inválido.'];
    }

    // AQUI O AJUSTE PARA 'assuntotb'
    $sql = "DELETE FROM assuntotb WHERE id_assunto = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("excluirAssunto: Erro ao preparar: " . $conn->error);
        return ['success' => false, 'message' => 'Erro ao preparar a query de exclusão de assunto: ' . $conn->error];
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => 'Assunto excluído com sucesso.'];
    } else {
        error_log("excluirAssunto: Erro ao executar: " . $stmt->error);
        $stmt->close();
        return ['success' => false, 'message' => 'Erro ao excluir assunto: ' . $stmt->error];
    }
}

function listarAssuntos($conn, $filters = []) {
    // LOG DE DEPURAÇÃO: Início da função e filtros recebidos
    error_log("listarAssuntos: FUNÇÃO INICIADA. Filtros recebidos: " . json_encode($filters));

    $sql = "SELECT a.id_assunto, a.nome_assunto, a.id_disciplina, d.nome_disciplina 
            FROM assuntotb a 
            JOIN disciplinatb d ON a.id_disciplina = d.id_disciplina";
    $params = [];
    $types = "";
    $where_clauses = [];

    // Lógica para filtrar por id_disciplina
    if (isset($filters['id_disciplina']) && $filters['id_disciplina'] !== '' && $filters['id_disciplina'] !== null) {
        $where_clauses[] = "a.id_disciplina = ?";
        // Validação adicional para garantir que é um inteiro, mesmo que já venha filtrado de get_assuntos.php
        $disciplina_id_filtered = filter_var($filters['id_disciplina'], FILTER_VALIDATE_INT);
        if ($disciplina_id_filtered !== false) {
            $params[] = $disciplina_id_filtered;
            $types .= "i";
            // LOG DE DEPURAÇÃO: ID da disciplina filtrado
            error_log("listarAssuntos: ID da disciplina validado e adicionado aos parâmetros: " . $disciplina_id_filtered);
        } else {
            error_log("listarAssuntos: ID da disciplina inválido após filter_var: " . var_export($filters['id_disciplina'], true));
        }
    } else {
        error_log("listarAssuntos: Filtro 'id_disciplina' não fornecido ou vazio.");
    }

    // Adiciona a cláusula WHERE se houver filtros
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    $sql .= " ORDER BY a.nome_assunto ASC";

    // LOG DE DEPURAÇÃO: SQL final montada e parâmetros para bind
    error_log("listarAssuntos: SQL FINAL: " . $sql);
    error_log("listarAssuntos: PARÂMETROS PARA BIND: " . json_encode($params));
    error_log("listarAssuntos: TIPOS PARA BIND: " . $types);

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // LOG DE ERRO: Problema ao preparar a declaração SQL
        error_log("listarAssuntos: ERRO FATAL AO PREPARAR STMT: " . $conn->error);
        return [];
    }

    // Vincula os parâmetros se existirem
    if (!empty($params)) {
        // LOG DE DEPURAÇÃO: Tentando bind_param
        error_log("listarAssuntos: Tentando bind_param com " . count($params) . " parâmetros.");
        // O operador spread (...) desempacota o array $params como argumentos individuais
        if (!$stmt->bind_param($types, ...$params)) {
             // LOG DE ERRO: Problema ao vincular parâmetros
             error_log("listarAssuntos: ERRO FATAL AO BIND_PARAM: " . $stmt->error);
             $stmt->close();
             return [];
        }
    } else {
        // LOG DE DEPURAÇÃO: Nenhum parâmetro para bind
        error_log("listarAssuntos: Nenhum parâmetro para bind (WHERE clause não aplicada).");
    }

    // Executa a declaração
    if (!$stmt->execute()) {
        // LOG DE ERRO: Problema ao executar a declaração SQL
        error_log("listarAssuntos: ERRO FATAL AO EXECUTAR STMT: " . $stmt->error);
        $stmt->close();
        return [];
    }

    $result = $stmt->get_result(); // Obtém o conjunto de resultados

    if (!$result) {
        // LOG DE ERRO: Problema ao obter resultados
        error_log("listarAssuntos: ERRO FATAL AO OBTER RESULTADO (get_result): " . $stmt->error);
        $stmt->close();
        return [];
    }

    $assuntos = $result->fetch_all(MYSQLI_ASSOC); // Busca todos os resultados como array associativo
    $stmt->close(); // Fecha a declaração

    // LOG DE DEPURAÇÃO: Resultados finais obtidos do BD
    error_log("listarAssuntos: RESULTADOS FINAIS OBTIDOS DO BD: " . json_encode($assuntos));
    return $assuntos;
}

?>