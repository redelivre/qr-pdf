<?php

/*
Plugin Name: QR PDF
Plugin URI: http://labculturadigital.org/
Description: Generate ready-to-print-PDFs with QR codes linking to your posts
Author: Laboratório de Cultura Digital - Flávio Zavan
Version: 0.01
Text Domain: qr-pdf
*/

if (!is_admin())
{
	return;
}

define('QRPDF_QRCODE_SIZE', 100);
define('QRPDF_FILENAME', 'qrcode');
define('QRPDF_PATH', dirname(__FILE__));
define('QRPDF_FORMAT', 'A4');
define('QRPDF_GAP', 5);

add_action('add_meta_boxes', function()
{
	$types = get_post_types();
	$types['link'] = 'link';
	foreach ($types as $type)
	{
		add_meta_box('qr-pdf-meta-box', 'QR PDF', 'qrpdfAddMetaBox', $type,
			'side');
	}
});

add_action('admin_menu', function()
{
	add_options_page(__('QR PDF Configuration', 'qr-pdf'), __('QR PDF'),
		'manage-options', 'qr-pdf', 'qrpdfOptions');
});

/* The bulk action hook won't allow us to add action */
add_action('admin_footer', function()
{
	?>
		<script type="text/javascript">
			jQuery(document).ready(function()
			{
				var select = jQuery('select[name="action2"]');
				if (select.length)
				{
					var option = jQuery('<option>').appendTo(select);
					option.val('qrcode');
					option.text('<?php _e('Generate QR code', 'qr-pdf'); ?>');
				}
			});
		</script>
	<?php
});

add_action('load-edit.php', 'qrpdfHandleForms');
add_action('load-link-manager.php', 'qrpdfHandleForms');
add_action('load-link.php', 'qrpdfHandleForms');

function qrpdfHandleForms()
{
	/* Single QR code */
	if (array_key_exists('qr-pdf-post', $_POST)
		&& array_key_exists('qr-pdf-submit', $_POST))
	{
		qrpdfGeneratePDF(array($_POST['qr-pdf-post']));
	}
	/* More than one */
	else if (array_key_exists('action2', $_GET)
			&& $_GET['action2'] == 'qrcode')
	{
		$urls = array();
		if (array_key_exists('post', $_GET) && is_array($_GET['post']))
		{
			foreach ($_GET['post'] as $post)
			{
				$url = get_permalink($post);
				if ($url !== false)
				{
					$urls[] = $url;
				}
			}
		}
		if (array_key_exists('linkcheck', $_GET) && is_array($_GET['linkcheck']))
		{
			foreach ($_GET['linkcheck'] as $link)
			{
				$bookmark = get_bookmark($link);
				if ($bookmark !== null)
				{
					$urls[] = $bookmark->link_url;
				}
			}
		}
		qrpdfGeneratePDF($urls);
	}
}

function qrpdfAddMetaBox()
{
	global $post;
	global $link;

	$url = null;

	/* Posts */
	if (isset($post))
	{
		$url = get_permalink($post);
	}
	/* Links */
	else if (isset($link))
	{
		$url = $link->link_url;
	}

	require QRPDF_PATH . DIRECTORY_SEPARATOR . 'views'
		. DIRECTORY_SEPARATOR . 'metabox.php';
}

function qrpdfGeneratePDF($urls)
{
	require_once 'tcpdf/tcpdf.php';

	$size = get_option('qr-pdf-size', QRPDF_QRCODE_SIZE);
	$filename = get_option('qr-pdf-filename', QRPDF_FILENAME) . '.pdf';
	$format = get_option('qr-pdf-format', QRPDF_FORMAT);

	/* QR Codes are squares, orientation doesn't matter */
	$pdf = new TCPDF('P', 'mm', $format, false, 'ISO-8859-1');
	$pdf->setFontSubsetting(false);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

	$dimensions = $pdf->getPageDimensions();
	$margins = $pdf->getMargins();
	$width = $dimensions['wk'] - $margins['right'];
	$height = $dimensions['hk'] - $margins['bottom'];
	$x = $width;
	$y = $height;

	foreach ($urls as $url)
	{
		if ($x + $size > $width)
		{
			$y += $size + QRPDF_GAP;
			$x = $margins['left'];
		}
		if ($y + $size > $height)
		{
			$y = $margins['top'];
			$pdf->AddPage();
		}
		$pdf->write2DBarcode($url, 'QRCODE,H', $x, $y, $size, $size);
		$x += $size + QRPDF_GAP;
	}
	$pdf->Output($filename);

	die;
}

function qrpdfOptions()
{
	if (!empty($_POST))
	{
		qrpdfSaveOptions();
	}

	$size = get_option('qr-pdf-size', QRPDF_QRCODE_SIZE);
	$filename = get_option('qr-pdf-filename', QRPDF_FILENAME);
	$format = get_option('qr-pdf-format', QRPDF_FORMAT);
	require QRPDF_PATH . DIRECTORY_SEPARATOR . 'formats.php';

	require QRPDF_PATH . DIRECTORY_SEPARATOR . 'views'
		. DIRECTORY_SEPARATOR . 'options.php';
}

function qrpdfSaveOptions()
{
	require QRPDF_PATH . DIRECTORY_SEPARATOR . 'formats.php';

	$size = (array_key_exists('size', $_POST) && is_numeric($_POST['size'])?
			max((int) $_POST['size'], 1) : QRPDF_QRCODE_SIZE);
	$filename = (!empty($_POST['filename'])? $_POST['filename'] :
			QRPDF_FILENAME);
	$format = (!empty($_POST['format'])
			&& array_key_exists($_POST['format'], $pageFormats)?
			$_POST['format'] : QRPDF_FORMAT);

	update_option('qr-pdf-size', $size);
	update_option('qr-pdf-filename', $filename);
	update_option('qr-pdf-format', $format);
}

?>
