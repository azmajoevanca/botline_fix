<?php
require __DIR__ . '/vendor/autoload.php';

use \LINE\LINEBot\SignatureValidator as SignatureValidator;

// load config
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// initiate app
$configs =  [
    'settings' => ['displayErrorDetails' => true],
];
$app = new Slim\App($configs);

/* ROUTES */
$app->get('/', function ($request, $response) {
    return "Lanjutkan!";
});

$app->post('/', function ($request, $response)
{
    // get request body and line signature header
    $body      = file_get_contents('php://input');
    $signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

    // log body and signature
    file_put_contents('php://stderr', 'Body: '.$body);

    // is LINE_SIGNATURE exists in request header?
    if (empty($signature)){
        return $response->withStatus(400, 'Signature not set');
    }

    // is this request comes from LINE?
    if($_ENV['PASS_SIGNATURE'] == false && ! SignatureValidator::validateSignature($body, $_ENV['CHANNEL_SECRET'], $signature)){
        return $response->withStatus(400, 'Invalid signature');
    }

    // init bot
    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV['CHANNEL_ACCESS_TOKEN']);
    $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV['CHANNEL_SECRET']]);
    $data = json_decode($body, true);

    foreach ($data['events'] as $event)
    {
        $userMessage0 = $event['message']['text'];
        $userMessage1 = strtolower($userMessage0);
        $userMessage2 = $event['message']['id'];
        $userMessage3 = $event['source']['userId'];

			include "koneksi.php";
			$sql1 = "SELECT * FROM data_konsumens where id_api='".$userMessage3."'";
			$row4 = mysqli_query($koneksi,$sql1);
			if ($row4===FALSE) {
				die(mysqli_error($koneksi));
			}
			$rows4 = mysqli_fetch_array($row4);

        $res = $bot->getProfile($userMessage2);
        $profile = $res->getJSONDecodedBody();
        $displayName = $profile['displayName'];

        $response = $bot->getMessageContent($userMessage2);

        if (strtolower($userMessage0) == '#daftar' && $rows4['status'] !== 'online_chat' && $rows4['status'] !== 'segera_chat')  
        {
            $message = "Input nomer identitas anda";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
            $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
            return $result->getHTTPStatus() . ' ' . $result->getRawBody();
        }
        elseif (strtolower($userMessage0) !== null && empty ($rows4['id'])) 
        {
            if (!preg_match("/^[0-9]*$/", $userMessage0)) {
                $message = "Input nomer identitas yang benar. Input hanya berupa angka";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            }
            elseif (strlen($userMessage0) < 12) {
                $message = "Input nomer identitas yang benar. Input harus 12 Angka";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            }
            elseif (strlen($userMessage0) > 12) {
                $message = "Input nomer identitas yang benar. Input harus 12 Angka";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();                
            } else {
                include "koneksi.php";
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
                }elseif (isset($rowqueri['id']) && ''.$rowqueri['status'].'' !=='0'){
                    $message = "Identitas tersebut sudah terdaftar!";
                    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                    $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                    return $result->getHTTPStatus() . ' ' . $result->getRawBody();    
                }else {
                    $message = "Identitas tersebut tidak terdaftar";
                    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                    $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                    return $result->getHTTPStatus() . ' ' . $result->getRawBody();    
                }
            }
        }
        else (strtolower($userMessage0) !== null && $userMessage3 !== $rows4['id_api'] && !preg_match("/^[0-9]*$/", $userMessage0)); 
        {
            $message = "Anda belum terdaftar, daftar dengan ketik #daftar";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
            $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
            return $result->getHTTPStatus() . ' ' . $result->getRawBody();
        }    
        
    
    
    } //batas foreach
    }); //batas app
    $app->run();

