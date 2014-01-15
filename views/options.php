<h1><?php _e('QR PDF Options', 'qr-pdf'); ?></h1>

<form method="post" id="qr-pdf-form">
	<span><?php _e('QR code width (in mm):', 'qr-pdf'); ?></span>
	<input type="text" name="size" value="<?php echo $size; ?>">
	<br>
	<span><?php _e('Filename:', 'qr-pdf'); ?></span>
	<input type="text" name="filename"
		value="<?php echo htmlspecialchars($filename); ?>">.pdf
	<br>
	<span><?php _e('Page format:', 'qr-pdf'); ?></span>
	<select name="format">
		<?php
			ksort($pageFormats);
			foreach ($pageFormats as $f=> $d)
			{
				echo '<option value="', htmlspecialchars($f), '"',
					($f === $format? ' selected' : ''), '>',
					htmlspecialchars($f), " ($d[0]x$d[1] mm)</option>";
			}
		?>
	</select>
	<br>
	<input type="submit" value="<?php _e('Save', 'qr-pdf'); ?>">
</form>
