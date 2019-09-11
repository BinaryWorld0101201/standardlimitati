<?php

/*
           _____ _                  _               _ _      _           _ _        _   _
    ____  / ____| |                | |             | | |    (_)         (_) |      | | (_) V4.0.0
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
//Docs: https://github.com/cianoscatolo/standardlimitati/wiki/3)-settings.php#admins
$adminID = [851611473, 851611473, 851611473]; //Mantenere la corrispondenza verticale
$adminUsername = ['cianoscatolo', 'despacito', 'aaaaa']; //Mantenere la corrispondenza verticale
$adminsAnonymous = false; //true per nascondere l'username degli Admins

//DATABASE - DA MODIFICARE
$INSTALLAZIONE = false; //true per abilitare l'installazione. Disabilitare appena possibile (leggere le docs)
$wdb = 'nomedb'; //nome database
$dbuser = 'usernamedb'; //username database
$dbpass = 'passworddb'; //password database
$tabella = 'limitati'; //nome tabella da scegliere ora (No caratteri strani)
$host = 'localhost:3306'; //host del db - default: localhost o localhost:3306

//CHAT - MODIFICA OPZIONALE
$directChat = false; //true per avviare la chat appena dai /start
$notifySentUser = false; //true per abilitare la notifica "Messaggio inviato" (utente)
$notifySentAdmin = false; //true per abilitare la notifica "Messaggio inviato" (admin)
$btnCambiaOperatore = true; //false per disattivare il bottone "cambia operatore"
$everyAdminRecieves = false; //true per far si che ogni admin riceva i messaggi di ogni utente

//COMANDI - Per modificarli o aggiungerne altri, vedi index.php
$startmsg = "⛓ <b>Benvenuto nel Limitati Bot di</b> @demo<b>!</b>"; //Inserisci un messaggio che sarà visualizzato allo /start
$btnComando = "Tasto msgestra"; //Tasto che porta a $msgextra
$msgextra = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam blandit. "; //Messaggio del comando personale, accessibile nel menù /start.
