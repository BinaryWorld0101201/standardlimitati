<?php

define("FILEBAN", "ban.txt");

echo "StandardLimitati by cianoscatolo.it - Versione 2.5.0";

$api = $_GET["apikey"];
$update = json_decode(file_get_contents("php://input"), true);
require_once "http.php";
require_once "functions.php";
//$botusername = json_decode(file_get_contents("https://api.telegram.org/$api/getMe"), true)["result"]["username"]; //non viene usato .-.

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
define("ADMINID", "611483250"); //Inserisci qua il tuo User ID. Puoi ottenerlo con @usinfobot
define("ADMINUSERNAME", "CianoScatolo"); //Username senza @
$startmsg = "⛓ <b>Benvenuto nel Limitati Bot di</b> @" . ADMINUSERNAME . "<b>!</b>
Sai come funziona: invia testo o media, sarà recapitato a " . ADMINUSERNAME . ", che potrà risponderti come se steste parlando in chat privata."; //Inserisci un messaggio che sarà visualizzato allo /start
//Menù start
$menustart[] = [
    [
        "text" => "👤 Privata",
        "url"  => "https://t.me/" . ADMINUSERNAME
    ],
    [
        "text" => "🔌 Codice sorgente",
        "url"  => "https://github.com/cianoscatolo/standardlimitati"
    ],
];
//Fine impostazioni


if ($chatID < 0) {
    exit(); //Evita che il bot sia utilizzato nei gruppi
}

//funzione BAN
$ban = explode("\n", file_get_contents(FILEBAN));

if (in_array($chatID, $ban) || in_array($username, $ban) and $username != "") {
    sm($chatID, "Sei bannato dal bot! Il messaggio non è stato inviato.");
    exit();
}

//Start bot
if ($msg == "/start") {
    sm($chatID, $startmsg, $menustart);
}

if ($msg == "/back") {
    cb_reply($cbid, "", true, $cbmid, $startmsg, $menustart);
}
//Chat Function
if ($userID != ADMINID) {
    $var = array(
        "chat_id"      => ADMINID,
        "from_chat_id" => $chatID,
        "message_id"   => $messageid
    );
    $richiesta = new HttpRequest("get", "https://api.telegram.org/$api/forwardMessage", $var);
    $response = json_decode($richiesta->getResponse(), true);

    if (!$response["result"]["forward_from"]["id"]) {
        sm($chatID,
            "Sembra che abbia la privacy inoltro attivata!\nL'admin del bot non potrà rispondere ai tuoi messaggi."); //Supporto intero nella prox versione
    }
}

if ($replying and $userID == ADMINID) {
    if (!$rfid) {
        sm($chatID, "Impossibile inviare, l'utente ha la privacy inoltro."); //Supporto intero nella prox versione
        exit();
    }
    sm($rfid, $msg);
    sm($chatID, "Inviato.");
}

//Funzione ban
if ($msg == "/banlist") {
    $list = file_get_contents(FILEBAN);
    if (!$list) {
        $list = "Nessun utente bannato.";
    }
    sm($chatID, "<b>Lista ban:</b>
$list", "", "HTML");
}

if (strpos($msg, "/ban ") === 0 and $chatID == ADMINID) {
    $campo = explode(" ", $msg);
    $current = file_get_contents(FILEBAN) . "$campo[1]\n";
    file_put_contents(FILEBAN, $current);
    sm($chatID, "Ho bannato l'utente $campo[1]", "", "HTML");
}