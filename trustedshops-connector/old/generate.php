<?php

/** añadido de gpt */
ob_start();
/**************** */

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);



// CREDENCIALES DE ACCESO AL GENERADOR DE ENLACES.

const USERNAME = 'ceramicconnection';

const PASSWORD = 'WceWCeyIk0';

const SECRET = 'BQg23WzVWHjNZc0pJNDzsx29OjWY6ocd';

const TOKEN_VALID_SECONDS = 24 * 60 * 60;



// CREDENCIALES DE ACCESO A LA BASE DE DATOS DE PRESTASHOP.

//const DB_HOST = 'localhost:3306';
const DB_HOST = '127.0.0.1';

const DB_USERNAME = 'cerami27_usernew';

const DB_PASSWORD = 'Bi1.A,uEm?O*';

const DB_DATABASE = 'cerami27_dbnew';



// CREDENCIALES DE AUTENTICACIÓN DE LA API DE TRUSTED SHOPS.

const CLIENT_ID = '6d450b8cb83b__conector-planatec';

const CLIENT_KEY = 'YBwks3F7pjwu0fPDWLe1vX9TtiS1VvBn';

const SYSTEM = 'planatec-connector';

const SYSTEM_VERSION = '1.0';



// CONFIGURACIÓN DE LOS FORMULARIOS DE LOS ENLACES A GENERAR.

const QUESTIONNAIRE_ID = 'tpl-qst-baaec16a-7fd6-4815-b119-9aadea3cf986';

const CHANNELS = [

    1 => [

        'id' => 'chl-40958f75-c38f-433c-b917-22dd65436842',

        'name' => 'Tienda en español',

    ],

    2 => [

        'id' => 'chl-6f051731-ef59-4f65-849d-d1c69640a300',

        'name' => 'Tienda en francés',

    ],

    3 => [

        'id' => 'chl-dafbe567-6893-45ab-a4f1-b5fbc962f2fd',

        'name' => 'Tienda en inglés',

    ],

];





// =====================================================================================================================





function trustedShopsLogin()

{

    $ch = curl_init();



    curl_setopt($ch, CURLOPT_URL, "https://login.etrusted.com/oauth/token");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt(

        $ch,

        CURLOPT_POSTFIELDS,

        "client_id=" . CLIENT_ID .

        "&client_secret=" . CLIENT_KEY .

        "&grant_type=client_credentials" .

        "&audience=" . urlencode("https://api.etrusted.com")

    );

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(

        'Content-Type: application/x-www-form-urlencoded',

        'Accept: application/json'

    ));



    $result = curl_exec($ch);

    curl_close($ch);



    return json_decode($result)->access_token;

}



function generateLink($token, $reference, $email, $channelId)

{

    $ch = curl_init();



    curl_setopt($ch, CURLOPT_URL, "https://api.etrusted.com/questionnaire-links");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt(

        $ch,

        CURLOPT_POSTFIELDS,

        sprintf(

            '{

  "type": "link_generation",

  "questionnaireTemplate": {

    "id": "%s"

  },

  "customer": {

    "email": "%s"

  },

  "channel": {

    "id": "%s",

    "type": "etrusted"

  },

  "transaction": {

    "reference": "%s"

  },

  "system": "%s",

  "systemVersion": "%s"

}',

            QUESTIONNAIRE_ID,

            $email,

            $channelId,

            $reference,

            SYSTEM,

            SYSTEM_VERSION

        ));

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(

        'Authorization: Bearer ' . $token,

        'Content-Type: application/json',

        'Accept: application/json'

    ));



    $result = curl_exec($ch);

    curl_close($ch);



    return json_decode($result)->link;

}



function getTokenSubject()

{

    return md5(USERNAME . substr(hash_hmac('sha256', PASSWORD, SECRET), 0, 10));

}



function generateTimestampHmac($timestamp)

{

    return hash_hmac('sha256', USERNAME . $timestamp, SECRET);

}


