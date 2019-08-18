<?php

echo "StandardLimitati by <a href='htpps://t.me/NetworkCiano'>NetworkCiano</a> - Versione 3.0.0";

if($chatID < 0) exit; //Evita che il bot sia utilizzato nei gruppi
$api = $_GET["apikey"];
$update = json_decode(file_get_contents("php://input"), true);
require_once "http.php";
require_once "functions.php";


//Unisciti al canale @StandardLimitati per rimanere aggiornato sulle ultime novità della base!
//IMPORTANTE: Leggere il readme

//IMPOSTAZIONI
//Inserisci nel primo array gli UserID degli admin, nel secondo gli username. Mantieni ordine verticale.
$adminID = [123456789, 000000000, 111111111];
$adminUsername = ['durov', 'despacito', 'aaaaa'];

$wdb = 'nomedb'; //nome database
$dbuser = 'root'; //username database
$dbpass = 'password'; //password database
$tabella = 'limitati'; //nome tabella (no caratteri speciali)
$host = 'localhost:3306'; //host del db - default: localhost o localhost:3306

$startmsg = "⛓ <b>Benvenuto nel Limitati Bot di</b> @demo<b>!</b>"; //Inserisci un messaggio che sarà visualizzato allo /start
$msgextra = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam blandit. "; //Messaggio del comando personale /demo
//Menù start
$menustart = [
  [
    [
        "text" => "☎️ Avvia Chat",
        "callback_data"  => "/chat"
    ],
  ],
  [
    [
        "text" => "❓ Sottomenù Demo",
        "callback_data"  => "/demo"
    ],
    [
        "text" => "🔌 Codice sorgente",
        "url"  => "https://github.com/cianoscatolo/standardlimitati"
    ],
  ],
];
//Fine impostazioni

//Connessione al Database
$db = new PDO("mysql:host=" . $host . ";dbname=".$wdb, $dbuser, $dbpass);
$db->exec('SET NAMES utf8mb4');
$uquery = $db->prepare("SELECT * FROM $tabella WHERE chat_id = :id LIMIT 1");
$uquery->execute(array('id' => $userID));
$u =  $uquery->fetch(PDO::FETCH_ASSOC);

//Creazione tabella al primo avvio
if(!$chatID)  {
  echo "\n".'Avvio tabella...';
  $db->query('CREATE TABLE IF NOT EXISTS '.$tabella.' (
  id int(0) AUTO_INCREMENT,
  chat_id bigint(0),
  username varchar(200),
  page varchar(200),
  PRIMARY KEY (id))');
  $arr = $db->errorInfo();
  $error = print_r($arr, true);
  echo "\n".'tabella: '.$error;
  $db->query('CREATE TABLE IF NOT EXISTS '.'msg'.$tabella.' (
  id int(0) AUTO_INCREMENT,
  msgid int(0),
  sender int(0),
  reciever int(0),
  txt text(0),
  PRIMARY KEY (id))');
  $arr = $db->errorInfo();
  $error = print_r($arr, true);
  echo "\n".'tabella2: '.$error;
}

//Se non sei nel DB, vieni inserito
if(!$u['id']) $db->query("insert into `$tabella` (chat_id, page, username) values ($chatID, ''," . '"'. $username.'"'.")");

//Menu del bot
if ($msg === '/start') {
  sm($chatID, $startmsg, $menustart);
  $db->query("UPDATE $tabella SET page = '' WHERE chat_id = '$userID'");
}

if ($msg === '/back') {
    if($cbid) cb_reply($cbid, "", true, $cbmid, $startmsg, $menustart);
    if(!$cbid)  sm($chatID, $startmsg, $menustart);
    $db->query("UPDATE $tabella SET page = '' WHERE chat_id = '$userID'");
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
if($msg === '/chat')  {
  $num = array_rand($adminID);
  $menu = [
    [
      [
          "text" => "📞 Cambia operatore", //Rimuovere questo bottone se c'è solo un admin.
          "callback_data"  => "/chat"
      ],
      [
          "text" => "🔙 Indietro",
          "callback_data"  => "/back"
      ],
    ],
  ];
  cb_reply($cbid, "", true, $cbmid, "☎️ <b>Benvenuto nella chat!</b>\nSei stato messo in contatto con @".$adminUsername[$num].", ora invia il tuo messaggio.\nUsa /back quando vuoi per uscire dalla chat.", $menu);
  $db->query("UPDATE $tabella SET page = 'chat-$adminID[$num]' WHERE chat_id = '$userID'");
}

/* Decommentare per debug
if($msg === '/page')  {
  sm($chatID, $u['page']);
}*/

if(strpos($u['page'], 'chat-') === 0 and strpos($msg, '/') !== 0) {
  $idadmin = explode('-', $u['page'])[1];
  //sm($chatID, "Messaggio inviato."); //Decommentare per attivare la notifica
  $msgid = json_decode(sm($idadmin, "👤 @$username #$userID\n\n$msg"), true)['result']['message_id'];
  $db->query("insert into `msg$tabella` (msgid, sender, reciever, txt) values ('$msgid', '$chatID', '$idadmin', '$msg')");
}

if(in_array($userID, $adminID)) {
  if($replyto) {
    $replyquery = $db->prepare("SELECT * FROM `msg$tabella` WHERE msgid = :id LIMIT 1");
    $replyquery->execute(array('id' => $replyto));
    $reply =  $replyquery->fetch(PDO::FETCH_ASSOC);
    sm($reply['sender'], "🗣 <b>Risposta di $username</b>\n\n$msg");
    sm($chatID, "Risposta inviata.");
  }
}
