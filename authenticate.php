<?php
session_start();

require 'vendor/autoload.php'; // Dependências 
include 'config/db.php'; // Configuração do banco de dados
include 'googleAuthenticator/PHPGangsta_GoogleAuthenticator.php'; // Importação do GoogleAuthenticator

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
$ga = new PHPGangsta_GoogleAuthenticator();

if (!isset($_SESSION["authenticated_user"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["authenticated_user"];
$user_name = $_SESSION["name_user"];

$query = "SELECT secret, type FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_type = $row["type"];
    $user_secret = $row["secret"];

    $aviso = "QR Code Indisponível!";
    if ($user_type == 1) {
        $nomeDaPessoa = $user_name;
        $aviso = "Escaneie o QR Code abaixo com o aplicativo Google Authenticator:";
        $qrCodeUrl = $ga->getQRCodeGoogleUrl("Plugin Logix Web: $nomeDaPessoa", $user_secret);

        $typeUpdate = "UPDATE users SET type = '0' WHERE id = $user_id";
        $conn->query($typeUpdate);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $code = $_POST["code"];

        if ($ga->verifyCode($user_secret, $code)) {
            $_SESSION["google_authenticated"] = true;
            header("Location: ./pages/home.php");
            exit();
        } else {
            echo '<script>alert("Código inválido. Por favor, tente novamente.");</script>';
            header("Location: login.php");
            die();
        }
    }
} else {
    $error_message = "Erro ao buscar o segredo do usuário.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>PLUGIN WEB - TWO FACTORS</title>
    <link href="./assets/loginLayout.css" rel="stylesheet">
</head>

<body>
    <div class="container-form-authenticate">
        <div class="qrcode-container">
            <p class=" qrcode-unavailable"><?php echo "$aviso"; ?>
                <img src="<?php
                            if ($user_type == 1) {
                                echo "$qrCodeUrl";
                            }
                            ?>">
            </p>
        </div>
    </div>
    <div class="container-form">
        <h2>Autenticação Plugin Web:</h2>
        <form method="post" action="">
            <h3>Olá <?= $user_name; ?>, seja bem-vindo(a)!</h3>
            <label for="code">Código de Autenticação:</label>
            <input type="text" id="code" name="code" required><br><br>
            <button type="submit">Verificar</button>
        </form>

        <?php if (isset($error_message)) {
            echo "<p>$error_message</p>";
        } ?>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector(".qrcode-container").classList.add("animate-slide");
    });
    </script>
</body>

</html>