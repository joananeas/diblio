<?php
# © Joan Aneas

/*___ ___ ___ _    ___ ___  
    |   \_ _| _ ) |  |_ _/ _ \ 
    | |) | || _ \ |__ | | (_) |
    |___/___|___/____|___\___/  (ASCII art)*/


#TODO: Añadir autoloader
#TODO: Crear clase para las sesiones
class API_Globales
{
    private $version;
    private $rootPath;

    public function __construct($version, $rootPath)
    {
        $this->version = $version;
        $this->rootPath = $rootPath;
    }

    public function obtenerDatos()
    {
        $sql = "SELECT `NOM_BIBLIOTECA` AS `nomBiblioteca`, `TITOL_WEB` AS `titolWeb`, `H1_WEB` AS `h1Web`, `FAVICON` AS `favicon` FROM `dib_config`";
        $mysqli = peticionSQL();
        $result = mysqli_query($mysqli, $sql);
        if ($result === FALSE) {
            echo json_encode(['response' => 'ERROR', 'msg' => mysqli_error($mysqli)]);
            return;
        }

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $datos = array(
                "version" => $this->version,
                "nomBiblioteca" => $row['nomBiblioteca'],
                "titolWeb" => $row['titolWeb'],
                "favicon" => $row['favicon'],
                "h1Web" => $row['h1Web'],
                "rootPath" => $this->rootPath
            );
            return json_encode($datos);
        } else {
            echo json_encode(['response' => 'NO DATA']);
        }
    }

    public function getColores()
    {
        $sql = "SELECT `COLOR_PRINCIPAL` AS 'colorPrincipal', `COLOR_SECUNDARIO` AS 'colorSecundario', `COLOR_TERCIARIO` AS 'colorTerciario' FROM `dib_config`";
        $result = mysqli_query(peticionSQL(), $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
        } else {
            echo json_encode(['response' => 'ERROR']);
        }

        return json_encode($row);
    }
    public function setColores($colorPrincipal, $colorSecundario, $colorTerciario)
    {
        $conexion = peticionSQL();
        $stmt = $conexion->prepare("UPDATE `dib_config` SET `COLOR_PRINCIPAL` = ?, `COLOR_SECUNDARIO` = ?, `COLOR_TERCIARIO` = ?");

        if ($stmt === false) {
            die("Error preparando la consulta: " . $conexion->error);
        }

        $stmt->bind_param("sss", $colorPrincipal, $colorSecundario, $colorTerciario);

        if (!$stmt->execute()) {
            return json_encode([
                "response" => "error",
                "message" => "colors-not-changed"
            ]);
        }

        $stmt->close();
        return json_encode([
            "response" => "ok",
            "message" => "colors-changed"
        ]);
    }

    public function getFavicon(){
        $conn = peticionSQL();
        $sql = "SELECT `FAVICON` FROM `dib_config`";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(['response' => 'OK', 'favicon' => $row['FAVICON']]);
        } else {
            echo json_encode(['response' => 'ERROR', 'message' => 'No favicon found']);
        }
        $conn->close();
    }
    
}

class API_Banner
{
    public function getBanner()
    {
        $sql = "SELECT `BANNER_STATE` AS 'bannerState', `BANNER_TEXT` AS 'bannerText' FROM `dib_config`";
        $result = mysqli_query(peticionSQL(), $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
        } else {
            echo json_encode(['response' => 'ERROR']);
        }

        # Prevent js error if no banner is set
        if ($row == null) {
            $row['bannerText'] = '0';
            $row['bannerState'] = '';
        }

        return json_encode($row);
    }

    public function setBanner($bannerState, $bannerText)
    {
        $conexion = peticionSQL();
        $stmt = $conexion->prepare("UPDATE `dib_config` SET `BANNER_STATE` = ?, `BANNER_TEXT` = ?");

        if ($stmt === false) {
            die("Error preparando la consulta: " . $conexion->error);
        }

        $stmt->bind_param("ss", $bannerState, $bannerText);

        if (!$stmt->execute()) {
            die("Error al ejecutar la consulta: " . $stmt->error);
        }

        $stmt->close();
        return json_encode([
            "response" => "ok",
            // "banner-state" => $bannerState,
            // "banner-text" => $bannerText,
            "message" => "banner-changed"
        ]);
    }
}

class API_Carroussel
{
    private $foto;
    private $ancho;
    private $alto;
    private $url;

    public function __construct($url_fotos)
    {
        $this->url = $url_fotos;
    }

