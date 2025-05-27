<?php
require_once 'conexao.php'; // Inclui a conexão com o banco de dados
require_once 'function.php'; // Manter para verificarLogin() se necessário, embora não mais chame registrarUsuario()
session_start();

$message = '';
$result = ['success' => false, 'message' => '']; // Inicializa $result para evitar notices

// Se o usuário já estiver logado, pode ser redirecionado (opcional para testes)
// Para testes sem login, pode-se comentar este bloco.
/*
if (verificarLogin()) {
    header("Location: index.php");
    exit();
}
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirm_senha = $_POST['confirm_senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha) || empty($confirm_senha)) {
        $message = 'Por favor, preencha todos os campos.';
        $result['success'] = false; // Define como falha
    } elseif ($senha !== $confirm_senha) {
        $message = 'As senhas não coincidem.';
        $result['success'] = false;
    } else {
        // Lógica de registro diretamente aqui

        // 1. Verificar se o e-mail já existe
        $sql_check = "SELECT id_professor FROM professortb WHERE email_professor = ?";
        $stmt_check = $conn->prepare($sql_check);

        if (!$stmt_check) {
            error_log("registrar.php: Erro ao preparar verificação de e-mail: " . $conn->error);
            $message = 'Erro interno ao verificar e-mail (prepare error).';
            $result['success'] = false;
        } else {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $message = 'Este e-mail já está cadastrado.';
                $result['success'] = false;
            } else {
                // 2. Hash da senha
                $hashed_password = password_hash($senha, PASSWORD_DEFAULT);

                // 3. Inserir o novo usuário na professortb
                // A coluna id_admin não é incluída no INSERT aqui,
                // pois assumimos que foi alterada para permitir NULL no BD.
                $sql_insert = "INSERT INTO professortb (nome_professor, email_professor, senha_professor) VALUES (?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);

                if (!$stmt_insert) {
                    error_log("registrar.php: Erro ao preparar inserção: " . $conn->error);
                    $message = 'Erro interno ao registrar usuário (prepare error).';
                    $result['success'] = false;
                } else {
                    $stmt_insert->bind_param("sss", $nome, $email, $hashed_password);

                    if ($stmt_insert->execute()) {
                        $message = 'Cadastro realizado com sucesso! Faça login para continuar.';
                        $result['success'] = true;
                        // Redireciona para a página de login após o registro bem-sucedido
                        header("Location: login.php?registered=true");
                        exit();
                    } else {
                        // Captura o erro específico da chave estrangeira se ainda ocorrer
                        $error_msg = $stmt_insert->error;
                        if (strpos($error_msg, 'Cannot add or update a child row: a foreign key constraint fails') !== false) {
                            $message = 'Erro ao cadastrar usuário: Falha na restrição de chave estrangeira (id_admin). Certifique-se de que a coluna `id_admin` na tabela `professortb` permite valores NULL no banco de dados.';
                        } else {
                            $message = 'Erro ao cadastrar usuário: ' . $error_msg;
                        }
                        error_log("registrar.php: Erro ao executar inserção: " . $error_msg);
                        $result['success'] = false;
                    }
                    $stmt_insert->close();
                }
            }
            $stmt_check->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrar - EduProvas</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/login.css" />
</head>
<body>
  <div class="login-container">
    <h1>Registrar Nova Conta</h1>
    <?php if ($message): ?>
      <p class="message <?php echo ($result['success']) ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="POST" action="registrar.php">
      <input type="text" name="nome" placeholder="Nome Completo" required value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" />
      <input type="email" name="email" placeholder="E-mail" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
      <input type="password" name="senha" placeholder="Senha" required />
      <input type="password" name="confirm_senha" placeholder="Confirme a Senha" required />
      <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
    <p class="register-link">Já tem uma conta? <a href="login.php">Faça login aqui</a>.</p>
  </div>
</body>
</html>