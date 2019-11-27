<?php

/*
           _____ _                  _               _ _      _           _ _        _   _
    ____  / ____| |                | |             | | |    (_)         (_) |      | | (_) V5.0.0
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
error_reporting(E_ERROR | E_PARSE);

echo '<br><b>StandardLimitati</b> by <a href=\'htpps://t.me/NetworkCiano\'>NetworkCiano</a> - Versione 5.0.0';

$api = $_GET['apikey'];
$update = json_decode(file_get_contents('php://input'), true);

include 'settings.php';
require_once 'functions.php';

//Connessione al Database
$db = new PDO('mysql:host=' . $host . ';dbname='.$wdb, $dbuser, $dbpass);
$db->exec('SET NAMES utf8mb4');

//Creazione tabella al primo avvio
if($INSTALLAZIONE == true)  {
  $db->query('CREATE TABLE IF NOT EXISTS '.$tabella.' (
  id int(0) AUTO_INCREMENT,
  chat_id bigint(0),
  username varchar(200),
  page varchar(200),
  admin int(1),
  PRIMARY KEY (id))');
  $errorInfo = $db->errorInfo();
  if($errorInfo[0] == 00000 and !$errorInfo[1] and !$errorInfo[2]) {
    sm($chatID, 'La tabella 1 è stata creata con successo.');
  }else{
    sm($chatID, 'La creazione della tabella 1 è fallita.');
  }
  $db->query('CREATE TABLE IF NOT EXISTS '.'msg'.$tabella.' (
  id int(0) AUTO_INCREMENT,
  msgid int(0),
  sender int(0),
  reciever int(0),
  txt text(0),
  PRIMARY KEY (id))');
  $errorInfo = $db->errorInfo();
  if($errorInfo[0] == 00000 and !$errorInfo[1] and !$errorInfo[2]) {
    sm($chatID, 'La tabella 2 è stata creata con successo.');
  }else{
    sm($chatID, 'La creazione della tabella 2 è fallita.');
  }
}

$uquery = $db->prepare("SELECT * FROM $tabella WHERE chat_id = :id LIMIT 1");
$uquery->execute(array('id' => $userID));
$u =  $uquery->fetch(PDO::FETCH_ASSOC);

if($u['page'] == 'ban' or $chatID < 0) exit; //ban + non usare nei gruppi
if(!$u['id'] and $chatID > 0) $db->query("INSERT into `$tabella` (chat_id, page, username) VALUES ($chatID, ''," . '"'. $username.'"'.")"); //Se non sei nel DB, vieni inserito
if($u['username'] != $username) $db->query("UPDATE $tabella SET username = '$username' WHERE chat_id = '$userID'"); //Aggiorna l'username nel DB se viene cambiato

//Menu del bot
if(!$directChat) {

  $menustart = [
    [
      [
          'text' => '☎️ Avvia Chat',
          'callback_data'  => '/chat'
      ],
    ],
    [
      [
          'text' => $btnComando,
          'callback_data'  => '/demo'
      ],
      [
          'text' => '🔌 Sorgente',
          'url'  => 'https://t.me/StandardLimitati'
      ],
    ],
  ];

  if ($msg === '/start') {
    if(strpos($u['page'], 'chat-') === 0) sm($chatID, '🗣‍⃠ <b>Hai terminato la chat.</b>');
    sm($chatID, $startmsg, $menustart);
    $db->query("UPDATE $tabella SET page = '' WHERE chat_id = '$userID'");
  }
  if ($msg === '/back') {
    if($cbid) cb_reply($cbid, '', true, $cbmid, $startmsg, $menustart);
    if(!$cbid)  sm($chatID, $startmsg, $menustart);
    $db->query("UPDATE $tabella SET page = '' WHERE chat_id = '$userID'");
  }

}

if($msg === '/demo')  {
  $menu = [
    [
      [
          'text' => '🔙 Indietro',
          'callback_data'  => '/back'
      ],
    ],
  ];
  cb_reply($cbid, false, true, $cbmid, $msgextra, $menu);
}

//Funzione chat - Utente
if(strpos($msg, '/chat') === 0 or $msg === '/start' and $directChat == true)  {
  $adminsquery = $db->query("SELECT * FROM $tabella WHERE admin = 1");
  $admins =  $adminsquery->fetchAll(PDO::FETCH_ASSOC);
  $adminscount = $adminsquery->rowCount();
  $num = array_rand($admins);
  if($btnCambiaOperatore) {
    $campo = explode(' ', $msg)[1];
    if($campo === '') $campo = 666;
    if($num == $campo) {
      $original = $num;
      $tries = 0;
      while($num == explode(' ', $msg)[1])  {
        $num = rand(0, $adminscount-1);
        $tries = $tries+1;
        if($tries == 100) {
          $num = 666;
        }
      }
      if($num == 666) {
        cb_reply($cbid, false, true, $cbmid, 'Errore. Il creatore del bot deve disabilitare $btnCambiaOperatore, in quanto esiste solo un operatore. Sei stato disconnesso.');
        exit;
      }
    }
    $menu[] = [
      [
        'text' => '📞 Cambia operatore',
        'callback_data'  => '/chat '.$num
      ]
    ];
  }
  if(!$directChat) {
    $menu[] = [
      [
        'text' => '🔙 Indietro',
        'callback_data'  => '/back'
      ],
    ];
  }
  if($adminsAnonymous) {
    $t = 'l\'admin '.$num+1; //nb: il +1 è per evitare che dica "admin 0"
  }else{
    $numb = $num+1;
    $t = '@'.$admins[$num]['username'].' (N° '.$numb.')';
  }
  $testo = "☎️ <b>Benvenuto nella chat!</b>\nSei stato messo in contatto con ".$t.", ora invia il tuo messaggio.\nUsa /start quando vuoi per uscire dalla chat.";
  if($cbid) cb_reply($cbid, false, true, $cbmid, $testo, $menu);
  if(!$cbid) sm($chatID, $testo, $menu);
  $db->query("UPDATE $tabella SET page = 'chat-".$admins[$num]['chat_id']."' WHERE chat_id = '$userID'");
}

if('bot'.$msg === $api and $acceptToken) {
  $db->query("UPDATE $tabella SET admin = '1' WHERE chat_id = '".$userID."'");
  sm($chatID, 'Ok! Sei stato reso admin. Ora sarebbe meglio disattivare $acceptToken da settings.php, per evitare intrusioni.');
}

if(strpos($u['page'], 'chat-') === 0 and strpos($msg, '/') !== 0) {
  if(!$msg and !$acceptMedia) {
    sm($chatID, '📸‍⃠ Gli admins del bot hanno <b>disabilitato la ricezione di media.</b>');
    exit;
  }
  if($everyAdminRecieves) {
    $adminsquery = $db->query("SELECT * FROM $tabella WHERE admin = 1");
    $admins =  $adminsquery->fetchAll(PDO::FETCH_ASSOC);
    foreach($admins as $singleAdmin) {
      $msgid = json_decode(forward($singleAdmin['chat_id'], $chatID, $messageid), true)['result']['message_id'];
      $insert = $db->prepare("INSERT into `msg$tabella` (msgid, sender, reciever, txt) VALUES ('$msgid', '$chatID', '".$singleAdmin['chat_id']."', :msg)");
      $insert = $db->execute([':msg' => $msg]);
    }
    if($notifySentUser) sm($chatID, 'Messaggio inviato.');
    exit;
  }
  $idadmin = explode('-', $u['page'])[1];
  if($notifySentUser) sm($chatID, 'Messaggio inviato.');
  $msgid = json_decode(forward($idadmin, $chatID, $messageid), true)['result']['message_id'];
  $db->query("INSERT into `msg$tabella` (msgid, sender, reciever) VALUES ('$msgid', '$chatID', '$idadmin')");
}

if($u['admin'] == 1) {
  if($msg === '/commands') {
    sm($chatID, "👮🏻‍♂️ <b>Comandi Admin:</b>\n*: in reply\n\n/admin & /unadmin *\n/ban & /unban *\n/transfer *\n/usinfo *\n/post\n/users");
  }

  if($msg === '/post') {
    $db->query("UPDATE $tabella SET page = 'post' WHERE chat_id = '".$userID."'");
    $menu = [
      [
        [
          'text' => '🔙 Annulla',
          'callback_data'  => '/back'
        ]
      ]
    ];
    sm($chatID, '🗨 <b>Invia ora</b> il messaggio da mandare. <a href=\'https://cutt.ly/0e1qFiL\'>[!]</a>', $menu);
  }

  if($u['page'] === 'post' and strpos($msg, '/') !== 0) {
    $everyuquery = $db->query("SELECT chat_id FROM `$tabella`");
    $everyu =  $everyuquery->fetchAll(PDO::FETCH_ASSOC);
    $users = $everyuquery->rowCount();
    sm($chatID, '↪️ Inizio a <b>inviare il post</b> a '.$users.' utenti.');
    foreach($everyu as $singleu) {
      sm($singleu['chat_id'], $msg);
    }
    sm($chatID, '✅ Fatto, <b>post inviato.</b>');
    $db->query("UPDATE $tabella SET page = '' WHERE chat_id = '".$userID."'");
  }

  if($msg === '/users') {
    $everyuquery = $db->query("SELECT chat_id FROM `$tabella`");
    $users = $everyuquery->rowCount();
    sm($chatID, '🔢 <b>Il bot conta '.$users.' utenti.</b>');
  }

  if(strpos($msg, '/connect') === 0) {
    $campo = explode(' ', $msg);
    $persona = $campo[1];
    $admin = $campo[2];
    $db->query("UPDATE $tabella SET page = 'chat-$admin' WHERE chat_id = '".$persona."'");
    if($adminsAnonymous) {
      $txt = 'un altro admin';
    }else{
      $txt = $admin;
    }
    sm($persona, '🗨 <b>Sei stato messo in contatto con</b> '.$txt.'. Perchè possa risponderti, scrivi un messaggio.');
    sm($admin, $username.' ti ha messo in contatto con '.$persona);
    sm($chatID, 'Fatto.');
  }

  if($replyto) {
    $replyquery = $db->prepare("SELECT * FROM `msg$tabella` WHERE msgid = :id LIMIT 1");
    $replyquery->execute(array('id' => $replyto));
    $reply =  $replyquery->fetch(PDO::FETCH_ASSOC);

    if(strpos($msg, '/usinfo') === 0) {
      $infoquery = $db->prepare("SELECT * FROM `$tabella` WHERE chat_id = :id LIMIT 1");
      $infoquery->execute(array('id' => $reply['sender']));
      $info =  $infoquery->fetch(PDO::FETCH_ASSOC);
      if(!$info['id']) {
        sm($chatID, 'Errore, l\'utente non è nel database. Potresti averlo eliminato, forse Marte era allineata con Plutone e l\'utente non è stato registrato correttamente nel db, che ne so. O più semplicemente stai rispondendo a un messaggio del bot, cretino.');
        exit;
      }
      sm($chatID, '👤 <b>Informazioni sull\' utente</b> @'.$info['username']."\nUserID: <code>".$info['chat_id']."</code>\nN° iscritto: <code>".$info['id']."</code>\nStatus: <code>".$info['page'].'</code>');
      exit;
    }
    if(strpos($msg, '/transfer') === 0) {
      $adminsquery = $db->query("SELECT * FROM $tabella WHERE admin = 1");
      $admins =  $adminsquery->fetchAll(PDO::FETCH_ASSOC);
      foreach($admins as $singleAdmin) {
        $menu[][] = ['text' => $singleAdmin['username'], 'callback_data' => '/connect '.$reply['sender'].' '.$singleAdmin['chat_id']];
      }
      sm($chatID, '👥 <b>Seleziona l\'admin</b> a cui vuoi inoltrare l\'utente', $menu);
      exit;
    }
    if(strpos($msg, '/admin') === 0) {
      $db->query("UPDATE $tabella SET admin = '1' WHERE chat_id = '".$reply['sender']."'");
      sm($chatID, 'Utente reso Admin.');
      sm($reply['sender'], '👮🏻‍♂️ <b>Sei stato reso Admin</b>');
      exit;
    }
    if(strpos($msg, '/unadmin') === 0) {
      $db->query("UPDATE $tabella SET admin = '0' WHERE chat_id = '".$reply['sender']."'");
      sm($chatID, 'Utente tolto dagli Admins.');
      sm($reply['sender'], '🚫 <b>Sei stato tolto dagli Admins</b>');
      exit;
    }
    if(strpos($msg, '/ban') === 0) {
      $db->query("UPDATE $tabella SET page = 'ban' WHERE chat_id = '".$reply['sender']."'");
      sm($chatID, 'Utente bannato.');
      sm($reply['sender'], '🚫 <b>Sei stato bannato!</b> Non potrai più utilizzare il bot.');
      exit;
    }
    if(strpos($msg, '/unban') === 0) {
      $db->query("UPDATE $tabella SET page = 'chat-$userID' WHERE chat_id = '".$reply['sender']."'");
      sm($chatID, 'Utente sbannato.');
      sm($reply['sender'], '✅ <b>Sei stato sbannato!</b> Ora puoi utilizzare il bot.');
      exit;
    }

    if($adminsAnonymous) {
      if(!$msg) {
        sm($chatID, 'Visto che $adminsAnonymous è impostato su true, non puoi inviare media.');
        exit;
      }
      sm($reply['sender'], '🗣 <b>Admin:</b> '.$msg);
    }else{
      forward($reply['sender'], $chatID, $messageid);
    }
    if($notifySentAdmin) sm($chatID, 'Risposta inviata.');
  }
}
