<?php
// Cargar variables de entorno desde el archivo .env
if (file_exists('.env')) {
    $dotenv = parse_ini_file('.env');
    foreach ($dotenv as $key => $value) {
        putenv("$key=$value");
    }
}

// Conectar a la base de datos
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexión a la base de datos establecida.\n";

    // Paso 1: Consultar todos los comentarios vacíos
    $sql = "SELECT * FROM comments WHERE name IS NULL AND email IS NULL AND comment IS NULL";
    $stmt = $pdo->query($sql);
    $emptyComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($emptyComments)) {
        echo "No hay comentarios vacíos para actualizar.\n";
        exit;
    }

    echo "Se encontraron " . count($emptyComments) . " comentarios vacíos.\n";

    // Paso 2: Obtener los comentarios desde la API externa
    $apiUrl = "https://jsonplaceholder.typicode.com/comments";
    $apiResponse = file_get_contents($apiUrl);

    if ($apiResponse === FALSE) {
        throw new Exception("Error al obtener los datos de la API.");
    }

    $commentsFromApi = json_decode($apiResponse, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar los datos de la API: " . json_last_error_msg());
    }

    echo "Datos de la API obtenidos correctamente.\n";

    // Paso 3: Actualizar la base de datos con los comentarios de la API
    foreach ($emptyComments as $index => $comment) {
        if (!isset($commentsFromApi[$index])) {
            echo "No hay más comentarios en la API para este índice.\n";
            break;
        }

        $apiComment = $commentsFromApi[$index];
        $sqlUpdate = "UPDATE comments SET name = :name, email = :email, comment = :comment WHERE id = :id";
        $stmtUpdate = $pdo->prepare($sqlUpdate);

      
        $stmtUpdate->bindParam(':name', $apiComment['name']);
        $stmtUpdate->bindParam(':email', $apiComment['email']);
        $stmtUpdate->bindParam(':comment', $apiComment['body']);
        $stmtUpdate->bindParam(':id', $comment['id'], PDO::PARAM_INT);

        if ($stmtUpdate->execute()) {
            echo "Comentario con ID " . $comment['id'] . " actualizado.\n";
        } else {
            echo "Error al actualizar el comentario con ID " . $comment['id'] . ".\n";
        }
    }
    echo "Proceso completado.\n";
} catch (PDOException $e) {   
    echo "Error de conexión a la base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
