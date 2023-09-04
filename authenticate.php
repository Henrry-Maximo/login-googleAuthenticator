<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // Nome do arquivo de log (pode ser personalizado)

require 'vendor/autoload.php'; // Dependências 
include 'config/db.php'; // Configuração do banco de dados
include 'googleAuthenticator/PHPGangsta_GoogleAuthenticator.php'; // Importação do GoogleAuthenticator

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
$ga = new PHPGangsta_GoogleAuthenticator();

// Verificar se o usuário está autenticado na sessão. Se não, redirecioná-lo para a página de login.
if (!isset($_SESSION["authenticated_user"])) {
    header("Location: login.php");
    die("Permissão Negada!");
}

// Obter o ID do usuário e o nome do usuário da sessão de login.
$user_id = $_SESSION["authenticated_user"];
$user_name = $_SESSION["name_user"];

// Consulta SQL para obter o segredo e o tipo do usuário com base no ID.
$query = "SELECT secret, type FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Função para atualizar o tipo para zero no banco de dados quando o botão de 'verificar' for pressionado.
function updateTypeToZero($conn, $user_id)
{
    $typeUpdate = "UPDATE users SET type = '0' WHERE id = ?";
    $stmt = $conn->prepare($typeUpdate);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

// Verificar se a consulta foi bem-sucedida e se há resultados.
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_type = $row["type"];
    $user_secret = $row["secret"];


    // Mensagem de aviso (caso o type = 0) e gerar um URL do QR Code se o tipo de usuário for 1 (exibir QRCode).
    $aviso = "QR Code Indisponível!";
    if ($user_type == 1) {
        $nomeDaPessoa = $user_name;
        $aviso = "Escaneie o QR Code abaixo com o aplicativo Google Authenticator:";
        $qrCodeUrl = $ga->getQRCodeGoogleUrl("Plugin Logix Web: $nomeDaPessoa", $user_secret);
    }

    // Verificar se a solicitação HTTP é do tipo POST.
    if (
        $_SERVER["REQUEST_METHOD"] == "POST"
    ) {
        $code = $_POST["code"];
        // Verificar se o código inserido pelo usuário corresponde ao segredo do Google Authenticator.
        if ($ga->verifyCode($user_secret, $code)) {
            $_SESSION["google_authenticated"] = true;
            header("Location: ./pages/home.php");

            // Se o botão "updateButton" for pressionado, chame a função 'updateTypeToZero' para atualizar o tipo do usuário para zero.
            if (isset($_POST["updateButton"])) {
                updateTypeToZero($conn, $user_id);
            }
        } else {
            error_log("Código inválido. Código inserido: $code, Segredo: $user_secret");
            echo '<script>alert("Código inválido. Por favor, tente novamente.");</script>';
            header("Location: login.php");
            die();
        }
    } else if (empty($code)) {
        echo '<script>alert("Após escanear o QRCode, dê entrada com o código.");</script>';
    } else {
        $error_message = "Erro de Execução.";
        error_log($error_message);
    }
}

$stmt->close(); // Fechar a declaração preparada.
$conn->close(); // Fechar a conexão com o banco de dados.
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
            <p class=" qrcode-unavailable">
                <?php echo "$aviso"; ?>
                <img src="<?php
                if ($user_type == 1) {
                    echo "$qrCodeUrl";
                }
                ?>">
        </div>
    </div>
    <div class="container-form">
        <h2>Autenticação Plugin Web:</h2>
        <form method="post" action="">
            <h3>Olá
                <?= $user_name; ?>, seja bem-vindo(a)!
            </h3>
            <label for="code">Código de Autenticação:</label>
            <input type="text" id="code" name="code" required><br><br>
            <button type="submit" name="updateButton">Verificar</button>
        </form>

        <?php if (isset($error_message)) {
            echo "<p>$error_message</p>";
        } ?>
    </div>
</body>

</html>