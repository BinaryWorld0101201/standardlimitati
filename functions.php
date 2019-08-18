<?php

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
    $msg = $cbdata; //può essere pericoloso fare così, uno potrebbe erroneamente trattare i cbdata come dei messaggi e viceversa
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

function sm(
    $chatID,
    $text,
    $rmf = false,
    $pm = 'HTML',
    $dis = true, // non è usato .-.
    $replyto = true, // non è usato .-.
    $inline = true,
    $dil = true, // non è usato .-.
    $ff = false,
    $ri = false
) {
    global $api;

    $args = [
        'chat_id'                  => $chatID,
        'text'                     => $text,
        'disable_web_page_preview' => true,
        'disable_notification'     => true,
        'parse_mode'               => $pm,
        'reply_to_message_id'      => $ri,
        'forward_from'             => $ff
    ];
    //if($replyto) $args["reply_to_message_id"] = $update["message"]["message_id"];
    if (is_array($rmf)) {
        if ($inline) {
            $rm = [
                "inline_keyboard" => $rmf
            ];
        } else {
            $rm = [
                "keyboard"        => $rmf,
                "resize_keyboard" => true
            ];
        }
        $args["reply_markup"] = json_encode($rm);
    }
    $r = new HttpRequest("post", "https://api.telegram.org/$api/sendmessage", $args);

    return $r->getResponse();
}

function cb_reply($id, $text, $alert = false, $cbmid = false, $ntext = false, $nmenu = false, $npm = "HTML")
{
    global $api;
    global $chatID;

    $args = [
        "callback_query_id" => $id,
        "text"              => $text,
        "show_alert"        => $alert
    ];
    $r = new HttpRequest("post", "https://api.telegram.org/$api/answerCallbackQuery", $args);

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
        $r = new HttpRequest("post", "https://api.telegram.org/$api/editMessageText", $args);
    }

    return $r->getResponse(); //può restituire sia una risposta di answercallback che di editmessage, sarebbe da rifare proprio la funzione...
}
