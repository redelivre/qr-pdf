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

add_action('add_meta_boxes', function()
{
	foreach (get_post_types() as $type)
	{
		add_meta_box('qr-pdf-meta-box', 'QR PDF', 'qrpdfAddMetaBox', $type,
			'side');
	}
});

add_action('init', function()
{
	if (array_key_exists('qr-pdf-post', $_POST))
	{
		qrpdfGeneratePDF($_POST['qr-pdf-post']);
	}
});

add_action('admin_menu', function()
{
	add_options_page(__('QR PDF Configuration', 'qr-pdf'), __('QR PDF'),
		'manage-options', 'qr-pdf', 'qrpdfOptions');
});

function qrpdfAddMetaBox()
{
	global $post;

	require QRPDF_PATH . DIRECTORY_SEPARATOR . 'views'
		. DIRECTORY_SEPARATOR . 'metabox.php';
}

function qrpdfGeneratePDF($post)
{
	require_once 'tcpdf/tcpdf.php';

	$link = get_permalink($post);
	if ($link !== false)
	{
		$size = get_option('qr-pdf-size', QRPDF_QRCODE_SIZE);
		$filename = get_option('qr-pdf-filename', QRPDF_FILENAME) . '.pdf';
		$format = get_option('qr-pdf-format', QRPDF_FORMAT);

		/* QR Codes are squares, orientation doesn't matter */
		$pdf = new TCPDF('P', 'mm', $format, false, 'ISO-8859-1');
		$pdf->setFontSubsetting(false);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->AddPage();
		$pdf->write2DBarcode($link, 'QRCODE,H', '', '', $size, $size);
		$pdf->Output($filename);
		die;
	}
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
