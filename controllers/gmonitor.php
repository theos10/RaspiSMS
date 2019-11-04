<?php
// Back-end ececutant la commande shell
	require_once('../mvc/constants.php');
	//NetworkSignal
	echo shell_exec(CMD_SIGNAL);
?>
