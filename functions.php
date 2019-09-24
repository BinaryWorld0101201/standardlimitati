<?php

/*
           _____ _                  _               _ _      _           _ _        _   _
    ____  / ____| |                | |             | | |    (_)         (_) |      | | (_) V4.5.0
   / __ \| (___ | |_ __ _ _ __   __| | __ _ _ __ __| | |     _ _ __ ___  _| |_ __ _| |_ _
  / / _` |\___ \| __/ _` | '_ \ / _` |/ _` | '__/ _` | |    | | '_ ` _ \| | __/ _` | __| |
 | | (_| |____) | || (_| | | | | (_| | (_| | | | (_| | |____| | | | | | | | || (_| | |_| |
  \ \__,_|_____/ \__\__,_|_| |_|\__,_|\__,_|_|  \__,_|______|_|_| |_| |_|_|\__\__,_|\__|_|
   \____/
  Base per creare LimitatiBot in PHP 7. Creata da @cianoscatolo.

   ___           __    __
  |__  |  | |\ |  / | /  \ |\ | |
  |    \__/ | \| /_ | \__/ | \| |
Modifica questo file solo se sai quello che fai. Leggi le docs.
Grazie @Mastmat per la classe http

This work is licensed under a Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License.
*/

class http{
private $curl;
public function __construct(){
	$this->curl = curl_init();
}

public function get($url){
curl_setopt_array($this->curl, [
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    CURLOPT_URL => $url
]);
$resp = curl_exec($this->curl);
return $resp;
}

public function post($url, $postfields){
curl_setopt_array($this->curl, [
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    CURLOPT_URL => $url,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $postfields
]);
$resp = curl_exec($this->curl);
return $resp;
}

public function __destruct(){
curl_close($this->curl);
}
}

$http = new http;

if (isset($update['message'])) {
    $userID = $update['message']['from']['id'];
    $msg = $update['message']['text'];
    $chatID = $update['message']['chat']['id'];
    $username = $update['message']['from']['username'];
    $messageid = $update['message']['message_id'];
    $replyto = $update['message']['reply_to_message']['message_id'];
    $replyforward = $update['message']['reply_to_message']['forward_from'];
    $replytoid = $update['message']['reply_to_message']['from']['id'];
    $rfid = $update['message']['reply_to_message']['forward_from']['id'];
} elseif (isset($update['callback_query'])) {
    $cbid = $update['callback_query']['id'];
    $cbdata = $update['callback_query']['data'];
    $msg = $cbdata;
    $cbmid = $update['callback_query']['message']['message_id'];
    $chatID = $update['callback_query']['message']['chat']['id'];
    $userID = $update['callback_query']['from']['id'];
    $nome = $update['callback_query']['from']['first_name'];
    $cognome = $update['callback_query']['from']['last_name'];
    $username = $update['callback_query']['from']['username'];
}

if(!$username)  {
  $username = " <i>No username</i>";
}

function sm($chatID, $text, $rmf = false){
    global $api;
    global $http;

    $args = array(
    'chat_id' => $chatID,
    'text' => $text,
    'parse_mode' => 'HTML'
     );

    if($rmf){
        $rm = array('inline_keyboard' => $rmf);
        $rm = json_encode($rm);
        $args['reply_markup'] = $rm;
    }

    $resp = $http->post('https://api.telegram.org/'.$api.'/sendMessage', $args);
    return $resp;
}

function cb_reply($id, $text, $alert = false, $cbmid = false, $ntext = false, $nmenu = false, $npm = "HTML")
{
    global $api;
    global $http;
    global $chatID;

    $args = [
        "callback_query_id" => $id,
        "text"              => $text,
        "show_alert"        => $alert
    ];
    $resp = $http->post("https://api.telegram.org/$api/answerCallbackQuery", $args);

    if ($cbmid !== false) {
        $args = [
            "chat_id"                  => $chatID,
            "message_id"               => $cbmid,
            "text"                     => $ntext,
            "parse_mode"               => $npm,
            "disable_web_page_preview" => true
        ];

        if (is_array($nmenu)) {
            $args["reply_markup"] = json_encode(["inline_keyboard" => $nmenu]);
        }
        $resp = $http->post("https://api.telegram.org/$api/editMessageText", $args);
    }

    return $resp;
}


function forward($chat_id, $from_chat_id, $message_id) {
  global $api;
  global $http;
  $args = [
      "chat_id"                  => $chat_id,
      "from_chat_id"             => $from_chat_id,
      "message_id"               => $message_id
  ];
  $resp = $http->post("https://api.telegram.org/$api/ForwardMessage", $args);
  return $resp;
}
