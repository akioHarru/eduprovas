<?php
// get_assuntos.php - Endpoint AJAX para carregar assuntos

// --- CONFIGURAÇÃO DE DEPURAÇÃO (REMOVER EM PRODUÇÃO) ---
error_reporting(E_ALL); // Reporta todos os erros PHP
ini_set('display_errors', 1); // Exibe erros diretamente no navegador
ini_set('log_errors', 1); // Liga o log de erros em arquivo
// ATENÇÃO: Ajuste este caminho para o seu sistema (XAMPP, WAMP, MAMP, etc.)
ini_set('error_log', __DIR__ . '/php_error.log'); // Tenta criar um log na mesma pasta de get_assuntos.php
// -----------------------------------------------------

require_once 'conexao.php'; // Inclui o arquivo de conexão
require_once 'function.php'; // Inclui o arquivo de funções

// ATENÇÃO: Comente esta linha TEMPORARIAMENTE para ver os logs de depuração na aba "Resposta".
//          DESCOMENTE-A quando o problema for resolvido e você quiser a saída JSON limpa.
// header('Content-Type: application/json');

// --- Início da Saída de Depuração no HTML (para a aba "Resposta" do navegador) ---
echo "\n"; // <--- ESTA LINHA É O FECHAMENTO DO COMENTÁRIO HTML

// --- Saída Final JSON (O que o JavaScript espera) ---
echo json_encode($assuntos);

// REMOVA OU COMENTE ESTA LINHA
// mysqli_close($conn);
?>