    public function obtenerDatos()
    {
        $datos = array(
            "foto" => $this->foto,
            "ancho" => $this->ancho,
            "alto" => $this->alto
        );
        return json_encode($datos);
    }

    public function obtenerFotos()
    {
        $i = 0;
        if (!dir($this->url)) return json_encode(["api" => "url doesn't exist."]);
        while (true) {
            $i++;
            if (!file_exists($this->url . 'prueba-' . $i . '.jpg')) {
                $i--; # Compensa la vuelta extra
                break;
            }
            #echo "Fotos: ". $i;
        }
        return json_encode(["num_libros" => $i]);
    }
}

class API_Usuarios
{
    private $email;
    private $password;
    private $rol; # Admin, Bibliotecario, Moderador y Usuario

    public function __construct($email, $password, $rol)
    {
        $this->email = $email;
        $this->password = $password;
        $this->rol = $rol;
    }

    public function obtenerDatos()
    {
        $datos = array(
            "email" => $this->email,
            "password" => $this->password,
            "rol" => $this->rol,
        );
        return json_encode($datos);
    }

    public function autenticarUsuario($email, $password)
    {
        $conn = peticionSQL();
        $sql = "SELECT * FROM dib_usuaris WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if (!$row) {
            return json_encode([
                "api" => null,
                "response" => "error"
            ]);
        }

        if (!password_verify($password, $row['passwd'])) {
            return json_encode([
                "api" => null,
                "response" => "error",
                # Debugging
                // "password-db" => $row['passwd'],
                // "password-usr" => $password,
                // "message" => "contrasena-incorrecta",
                // "message2" => password_verify($password, $row['passwd']),
            ]);
        }

        session_start();
        $_SESSION['email'] = $row['email'];
        $_SESSION['u_id'] = $row['usuari'];

        $cookie = isset($row['rol']) && $row['rol'] != "" ? $row['rol'] : 'lector';

        setcookie('rol', $cookie, time() + (86400 * 30), "/", "", true, true);

