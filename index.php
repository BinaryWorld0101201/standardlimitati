<?php

//Standard Limitati Bot by cyanbox.netsons.org
//Hai bisogno di aiuto? @cianoscatolo_bot

/*ISTRUZIONI: 
1) Modifica le impostazioni sottostanti
2) Imposta il Webhook aprendo nel browser il seguente link: https://api.telegram.org/botTUABOTKEY/setwebhook?url=https://SITO/CARTELLA/index.php?apikey=botTUABOTKEY
3) Avvia il bot su Telegram: Fatto!*/

/*Impostazioni*/
$adminid = "123456"; //Inserisci qua il tuo User ID. Puoi ottenerlo con @usinfobot
$startmsg = "Benvenuto nel mio <b>Limitati Bot!</b>
Creato con <a href='https://github.com/cyanboxdev/standardlimitati/'>StandardLimitati</a>"; //Inserisci un messaggio che sarà visualizzato allo /start
/*Fine Impostazioni*/

$api = $_GET['apikey'];
include ("http.php");
global $api;
$content = file_get_contents('php://input');
$update = json_decode($content, true); 
$userID = $update[message][from][id];
$msg = $update[message][text];
$chatID = $update[message][chat][id];
$messageid = $update[message][message_id];
$replyforward = $update[message][reply_to_message][forward_from];
$rfid = $update[message][reply_to_message][forward_from][id];
include ("functions.php");
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
