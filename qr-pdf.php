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

define('DEFAULT_QRCODE_SIZE', 100);
define('DEFAULT_FILENAME', 'qrcode.pdf');
define('QRPDF_PATH', dirname(__FILE__));

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
		$pdf = new TCPDF();
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->AddPage();
		$pdf->write2DBarcode($link,
				'QRCODE,H', '', '', DEFAULT_QRCODE_SIZE, DEFAULT_QRCODE_SIZE);
		$pdf->Output(DEFAULT_FILENAME);
		die;
	}
}

?>
