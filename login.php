<?php
session_start();

include 'config/db.php';
include 'googleAuthenticator/PHPGangsta_GoogleAuthenticator.php';

$ga = new PHPGangsta_GoogleAuthenticator();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    $username = $conn->real_escape_string($_POST["username"]);
    $password = $conn->real_escape_string($_POST["password"]);

    $query = "SELECT id, name, secret, type FROM users WHERE name = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();

        $user_id = $user_data["id"];
        $user_secret = $user_data["secret"];
        $user_name = $user_data["name"];
        $user_type = $user_data["type"];

        $_SESSION["authenticated_user"] = $user_id;
        $_SESSION["name_user"] = $user_name;

        // só irá atualizar se $user_type for diferente de zero
        // permissão para o usuário novo visualizar o qrcode
        if ($user_type != 0 || $user_type == '') {
            $updateQuery = "UPDATE users SET type = '1' WHERE id = $user_id";
            $conn->query($updateQuery);
        }

        // só irá atualizar se $user_secret estiver vazio no bd
        // cadastro da secret
        if (empty($user_secret)) {
            $user_secret = $ga->createSecret();

            $updateQuery = "UPDATE users SET secret = '$user_secret' WHERE id = $user_id";
            $conn->query($updateQuery);
        }

        // Redirecionar para a página de autenticação com o QR Code
        header("Location: authenticate.php"); // Você pode passar informações aqui se necessário
        exit();
    } else {
        $error_message = "Usuário ou senha inválidos.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>PLUGIN WEB: LOGIX</title>
    <link href="./assets/loginLayout.css" rel="stylesheet">
</head>

<body>

    <form method="post" action="">
        <div class="container-form">
            <h2>LOGIN</h2>
            <div>
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required><br><br>
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required><br><br>

            </div>
            <div class="button-center">
                <button type="submit">Entrar</button>
            </div>
            <?php if (isset($error_message)) {
                echo "<p>$error_message</p>";
            } ?>
        </div>
    </form>

</body>

</html>