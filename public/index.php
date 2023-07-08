<?php
require __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;

$_ENV['PASS_SIGNATURE'] = true;
$_ENV['CHANNEL_SECRET'] = "7f3c00c30be90034d07a1b0ee848dc17";
$_ENV['CHANNEL_ACCESS_TOKEN'] = "eDvcGNdLX6WtAGlEIuTOX8hhYJf2ERp86xpoq0wazl0mWrKP8epGX9Eor6OcjjuPB5KCUpOuQ0j2BVf4/ZY49K8QVbO8O7s1UK3dOj3orw47UuhEzhV2cmfBpfO41FqR+htLzGLWmG5w0UqZJAOhMgdB04t89/1O/w1cDnyilFU=";
$pass_signature = true;

// set LINE channel_access_token and channel_secret
$channel_access_token = "eDvcGNdLX6WtAGlEIuTOX8hhYJf2ERp86xpoq0wazl0mWrKP8epGX9Eor6OcjjuPB5KCUpOuQ0j2BVf4/ZY49K8QVbO8O7s1UK3dOj3orw47UuhEzhV2cmfBpfO41FqR+htLzGLWmG5w0UqZJAOhMgdB04t89/1O/w1cDnyilFU=";
$channel_secret = "7f3c00c30be90034d07a1b0ee848dc17";

// inisiasi objek bot
$httpClient = new CurlHTTPClient($channel_access_token);
$bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);

$app = AppFactory::create();
$app->setBasePath("/public");

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello World!");
    return $response;
});

$app->post(
    '/webhook',
    function (Request $request, Response $response) use ($channel_secret, $bot, $httpClient, $pass_signature) {
        // get request body and line signature header
        $body      = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

        // log body and signature
        file_put_contents('php://stderr', 'Body: ' . $body);

        // is LINE_SIGNATURE exists in request header?
        if (empty($signature)) {
            return $response->withStatus(400, 'Signature not set');
        }

        // is this request comes from LINE?
        if ($_ENV['PASS_SIGNATURE'] == false && !SignatureValidator::validateSignature($body, $_ENV['CHANNEL_SECRET'], $signature)) {
            return $response->withStatus(400, 'Invalid signature');
        }

        // init bot
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV['CHANNEL_ACCESS_TOKEN']);
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV['CHANNEL_SECRET']]);
        $data = json_decode($body, true);
        foreach ($data['events'] as $event) {
            $userMessage0 = $event['message']['text'];
            $userMessage1 = strtolower($userMessage0);
            $userMessage2 = $event['message']['id'];
            $userMessage3 = $event['source']['userId'];

            include "../koneksi.php";
            $sql1 = "SELECT * FROM data_konsumens where id_api='" . $userMessage3 . "'";
            $row1 = mysqli_query($koneksi, $sql1);
            if ($row1 === FALSE) {
                die(mysqli_error($koneksi));
            }
            $rows1 = mysqli_fetch_array($row1);

            $res = $bot->getProfile($userMessage2);
            $profile = $res->getJSONDecodedBody();
            $userId = $profile['userId'];
            $response = $bot->getMessageContent($userMessage2);

            if (strtolower($userMessage0) == "daftar" && $userMessage3 !== $rows1['id_api'] && !preg_match("/^[0-9]*$/", $userMessage0)) {
                $message = "input no identitas";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            } else if (strtolower($userMessage0) !== null && empty($rows1['id'])) {
                if (!preg_match("/^[0-9]*$/", $userMessage0)) {
                    $message = "Input nomer identitas yang benar. Input hanya berupa angka";
                    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                    $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                    return $result->getHTTPStatus() . ' ' . $result->getRawBody();
                } elseif (strlen($userMessage0) < 12 || strlen($userMessage0) > 12 ) {
                    $message = "Input nomer identitas yang benar. Input harus 12 Angka";
                    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                    $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                    return $result->getHTTPStatus() . ' ' . $result->getRawBody();
                } else {
                    include "../koneksi.php";
                    date_default_timezone_set('Asia/Jakarta');
                    $datenow = date('Y-m-d H:i:s', strtotime('+90 minutes'));
    
                    $mysqli7 = "INSERT INTO data_konsumens (nama_konsumen, username, id_api, id_identitas, telepon, alamat, tipe , status, tanggal_daftar, created_at, updated_at) 
                    VALUES ('' ,'".$displayName."','".$userMessage3."','".$userMessage0."', 'NULL','NULL','LINE','0','".$datenow."','','')";;
                    $data = mysqli_query($koneksi,$mysqli7);
    
                    $queri1 = "SELECT * FROM data_konsumens where id_identitas='".$userMessage0."'";
                    $rowqueris = mysqli_query($koneksi,$queri1);
                    if ($rowqueris === FALSE) {
                        die(mysqli_error($koneksi));
                    }
                    $rowqueri = mysqli_fetch_array($rowqueris);
    
                    $enkripsi = md5(''.$rowqueri['id'].'');
                    if (isset($rowqueri['id']) && ''.$rowqueri['status'].'' == '0') {
                        $message = "Selamat nomer identitas berhasil disimpan. Anda dapat melakukan transaksi".$enkripsi."";
                        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                        $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                        return $result->getHTTPStatus() . ' ' . $result->getRawBody();    
                    }                
                }   
            }
            if (strtolower($userMessage0) !== "list" && $userMessage3 == $rows1['id_api'] && !preg_match("/^[0-9]*$/", $userMessage0)) {
                $message = "ketik list";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            } else if (strtolower($userMessage0) == '1' && $userMessage3 == $rows1['id_api']) {
                $message = "Anda memilih tampilkan data user";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            } else if (strtolower($userMessage0) == '2') {
                $message = "Anda memilih tampilkan list produk";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            } else if (strtolower($userMessage0) == '3') {
                $message = "Anda memilih menu transaksi";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            } else if (strtolower($userMessage0) == '4') {
                $message = "Anda memilih prosedur transaksi";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            } else if (strtolower($userMessage0) == 'list') {
                $message = "Pilihan list sebagai berikut : 
                    1. Data User
                    2. List Produk
                    3. Menu Transaksi
                    4. Prosedur Transaksi";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            } else {

                $message = "Anda belum terdaftar. Ketik Daftar";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            }
        }
    } //batas foreach
); //batas app
$app->run();
