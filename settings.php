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

         __   __   __  ___      __    __             __   ___       ___  __
|  |\/| |__) /  \ /__`  |   /\   / | /  \ |\ | |    / _` |__  |\ | |__  |__)  /\  |    |
|  |  | |    \__/ .__/  |  /~~\ /_ | \__/ | \| |    \__> |___ | \| |___ |  \ /~~\ |___ |
Modifica le impostazioni a tuo piacimento. Per più informazioni, leggi le docs.

*/

//ADMINS - DA MODIFICARE
//Docs: https://cutt.ly/we1rw1u
$adminsAnonymous = false; //true per nascondere l'username degli Admins
$everyAdminRecieves = false; //true per far si che ogni admin riceva i messaggi di ogni utente
$acceptToken = false; //true per far si che chi invia il token al bot diventa admin in automatico. Consigliato solo per il primo admin se non si vuole far casini con PMA.

//DATABASE - DA MODIFICARE
$INSTALLAZIONE = false; //true per abilitare l'installazione. Disabilitare appena possibile (leggere le docs)
$wdb = 'bots'; //nome database
$dbuser = ''; //username database
$dbpass = ''; //password database
$tabella = 'limitati'; //nome tabella da scegliere ora (No caratteri strani)
$host = 'localhost:3306'; //host del db - default: localhost o localhost:3306

//CHAT - MODIFICA OPZIONALE
$directChat = false; //true per avviare la chat appena dai /start
$notifySentUser = false; //true per abilitare la notifica "Messaggio inviato" (utente)
$notifySentAdmin = false; //true per abilitare la notifica "Messaggio inviato" (admin)
$btnCambiaOperatore = false; //false per disattivare il bottone "cambia operatore" - Da disattivare se si ha solo un admin.
$acceptMedia = true; //false per impedire l'invio di media - Funziona solo con admins anonymous su false.

//COMANDI - Per modificarli o aggiungerne altri, vedi index.php
$startmsg = "⛓ <b>Benvenuto nel Limitati Bot di</b> @demo<b>!</b>"; //Inserisci un messaggio che sarà visualizzato allo /start
$btnComando = "Tasto msgestra"; //Tasto che porta a $msgextra
$msgextra = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam blandit. "; //Messaggio del comando personale, accessibile nel menù /start.
$hideWebPagePreview = true; //Nascondi le anteprime link