        return json_encode([
            "api" => $row,
            "response" => "ok"
        ]);
    }

    public function getRol()
    {
        session_start();
        // if (!isset($_COOKIE['rol'])) {
        //     return json_encode([
        //         "api" => null,
        //         "response" => "error",
        //         "message" => "Usuario no autenticado"
        //     ]);
        // }
        return json_encode([
            "rol" => $_COOKIE['rol'],
            "username" => $_SESSION['email'],
            "response" => "ok",
            "message" => "Usuario autenticado"
        ]);
    }

    public function getUserCreationDate()
    {
        session_start();
        if (!isset($_SESSION['email'])) {
            return json_encode([
                "api" => null,
                "response" => "error",
                "message" => "Usuario no autenticado"
            ]);
        }

        $sql = "SELECT YEAR(`data_registre`) AS year FROM `dib_usuaris` WHERE `email` = ?";
        $conn = peticionSQL();
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return json_encode([
                "api" => null,
                "response" => "error",
                "message" => "Error en la preparación de la consulta: " . mysqli_error($conn)
            ]);
        }

        mysqli_stmt_bind_param($stmt, 's', $_SESSION['email']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if ($result === false) {
            return json_encode([
                "api" => null,
                "response" => "error",
                "message" => "Error en la ejecución de la consulta: " . mysqli_stmt_error($stmt)
            ]);
        }

        $row = mysqli_fetch_assoc($result);

        if (!$row) {
            return json_encode([
                "api" => null,
                "response" => "error",
                "message" => "No se encontró el usuario"
            ]);
        }

        return json_encode([
            "api" => $row['year'],
            "response" => "ok",
            "message" => "Fecha de creación obtenida exitosamente"
        ]);
    }

    public function getID()
    {
        session_start();
        if (!isset($_SESSION['email'])) {
            return json_encode([
                "api" => null,
                "response" => "error",
                "message" => "Usuario no autenticado"
            ]);
        }

        $conn = peticionSQL();
        $sql = "SELECT usuari FROM dib_usuaris WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $_SESSION['email']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['usuari'];
    }

    public function headerAuthUsuario()
    {
        session_start();
        if (!isset($_SESSION['email'])) {
            return json_encode([
                "api" => null,
                "response" => "error",
                "message" => "Usuario no autenticado"
            ]);
        }
        return json_encode([
            "api" => $_SESSION['email'],
            "response" => "ok",
            "message" => "Usuario autenticado"
        ]);
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM dib_usuaris";
        $result = mysqli_query(peticionSQL(), $sql);
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($users, $row);
        }
        return json_encode($users);
    }

    public function createUser($email, $passwd, $rol)
    {
        $conn = peticionSQL();
        $email = mysqli_real_escape_string($conn, $email);
        $passwd = mysqli_real_escape_string($conn, $passwd);
        $rol = mysqli_real_escape_string($conn, $rol);

        $sql = "INSERT INTO dib_usuaris (email, passwd, rol) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            die("Error: " . mysqli_error($conn));
        }

        $hashedPasswd = password_hash($passwd, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, 'sss', $email, $hashedPasswd, $rol);

        $result = mysqli_stmt_execute($stmt);
        if (!$result) {
            die("Error: " . mysqli_error($conn));
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        return $result;
    }

    public function getNotifications($user_id)
    {
        $conn = peticionSQL();
        if ($conn->connect_errno) {
            return json_encode(["status" => "error", "message" => "Fallo al conectar a MySQL: " . $conn->connect_error]);
        }
        $conn->set_charset("utf8mb4");

        $stmt = $conn->prepare("SELECT * FROM dib_notificacions WHERE usuari_id = ? AND `estat`= 'pendent' ORDER BY `id_notificacio` DESC");
        if (!$stmt) {
            return json_encode(["status" => "error", "message" => "Error en la preparación de la consulta: " . $conn->error]);
        }

        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            if (count($data) > 0) {
                return json_encode(["status" => "ok", "data" => $data]);
            } else {
                return json_encode(["status" => "ok", "data" => []]); # Así muestra un 0
            }
        } else {
            return json_encode(["status" => "error", "message" => "Error en la ejecución de la consulta: " . $stmt->error]);
        }
    }

    public function clearNotifications($user_id)
    {
        $conn = peticionSQL();
        if ($conn->connect_errno) {
            return json_encode(["status" => "error", "message" => "Fallo al conectar a MySQL: " . $conn->connect_error]);
        }
        $conn->set_charset("utf8mb4");

        $stmt = $conn->prepare("UPDATE dib_notificacions SET `estat` = 'llegida' WHERE usuari_id = ?");
        if (!$stmt) {
            return json_encode(["status" => "error", "message" => "Error en la preparación de la consulta: " . $conn->error]);
        }

        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            return json_encode(["status" => "ok", "message" => "Notificaciones marcadas como vistas"]);
        } else {
            return json_encode(["status" => "error", "message" => "Error en la ejecución de la consulta: " . $stmt->error]);
        }
    }


    public function getReservesFromUser($userId)
    {
        $conn = peticionSQL();
        $reservas = [];

        $sql = "SELECT DISTINCT `reserva`, dib_cataleg.TITOL AS llibre, `data_inici`, `data_fi`, dib_reserves.`estat`, `prolongada`, `motiu_prolongacio` 
                FROM dib_reserves 
                JOIN dib_exemplars ON dib_reserves.exemplar_id = dib_exemplars.IDENTIFICADOR 
                JOIN dib_cataleg ON dib_exemplars.IDENTIFICADOR = dib_cataleg.NUMERO WHERE usuari_id = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, 'i', $userId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $reservas[] = $row;
            }
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
        }
        mysqli_close($conn);
        return json_encode($reservas);
    }
}

class API_Stats
{
    public function getUserStats()
    {
        $conn = peticionSQL();
        $sql = "SELECT 
                    COUNT(*) AS 'total', 
                    COUNT(CASE WHEN estat = 'actiu' THEN 1 END) AS 'actius', 
                    COUNT(CASE WHEN estat = 'inactiu' THEN 1 END) AS 'inactius', 
                    COUNT(CASE WHEN estat = 'expulsat' THEN 1 END) AS 'expulsats', 
                    COUNT(CASE WHEN estat = 'expulsat-temp' THEN 1 END) AS 'expulsat_temp'
                FROM 
                    dib_usuaris;";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return json_encode(['response' => 'OK', 'stats' => $row]);
        } else {
            return json_encode(['response' => 'ERROR', 'message' => 'Error en la consulta: ' . mysqli_error($conn)]);
        }
    }

    public function getBookStats()
    {
        $conn = peticionSQL();
        $sql = "SELECT 
                    (SELECT COUNT(DISTINCT NUMERO) FROM dib_cataleg) as total,
                    (SELECT COUNT(IDENTIFICADOR) FROM dib_exemplars) as totalExemplars;
                ";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return json_encode(['response' => 'OK', 'stats' => $row]);
        } else {
            return json_encode(['response' => 'ERROR', 'message' => 'Error en la consulta: ' . mysqli_error($conn)]);
        }
    }

    
}