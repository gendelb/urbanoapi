<?php
// Conexão com o banco de dados (MySQL)
$host = 'localhost';
$dbname = 'encurtador';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Função para gerar código curto
function gerarCodigo($length = 6) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $codigo = '';
    for ($i = 0; $i < $length; $i++) {
        $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    return $codigo;
}

// Encurtar URL
if (isset($_POST['url'])) {
    $url = $_POST['url'];
    
    // Validar URL
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        die('URL inválida');
    }
    
    // Verificar se a URL já existe
    $stmt = $pdo->prepare("SELECT codigo FROM links WHERE url = ?");
    $stmt->execute([$url]);
    $resultado = $stmt->fetch();
    
    if ($resultado) {
        $codigo = $resultado['codigo'];
    } else {
        // Gerar código único
        do {
            $codigo = gerarCodigo();
            $stmt = $pdo->prepare("SELECT id FROM links WHERE codigo = ?");
            $stmt->execute([$codigo]);
        } while ($stmt->fetch());
        
        // Inserir no banco de dados
        $stmt = $pdo->prepare("INSERT INTO links (url, codigo, acessos) VALUES (?, ?, 0)");
        $stmt->execute([$url, $codigo]);
    }
    
    $linkCurto = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . $codigo;
    echo "Seu link encurtado: <a href='$linkCurto'>$linkCurto</a>";
}

// Redirecionamento
if (isset($_GET['url'])) {
    $codigo = $_GET['url'];
    
    $stmt = $pdo->prepare("SELECT url FROM links WHERE codigo = ?");
    $stmt->execute([$codigo]);
    $resultado = $stmt->fetch();
    
    if ($resultado) {
        // Atualizar contador de acessos
        $stmt = $pdo->prepare("UPDATE links SET acessos = acessos + 1 WHERE codigo = ?");
        $stmt->execute([$codigo]);
        
        // Redirecionar
        header("Location: " . $resultado['url']);
        exit();
    } else {
        die('Link não encontrado');
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Encurtador de Links</title>
</head>
<body>
    <h1>Encurtador de Links</h1>
    <form method="post">
        <input type="text" name="url" placeholder="Cole sua URL aqui" required>
        <button type="submit">Encurtar</button>
    </form>
</body>
</html>
