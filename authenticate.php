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

<<<<<<< HEAD
function updateTypeToZero($conn, $user_id)
{
    $typeUpdate = "UPDATE users SET type = '0' WHERE id = ?";
    $stmt = $conn->prepare($typeUpdate);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
=======
>>>>>>> 49b805e47a591624e7f94d6df867af5590dc3caa

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_type = $row["type"];
    $user_secret = $row["secret"];

    $aviso = "QR Code Indisponível!";
    if ($user_type == 1) {
        $nomeDaPessoa = $user_name;
        $aviso = "Escaneie o QR Code abaixo com o aplicativo Google Authenticator:";
        $qrCodeUrl = $ga->getQRCodeGoogleUrl("Plugin Logix Web: $nomeDaPessoa", $user_secret);
<<<<<<< HEAD
=======

        $typeUpdate = "UPDATE users SET type = '0' WHERE id = $user_id";
        $conn->query($typeUpdate);
>>>>>>> 49b805e47a591624e7f94d6df867af5590dc3caa
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $code = $_POST["code"];
<<<<<<< HEAD
        if ($ga->verifyCode($user_secret, $code)) {
            $_SESSION["google_authenticated"] = true;
            header("Location: ./pages/home.php");
            if (isset($_POST["updateButton"])) {
                updateTypeToZero($conn, $user_id);
            }
=======

        if ($ga->verifyCode($user_secret, $code)) {
            $_SESSION["google_authenticated"] = true;
            header("Location: ./pages/home.php");
>>>>>>> 49b805e47a591624e7f94d6df867af5590dc3caa
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
        <div class="container-form-qr">
<<<<<<< HEAD
            <p class=" qrcode-unavailable">
                <?php echo "$aviso"; ?>
                <img src="<?php
                if ($user_type == 1) {
                    echo "$qrCodeUrl";
                }
                ?>">
=======
            <p class=" qrcode-unavailable"><?php echo "$aviso"; ?>
                <img src="<?php
                            if ($user_type == 1) {
                                echo "$qrCodeUrl";
                            }
                            ?>">
>>>>>>> 49b805e47a591624e7f94d6df867af5590dc3caa
            </p>
        </div>
    </div>
    <div class="container-form">
        <h2>Autenticação Plugin Web:</h2>
        <form method="post" action="">
<<<<<<< HEAD
            <h3>Olá
                <?= $user_name; ?>, seja bem-vindo(a)!
            </h3>
            <label for="code">Código de Autenticação:</label>
            <input type="text" id="code" name="code" required><br><br>
            <button type="submit" name="updateButton">Verificar</button>
=======
            <h3>Olá <?= $user_name; ?>, seja bem-vindo(a)!</h3>
            <label for="code">Código de Autenticação:</label>
            <input type="text" id="code" name="code" required><br><br>
            <button type="submit">Verificar</button>
>>>>>>> 49b805e47a591624e7f94d6df867af5590dc3caa
        </form>

        <?php if (isset($error_message)) {
            echo "<p>$error_message</p>";
        } ?>
    </div>

    <script>
<<<<<<< HEAD
        document.addEventListener("DOMContentLoaded", function () {
=======
        document.addEventListener("DOMContentLoaded", function() {
>>>>>>> 49b805e47a591624e7f94d6df867af5590dc3caa
            document.querySelector(".qrcode-container").classList.add("animate-slide");
        });
    </script>
</body>

</html>