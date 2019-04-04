<?php

$userID = $update[message][from][id];
$msg = $update[message][text];
$chatID = $update[message][chat][id];
$username = $update[message][from][username];
$messageid = $update[message][message_id];
$replyforward = $update[message][reply_to_message][forward_from];
$rfid = $update[message][reply_to_message][forward_from][id];

if($update["callback_query"])
{
$cbid = $update["callback_query"]["id"];
$cbdata = $update["callback_query"]["data"];
$msg = $cbdata;
$cbmid = $update["callback_query"]["message"]["message_id"];
$chatID = $update["callback_query"]["message"]["chat"]["id"];
$userID = $update["callback_query"]["from"]["id"];
$nome = $update["callback_query"]["from"]["first_name"];
$cognome = $update["callback_query"]["from"]["last_name"];
$username = $update["callback_query"]["from"]["username"];
}

function sm($chatID, $text, $rmf = false, $pm = 'HTML', $dis = true, $replyto = true, $inline = true, $dil=true, $ff= false, $ri= false)
{
global $api;
global $userID;
global $update;
if(!$inline)
{
$rm = array('keyboard' => $rmf,
'resize_keyboard' => true
);
}else{
$rm = array('inline_keyboard' => $rmf,
);
}
$rm = json_encode($rm);
$args = array(
'chat_id' => $chatID,
'text' => $text,
'disable_web_page_preview' => true,
'disable_notification' => true,
'parse_mode' => $pm,
'reply_to_message_id' => $ri,
'forward_from' => $ff
);
if($replyto) $args['reply_to_message_id'] = $update["message"]["message_id"];
if($rmf) $args['reply_markup'] = $rm;
if($text)
{
$r = new HttpRequest("post", "https://api.telegram.org/$api/sendmessage", $args);
$rr = $r->getResponse();
$ar = json_decode($rr, true);
$ok = $ar["ok"]; //false
$e403 = $ar["error_code"];
if($e403 == "403")
{
//bot disabled by user
}
}
}

function cb_reply($id, $text, $alert = false, $cbmid = false, $ntext = false, $nmenu = false, $npm = "HTML")
{
global $api;
global $chatID;
global $config;

$args = array(
'callback_query_id' => $id,
'text' => $text,
'show_alert' => $alert

);
$r = new HttpRequest("get", "https://api.telegram.org/$api/answerCallbackQuery", $args);

if($cbmid)
{
if($nmenu)
{
$rm = array('inline_keyboard' => $nmenu
);
$rm = json_encode($rm);
}


$args = array(
'chat_id' => $chatID,
'message_id' => $cbmid,
'text' => $ntext,
'parse_mode' => $npm,
'disable_web_page_preview' => true,
);
if($nmenu) $args["reply_markup"] = $rm;
$r = new HttpRequest("post", "https://api.telegram.org/$api/editMessageText", $args);


}
}