/*
function generateToken()

{

    $timestamp = (new DateTime())->getTimestamp() + TOKEN_VALID_SECONDS;

    $subject = getTokenSubject();



    $hmac = generateTimestampHmac($timestamp);



    return base64_encode(openssl_encrypt("$subject$timestamp.$hmac", 'aes-256-cbc', SECRET));

}

function validateToken($token)

{

    if (!$token) {

        return false;

    }



    $data = openssl_decrypt(base64_decode($token), 'aes-256-cbc', SECRET);

    $chunks = explode('.', $data);

    if (count($chunks) !== 2) {

        return false;

    }



    $data = $chunks[0];

    $subject = getTokenSubject();

    if (substr($data, 0, strlen($subject)) !== $subject) {

        return false;

    }



    $timestamp = substr($data, strlen($subject));

    if (!is_numeric($timestamp)) {

        return false;

    }



    $timestamp = intval($timestamp);

    $now = (new DateTime())->getTimestamp();

    if ($now > $timestamp) {

        return false;

    }



    $hmac = $chunks[1];

    if ($hmac !== generateTimestampHmac($timestamp)) {

        return false;

    }



    return true;

}
*/

function generateToken()
{
    $timestamp = (new DateTime())->getTimestamp() + TOKEN_VALID_SECONDS;
    $subject = getTokenSubject();
    $hmac = generateTimestampHmac($timestamp);
    $data = "$subject$timestamp.$hmac";

    // AES-256-CBC requiere un IV de 16 bytes
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivLength);

    $encrypted = openssl_encrypt($data, 'aes-256-cbc', SECRET, 0, $iv);

    // Empaquetar IV + encrypted, separados por ':'
    return base64_encode($iv . ':' . $encrypted);
}

function validateToken($token)
{
    if (!$token) return false;

    $decoded = base64_decode($token);
    list($iv, $encrypted) = explode(':', $decoded, 2);

    $data = openssl_decrypt($encrypted, 'aes-256-cbc', SECRET, 0, $iv);

    if (!$data) return false;

    $chunks = explode('.', $data);
    if (count($chunks) !== 2) return false;

    $data = $chunks[0];
    $subject = getTokenSubject();
    if (substr($data, 0, strlen($subject)) !== $subject) return false;

    $timestamp = substr($data, strlen($subject));
    if (!is_numeric($timestamp)) return false;

    $timestamp = intval($timestamp);
    if ((new DateTime())->getTimestamp() > $timestamp) return false;

    $hmac = $chunks[1];
    return $hmac === generateTimestampHmac($timestamp);
}




function fetchOrderData($reference)

{

    $dsn = sprintf("mysql:host=%s;dbname=%s", DB_HOST, DB_DATABASE);

    $db = new PDO($dsn, DB_USERNAME, DB_PASSWORD);



    $statement = $db->prepare("

        SELECT c.email, o.id_lang

        FROM ps_orders o

            LEFT JOIN ps_customer c ON(o.id_customer = c.id_customer)

        WHERE o.reference = :ref

    ");

    $statement->execute(['ref' => $reference]);



    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$result) {

        return null;

    }



    return [

        'email' => $result['email'],

        'channel' => $result['id_lang'],

    ];

}





// =====================================================================================================================





$ERRORS = [];

$RESULT = null;



