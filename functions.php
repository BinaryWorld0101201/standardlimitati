<?php
function sm($chatID, $text, $rmf = false, $pm = 'HTML', $dis = true, $replyto = false, $inline = false, $dil=true, $ff= false, $ri= false)
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
