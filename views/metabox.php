<form method="post" id="qr-pdf-form">
	<input type="hidden" name="qr-pdf-post"
		value="<?php echo htmlspecialchars($url); ?>">
	<input type="submit" name="qr-pdf-submit"
		value="<?php _e('Generate QR Code with link', 'qr-pdf'); ?>">
</form>