try {

    // VALIDACIÓN DEL TOKEN.

    $TOKEN = $_GET['t'] ?? $_POST['t'] ?? '';

    $ACCESS_GRANTED = validateToken($TOKEN);

    if (!$ACCESS_GRANTED && (isset($_GET['t']) || isset($_POST['t']))) {

        header('Location: ' . $_SERVER['PHP_SELF']);

        die;

    }



    // INICIO DE SESIÓN.

    if (isset($_POST['username']) && isset($_POST['password'])) {

        if ($_POST['username'] === USERNAME && $_POST['password'] === PASSWORD) {

            $TOKEN = generateToken();



            header('Location: ' . $_SERVER['PHP_SELF'] . '?t=' . $TOKEN);

            die;

        }



        $ERRORS[] = 'Credenciales inválidas.';

    }



    // GENERACIÓN DE UN ENLACE.

    if (isset($_POST['reference'])) {

        $order = fetchOrderData($_POST['reference']);

        if ($order === null) {

            throw new Exception('Pedido no encontrado.');

        }



        if ($order['email'] === null) {

            throw new Exception('El usuario vinculado al pedido no existe.');

        }



        $channel = CHANNELS[$order['channel']];

        if ($channel['id'] === null) {

            throw new Exception(sprintf('No existe un canal en TrustedShops para los pedidos de la %s.', $channel['name']));

        }



        $trustedShopsToken = trustedShopsLogin();

        $link = generateLink($trustedShopsToken, $_POST['reference'], $order['email'], $channel['id']);



        $RESULT = [

            'reference' => $_POST['reference'],

            'email' => $order['email'],

            'link' => $link,

        ];

    }

} catch (Exception $e) {

    $ERRORS[] = $e->getMessage();

}



?>



<?php ob_start(); ?>

<div>

    <?php foreach ($ERRORS as $ERROR): ?>

        <div class="alert alert-danger"><?= $ERROR ?></div>

    <?php endforeach; ?>

</div>

<?php $ERRORS_HTML = ob_get_clean(); ?>



<!doctype html>

<html lang="es" class="h-100">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"

          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Generador de enlaces Trusted Shops</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"

          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

</head>

<body data-bs-theme="dark" class="h-100">

<div class="container d-grid justify-content-center align-items-center min-vh-100">

    <div class="inner">

        <?php if (!$ACCESS_GRANTED): ?>

            <h1 class="text-center mb-4">ACCESO</h1>



            <?= $ERRORS_HTML ?>



            <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">

                <div class="mb-3">

                    <label for="username" class="form-label">Usuario</label>

                    <input type="text" class="form-control" name="username" id="username">

                </div>

                <div class="mb-3">

                    <label for="password" class="form-label">Contraseña</label>

                    <input type="password" class="form-control" name="password" id="password">

                </div>

                <button type="submit" class="btn btn-primary d-block w-100">Acceder</button>

            </form>

        <?php else: ?>

            <?php if ($RESULT === null): ?>

                <h1 class="text-center mb-4">GENERAR ENLACE</h1>



                <?= $ERRORS_HTML ?>

            <?php else: ?>

                <h1 class="text-center mb-4">¡ENLACE GENERADO!</h1>



                <div>

                    <div class="alert alert-success">¡El enlace se ha generado correctamente!</div>

                </div>



                <dl>

                    <dt>Referencia de pedido:</dt>

                    <dd><?= $RESULT['reference'] ?></dd>



                    <dt>Email de cliente:</dt>

                    <dd><?= $RESULT['email'] ?></dd>



                    <dt>Enlace:</dt>

                    <dd><a href="<?= $RESULT['link'] ?>" target="_blank"><?= $RESULT['link'] ?></a></dd>

                </dl>



                <h1 class="text-center mt-5 mb-4">GENERAR OTRO ENLACE</h1>



                <?= $ERRORS_HTML ?>

            <?php endif; ?>



            <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">

                <input type="hidden" name="t" value="<?= $TOKEN ?>">



                <div class="mb-3">

                    <label for="reference" class="form-label">Pedido</label>

                    <input type="text" class="form-control" name="reference" id="reference">

                    <div class="form-text">Introduce la referencia del pedido de PrestaShop</div>

                </div>

                <button type="submit" class="btn btn-primary d-block w-100">Generar</button>

            </form>

        <?php endif; ?>

    </div>

</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"

        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"

        crossorigin="anonymous"></script>

</body>

</html>

