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

          __   ___         ___     __   __                   __
  | |\ | |  \ |__  \_/    |__     /  ` /  \  |\/|  /\  |\ | |  \ |
  | | \| |__/ |___ / \    |___    \__, \__/  |  | /~~\ | \| |__/ |
Modifica questo file solo se sai quello che fai. Leggi le docs.

This work is licensed under a Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License.
*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "StandardLimitati by <a href='htpps://t.me/NetworkCiano'>NetworkCiano</a> - Versione 4.0.0";

if($chatID < 0) exit; //Evita che il bot sia utilizzato nei gruppi
$api = $_GET["apikey"];
$update = json_decode(file_get_contents("php://input"), true);

require_once "functions.php";
include 'settings.php';

//Connessione al Database
$db = new PDO("mysql:host=" . $host . ";dbname=".$wdb, $dbuser, $dbpass);
$db->exec('SET NAMES utf8mb4');

//Creazione tabella al primo avvio
if($INSTALLAZIONE == true)  {
  $db->query('CREATE TABLE IF NOT EXISTS '.$tabella.' (
  id int(0) AUTO_INCREMENT,
  chat_id bigint(0),
  username varchar(200),
  page varchar(200),
  PRIMARY KEY (id))');
  $error = print_r($db->errorInfo(), true);
  sm($chatID, "Tabella 1: \n".print_r($db->errorInfo(), true));
  $db->query('CREATE TABLE IF NOT EXISTS '.'msg'.$tabella.' (
  id int(0) AUTO_INCREMENT,
  msgid int(0),
  sender int(0),
  reciever int(0),
  txt text(0),
  PRIMARY KEY (id))');
  sm($chatID, "Tabella 2: \n".print_r($db->errorInfo(), true));
  sm($chatID, 'Se è andato tutto bene, <b>ricorda di disattivare $INSTALLAZIONE.</b>');
}

$uquery = $db->prepare("SELECT * FROM $tabella WHERE chat_id = :id LIMIT 1");
$uquery->execute(array('id' => $userID));
$u =  $uquery->fetch(PDO::FETCH_ASSOC);

if(!$u['id']) $db->query("INSERT into `$tabella` (chat_id, page, username) VALUES ($chatID, ''," . '"'. $username.'"'.")"); //Se non sei nel DB, vieni inserito
if($u['page'] == 'ban') exit; //ban
if($u['username'] != $username) $db->query("UPDATE $tabella SET username = '$username' WHERE chat_id = '$userID'"); //Aggiorna l'username nel DB se viene cambiato

//Menu del bot
if($directChat == false) {

  $menustart = [
    [
      [
          "text" => "☎️ Avvia Chat",
          "callback_data"  => "/chat"
      ],
    ],
    [
      [
          "text" => $btnComando,
          "callback_data"  => "/demo"
      ],
      [
          "text" => "🔌 Crea un bot uguale",
          "url"  => "https://t.me/StandardLimitati"
      ],
    ],
  ];

  if ($msg === '/start') {
    sm($chatID, $startmsg, $menustart);
    $db->query("UPDATE $tabella SET page = '' WHERE chat_id = '$userID'");
  }
  if ($msg === '/back') {
    if($cbid) cb_reply($cbid, "", true, $cbmid, $startmsg, $menustart);
    if(!$cbid)  sm($chatID, $startmsg, $menustart);
    $db->query("UPDATE $tabella SET page = '' WHERE chat_id = '$userID'");
  }

}

if($msg === '/demo')  {
  $menu = [
    [
      [
          "text" => "🔙 Indietro",
          "callback_data"  => "/back"
      ],
    ],
  ];
  cb_reply($cbid, "", true, $cbmid, $msgextra, $menu);
}

//Funzione chat - Utente
if($msg === '/chat' or $msg === '/start' and $directChat == true)  {
  $num = array_rand($adminID);
  if($btnCambiaOperatore == true) {
    $menu[] = [
      [
        "text" => "📞 Cambia operatore",
        "callback_data"  => '/chat'
      ]
    ];
  }
  if($directChat == false) {
    $menu[] = [
      [
        "text" => "🔙 Indietro",
        "callback_data"  => "/back"
      ],
    ];
  }
  if($adminsAnonymous == true) {
    $t = 'l\'admin '.$num;
  }else{
    $t = '@'.$adminUsername[$num];
  }
  $testo = "☎️ <b>Benvenuto nella chat!</b>\nSei stato messo in contatto con ".$t.", ora invia il tuo messaggio.\nUsa /start quando vuoi per uscire dalla chat.";
  if($cbid) cb_reply($cbid, "", true, $cbmid, $testo, $menu);
  if(!$cbid) sm($chatID, $testo, $menu);
  $db->query("UPDATE $tabella SET page = 'chat-$adminID[$num]' WHERE chat_id = '$userID'");
}

if(strpos($u['page'], 'chat-') === 0 and strpos($msg, '/') !== 0) {
  if($everyAdminRecieves == true) {
    foreach($adminID as $singleAdmin) {
      $msgid = json_decode(forward($singleAdmin, $chatID, $messageid), true)['result']['message_id'];
      $db->query("INSERT into `msg$tabella` (msgid, sender, reciever, txt) VALUES ('$msgid', '$chatID', '$singleAdmin', '$msg')");
    }
    if($notifySentUser == true) sm($chatID, "Messaggio inviato.");
    exit;
  }
  $idadmin = explode('-', $u['page'])[1];
  if($notifySentUser == true) sm($chatID, "Messaggio inviato.");
  $msgid = json_decode(forward($idadmin, $chatID, $messageid), true)['result']['message_id'];
  $db->query("INSERT into `msg$tabella` (msgid, sender, reciever, txt) VALUES ('$msgid', '$chatID', '$idadmin', '$msg')");
}

if(in_array($userID, $adminID)) {

  if($msg === '/post') {
    $db->query("UPDATE $tabella SET page = 'post' WHERE chat_id = '".$userID."'");
    $menu[] = [
      [
        "text" => "🔙 Annulla",
        "callback_data"  => "/back"
      ],
    ];
    sm($chatID, 'Invia ora il messaggio da mandare.', $menu);
  }

  if($u['page'] === 'post' and strpos($msg, '/') !== 0) {
    $everyuquery = $db->query("SELECT chat_id FROM `$tabella`");
    $everyu =  $everyuquery->fetchAll(PDO::FETCH_ASSOC);
    $users = $everyuquery->rowCount();
    sm($chatID, 'Inizio a inviare il post a '.$users.' utenti.');
    foreach($everyu as $singleu) {
      sm($singleu['chat_id'], $msg);
      if($everyu['chat_id'][100]) sleep(1);
    }
    sm($chatID, 'Fatto, post inviato.');
    $db->query("UPDATE $tabella SET page = '' WHERE chat_id = '".$userID."'");
  }

  if($msg === '/users') {
    $everyuquery = $db->query("SELECT chat_id FROM `$tabella`");
    $users = $everyuquery->rowCount();
    sm($chatID, 'Il bot conta '.$users.' utenti.');
  }

  if($replyto) {
    $replyquery = $db->prepare("SELECT * FROM `msg$tabella` WHERE msgid = :id LIMIT 1");
    $replyquery->execute(array('id' => $replyto));
    $reply =  $replyquery->fetch(PDO::FETCH_ASSOC);

    if(strpos($msg, '/ban') === 0) {
      $db->query("UPDATE $tabella SET page = 'ban' WHERE chat_id = '".$reply['sender']."'");
      sm($chatID, "Utente bannato.");
      sm($reply['sender'], "🚫 <b>Sei stato bannato!</b> Non potrai più utilizzare il bot.");
      exit;
    }
    if(strpos($msg, '/unban') === 0) {
      $db->query("UPDATE $tabella SET page = 'chat-$userID' WHERE chat_id = '".$reply['sender']."'");
      sm($chatID, "Utente sbannato.");
      sm($reply['sender'], "✅ <b>Sei stato sbannato!</b> Ora puoi utilizzare il bot.");
      exit;
    }

    if($adminsAnonymous == true) {
      sm($reply['sender'], "🗣 <b>Admin:</b> $msg");
    }else{
      forward($reply['sender'], $chatID, $messageid);
    }
    if($notifySentAdmin == true) sm($chatID, "Risposta inviata.");
  }
}
