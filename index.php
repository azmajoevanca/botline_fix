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
        $pesanUser = $event['message']['text'];
        $pesanStr = strtolower($pesanUser);
        $pesanID = $event['message']['id'];
        $dapatkanID = $event['source']['userId'];
        $arguments = explode(" ",$pesanUser);

        include "koneksi.php";
			$sql1 = "SELECT * FROM data_konsumens where id_api='".$dapatkanID."'";
			$row4 = mysqli_query($koneksi,$sql1);
			if ($row4===FALSE) {
				die(mysqli_error($koneksi));
			}
			$rows4 = mysqli_fetch_array($row4);

        $res = $bot->getProfile($dapatkanID);
        $profile = $res->getJSONDecodedBody();
        $displayName = $profile['displayName'];

        $response = $bot->getMessageContent($pesanID);

        if($arguments[0] == "menu" && ($dapatkanID == $rows4 ['id_api'])) 
        {
            $message = "berhasil";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
            $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
            return $result->getHTTPStatus() . ' ' . $result->getRawBody();
        }
		if($arguments[0] !== "daftar" && empty ($rows4['id_api'])) 
		{
            $message = "Silahkan ketik #daftar spasi no identitas untuk menggunakan bot ini";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
			$result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
			return $result->getHTTPStatus() . ' ' . $result->getRawBody();
        } elseif ($arguments[0] == "daftar") 
        {
            if (!preg_match("/^[0-9]*$/", $arguments[1])) {
                $message = "Input nomer identitas yang benar. Input hanya berupa angka";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            }
            elseif (strlen($arguments[1]) < 12) {
                $message = "Input nomer identitas yang benar. Input harus 12 Angka";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            }
            elseif (strlen($arguments[1]) > 12) {
                $message = "Input nomer identitas yang benar. Input harus 12 Angka";
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();                
            } 
            else 
            {
                include "koneksi.php";
                date_default_timezone_set('Asia/Jakarta');
                $datenow = date('Y-m-d H:i:s', strtotime('+90 minutes'));
                $nik = $arguments[1];

                $mysqli7 = "INSERT INTO data_konsumens (nama_konsumen, username, id_api, id_identitas, telepon, alamat, tipe , status, tanggal_daftar, created_at, updated_at) 
                VALUES ('".$displayName."' ,'".$displayName."','".$dapatkanID."','".$nik."', 'NULL','NULL','LINE','0','".$datenow."','','')";;
                $data = mysqli_query($koneksi,$mysqli7);

                $queri1 = "SELECT * FROM data_konsumens where id_identitas='".$nik."'";
                $rowqueris = mysqli_query($koneksi,$queri1);
                if ($rowqueris === FALSE) {
                    die(mysqli_error($koneksi));
                }
                $rowqueri = mysqli_fetch_array($rowqueris);

                if (isset($rowqueri['id']) && ''.$rowqueri['status'].'' == '0') {
                    $message = "Selamat nomer identitas berhasil disimpan dengan id".$nik." Untuk melanjutkan menggunakan bot silahkan ketik #menu";
                    $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
                    $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                    return $result->getHTTPStatus() . ' ' . $result->getRawBody();
                }
            }
			// $message = "Ini nik kamu ".$nik."";
            // $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
			// $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
			// return $result->getHTTPStatus() . ' ' . $result->getRawBody();
        }
        else 
        {
            $message = "Anda sudah terdaftar. Ketik menu untuk menggunakan bot ini";
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message);
			$result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
			return $result->getHTTPStatus() . ' ' . $result->getRawBody();
        }
      
        

    } //batas foreach
    }); //batas app
    $app->run();

