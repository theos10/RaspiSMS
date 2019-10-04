	<footer class="text-center">
		RaspiSMS licence GNU GPL - Copyright 2014 by <a href="http://raspbian-france.fr">Raspbian-France</a><br/>
	</footer>
	<?php if (RASPISMS_SETTINGS_SMS_RECEPTION_SOUND) { ?>
		<audio id="reception-sound">
			<source src="<?php echo HTTP_PWD; ?>sounds/receptionSound.ogg" type="audio/ogg">
			<source src="<?php echo HTTP_PWD; ?>sounds/receptionSound.mp3" type="audio/mpeg">
			<source src="<?php echo HTTP_PWD; ?>sounds/receptionSound.wav" type="audio/wav">
		</audio>
	<?php } ?>
	</body>
</html>
