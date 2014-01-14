<form method="post" id="qr-pdf-form">
	<input type="hidden" name="qr-pdf-post" value="<?php echo $post->ID; ?>">
	<input type="submit"
		value="<?php _e('Generate QR Code with link', 'qr-pdf'); ?>">
</form>
