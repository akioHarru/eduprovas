<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "bancodequestões";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    // É bom manter esta mensagem de erro para depuração da conexão
    // mas ela deve ser apenas para o desenvolvedor ver, não para a saída JSON.
    // Em produção, você registraria o erro em um log, não ecoaria.
    echo "Error: Unable to connect to MySQL.". mysqli_connect_error();
    exit; // Importante para parar a execução se a conexão falhar
}
else {
    $db = mysqli_select_db($conn, $database);
    if (!$db) {
        // Mesma observação: esta é uma mensagem de erro, não de sucesso
        echo "Error: Unable to connet to ".$database;
        exit; // Adicione um exit aqui também, se a seleção do banco falhar
    }
    // else {
    //     echo "Success"; // <--- REMOVA OU COMENTE ESTA LINHA!
    // }
}
?>