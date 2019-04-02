<?php

//Standard Limitati Bot by cianoscatolo.it
//Hai bisogno di aiuto? @cianoscatolo_bot

/*ISTRUZIONI: 
1) Modifica le impostazioni sottostanti
2) Imposta il Webhook aprendo nel browser il seguente link: https://api.telegram.org/botTUABOTKEY/setwebhook?url=https://SITO/CARTELLA/index.php?apikey=botTUABOTKEY
3) Avvia il bot su Telegram: Fatto!*/

/*FUNZIONI ADMIN:

/ban USERNAME/USERID
Rimpiazza USERID con l'ID della persona da bannare o con l'Username della persona da bannare SENZA @. ATTENZIONE: CASE SENSITIVE!
Copia l'username dal profilo della persona da bannare per essere sicuro di non sbagliare maiuscole e che il ban venga effettuato correttamente.
ESEMPIO 1: /ban 123456
ESEMPIO 2: /ban cianoscatolo [da notare la mancanza della @]

Per sbannare, vieni nella cartella del bot, apri ban.txt e rimuovi manualmente l'ID o l'username della persona da sbannare.

Per vedere la lista dei bannati, usa /banlist*/

/*Impostazioni*/
$adminid = "123456789"; //Inserisci qua il tuo User ID. Puoi ottenerlo con @usinfobot
$startmsg = "<b>Benvenuto nel Limitati Bot di</b> @MIOUSERNAME<b>!</b>
Sai come funziona: invia testo o media, sarà recapitato a MIOUSERNAME, che potrà risponderti come se steste parlando in chat privata.
Creato con cianoscatolo.it/github"; //Inserisci un messaggio che sarà visualizzato allo /start. Al contrario di LimitatiBot o simili, questo è interamente personalizzabile, ma un riferimento a questa pagina GitHub sarebbe molto gradito!
/*Fine Impostazioni*/

$api = $_GET['apikey'];
include ("http.php");
global $api;
$content = file_get_contents('php://input');
$update = json_decode($content, true); 
$userID = $update[message][from][id];
$msg = $update[message][text];
$chatID = $update[message][chat][id];
$username = $update[message][from][username];
$messageid = $update[message][message_id];
$replyforward = $update[message][reply_to_message][forward_from];
$rfid = $update[message][reply_to_message][forward_from][id];
include ("functions.php");

//funzione BAN
$banlist = file_get_contents('ban.txt');

if(in_array($chatID, $ban))
{
sm($chatID, "Sei bannato dal bot! Il messaggio non è stato inviato.");
exit();
}

if(in_array($username, $ban) and $username != "")
{
sm($chatID, "Sei bannato dal bot! Il messaggio non è stato inviato.");
exit();	
}

//Start bot
if($msg == "/start"){
	sm($chatID, "$startmsg",'','HTML');
}
//Chat Function
if($userID != $adminid){
	$var = array(
		'chat_id' => $adminid,
		'from_chat_id' => $chatID,
		'message_id' => $messageid);
	$richiesta = new HttpRequest("get", "https://api.telegram.org/$api/forwardMessage", $var);
}
if($replyforward and $userID == $adminid){
	$id = $rfid;
	sm($id, $msg);
}

//Funzione ban
if($msg == "/banlist")
{
$list = file_get_contents("ban.txt");
sm($chatID, "<b>Lista ban:</b>
$list",'','HTML');
}

if(strpos($msg,"/ban ")===0 and $chatID == $adminid)
{
$campo = explode(" ", $msg);
$file = 'ban.txt';
$current = file_get_contents($file);
$current .= "$campo[1]\n";
file_put_contents($file, $current);
sm($chatID, "Ho bannato l'utente $campo[1]",'','HTML');
}
