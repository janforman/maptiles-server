<?php
// Map Tiles Service 1.0 compatible with ESRI ArcGIS Server
// Can serve exploded maptiles from directory
// define directory where script and tiles are located, expire time for caching
$expiresOffset = 3600 * 24 * 14;
$basepath = '/home/';
$mimetype = 'image/jpg';
$image = 'jpg';

function convhex($tileid)
{
	return sprintf("%08s", dechex($tileid));
}
//////////////////////////////////////////
$id = explode('MapServer', $_SERVER['REQUEST_URI']);
$service = explode('/services/', $id[0]);
$id = explode('/', $id[1]);
$layer = $id[2];
$directory = $id[3];
$file = $id[4];
if ($id[3] == '') {
	// send JSON service descriptor
	if (file_exists($basepath . $service[1] . 'json.txt')) {
		header('Cache-control: max-age=' . $expiresOffset);
		if ($_GET['callback']) echo $_GET['callback'] . "(" . readfile($basepath . $service[1] . 'json.txt') . ");";
		else readfile($basepath . $service[1] . 'json.txt');
		exit;
	} else {
		header('HTTP/1.0 403 Forbidden');
		exit('Map Service not Found / Access Denied');
	}
}
$tile = 'tiles/L' . sprintf("%02d", $layer) . '/R' . convhex($directory) . '/C' . convhex($file) . '.' . $image;

header('Cache-control: max-age=' . $expiresOffset);
header('Content-type: ' . $mimetype);

if (!@readfile($basepath . $service[1] . $tile)) {
	// image doesn't exist
	header('Cache-control: max-age=' . $expiresOffset);
	header('Content-type: image/jpeg');
	header('Content-Length: ' . (string) filesize($basepath . '404.jpg'));
	readfile($basepath . '404.jpg');
}
