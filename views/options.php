<h1><?php _e('QR PDF Options', 'qr-pdf'); ?></h1>

<form method="post" id="qr-pdf-form">
	<?php _e('QR code width (in mm):', 'qr-pdf'); ?>
	<input type="text" name="size" value="<?php echo $size; ?>">
	<br>
	<?php _e('Filename:', 'qr-pdf'); ?>
	<input type="text" name="filename"
		value="<?php echo htmlspecialchars($filename); ?>">.pdf
	<br>
	<input type="submit" value="<?php _e('Save', 'qr-pdf'); ?>">
</form>
