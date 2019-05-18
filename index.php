<?php

echo "StandardLimitati by cianoscatolo.it - Versione 2.5.0";

$api = $_GET['apikey'];
include ("http.php");
global $api;
$content = file_get_contents('php://input');
$getm0 = file_get_contents("https://api.telegram.org/$api/getMe");
$getme = json_decode($getm0, true);
$botusername = $getme[result][username];

//Unisciti al canale @StandardLimitati per rimanere aggiornato sulle ultime novità della base!

/*ISTRUZIONI: 
1) Modifica le impostazioni sottostanti
2) Imposta il Webhook aprendo nel browser il seguente link: https://api.telegram.org/botTUABOTKEY/setwebhook?url=https://SITO/CARTELLA/index.php?apikey=botTUABOTKEY
3) Avvia il bot su Telegram: Fatto!

FUNZIONI ADMIN:

/ban USERNAME/USERID
Rimpiazza USERID con l'ID della persona da bannare o con l'Username della persona da bannare SENZA @. ATTENZIONE: CASE SENSITIVE!
Copia l'username dal profilo della persona da bannare per essere sicuro di non sbagliare maiuscole e che il ban venga effettuato correttamente.
ESEMPIO 1: /ban 123456
ESEMPIO 2: /ban cianoscatolo [da notare la mancanza della @]

Per sbannare, vieni nella cartella del bot, apri ban.txt e rimuovi manualmente l'ID o l'username della persona da sbannare.

Per vedere la lista dei bannati, usa /banlist

NOTA IMPORTANTISSIMA: Il supporto alla privacy inoltro non è ancora completamente pronto. Adesso gli utenti ricevono una notifica che li informa di questo, ma in un futuro molto prossimo la funzione sarà implementata correttamente. Entra in @StandardLimitati per seguire gli aggiornamenti.
*/

//Inizio impostazioni

$adminid = "611483250"; //Inserisci qua il tuo User ID. Puoi ottenerlo con @usinfobot
$adminusername = "CianoScatolo"; //Username senza @
$startmsg = "⛓ <b>Benvenuto nel Limitati Bot di</b> @$adminusername<b>!</b>
Sai come funziona: invia testo o media, sarà recapitato a $adminusername, che potrà risponderti come se steste parlando in chat privata."; //Inserisci un messaggio che sarà visualizzato allo /start
//Menù start
$menustart[] = array(
array(
"text" => "👤 Privata",
"url" => "https://t.me/$adminusername"),
array(
"text" => "🔌 Codice sorgente",
"url" => "https://github.com/cianoscatolo/standardlimitati"),
);

//Fine impostazioni

$update = json_decode($content, true); 
$userID = $update[message][from][id];
$msg = $update[message][text];
$chatID = $update[message][chat][id];
$username = $update[message][from][username];
$messageid = $update[message][message_id];
$replying = $update[message][reply_to_message];
$replyforward = $update[message][reply_to_message][forward_from];
$replytoid = $update[message][reply_to_message][from][id];
$rfid = $update[message][reply_to_message][forward_from][id];
include ("functions.php");

//funzione BAN
$banlist = file_get_contents('ban.txt');
$ban = explode("\n", $banlist);

if(in_array($chatID, $ban) || in_array($username, $ban) and $username != "")
{
sm($chatID, "Sei bannato dal bot! Il messaggio non è stato inviato.");
exit();
}

if($chatID < 0)
{
exit(); //Evita che il bot sia utilizzato nei gruppi
}

//Start bot
if($msg == "/start")
{
sm($chatID, $startmsg, $menustart);
}

if($msg == "/back")
{
cb_reply($cbid, "", true, $cbmid, $startmsg, $menustart);
}
//Chat Function
if($userID != $adminid)
{
	$var = array(
		'chat_id' => $adminid,
		'from_chat_id' => $chatID,
		'message_id' => $messageid);
	$richiesta = new HttpRequest("get", "https://api.telegram.org/$api/forwardMessage", $var);
	$response = $richiesta->getResponse();
	$jsdec = json_decode($response, true);

	if(!$jsdec[result][forward_from][id])
	{
		sm($chatID, "Sembra che abbia la privacy inoltro attivata!\nL'admin del bot non potrà rispondere ai tuoi messaggi."); //Supporto intero nella prox versione
	}

}

if($replying and $userID == $adminid)
{
	if(!$rfid)
	{
		sm($chatID, "Impossibile inviare, l'utente ha la privacy inoltro."); //Supporto intero nella prox versione
		exit();
	}

	sm($rfid, $msg);
	sm($chatID, "Inviato.");
}

//Funzione ban
if($msg == "/banlist")
{
$list = file_get_contents("ban.txt");
if(!$list)	$list = "Nessun utente bannato.";
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