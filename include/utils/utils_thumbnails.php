<?php
function getSmallThumbnail($image) {
	return getThumbnail($image);
}
function getMediumThumbnail($image) {
	return getThumbnail($image, 'medium');
}
function getLargeThumbnail($image) {
	return getThumbnail($image, 'large');
}
function getThumbnail($image, $type='small') {
	$pathInfo = pathinfo($image);
	if (is_array($pathInfo) && isset($pathInfo['dirname'])) {
		return $pathInfo['dirname'].'/'.$type.'/'.$pathInfo['basename'];
	}
	return $image;
}
function createThumbnail($path, $type) {
	$include = array('small', 'medium', 'large');
	if (!in_array($type, $include)) return;
	$pathInfo = pathinfo($path);

	switch($type) {
		case 'large':
			$w = 760;
			$h = 376;
			break;
		case 'medium':
			$w = 175;
			$h = 87;
			break;
		default:
			$w = 100;
			$h = 49;
			break;
	}
	$resize = new utils_image_resize($path);
	$resize->size_auto($h, $w);
	$filename = substr($pathInfo['basename'], 0, strrpos($pathInfo['basename'], '.'));
	$resize->save($pathInfo['dirname']."/$type/".$filename);
}
function deleteThumbnail($path, $type) {
	$include = array('small', 'medium', 'large');
	if (!in_array($type, $include)) return;
	$pathInfo = pathinfo($path);
	$target = $pathInfo['dirname']."/$type/".$pathInfo['basename'];
	if (is_file($target)) unlink($target);
}