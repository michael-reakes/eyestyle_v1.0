<?
/*****************************************************************************
* Filename: utils_image_resize.php
* Copyright: 2005 S3 Group Pty Ltd (www.s3design.com.au)
* Modified from Shiege Iseng Resize Class (kentung.f2o.org/scripts/thumbnail)
*
* Sample :
* $thumb=new utils_image_resize("./photo.jpg");// set filename to resize
* $thumb->size_width(100);				// set width for thumbnail, or
* $thumb->size_height(300);				// set height for thumbnail, or
* $thumb->size_auto(200);				// set maximum width or height
* $thumb->jpeg_quality(90);				// [OPTIONAL] set quality for
*										//   jpeg only. (0 - 100) default = 90
* $thumb->show();						// show your thumbnail
* $thumb->save("./huhu.jpg");			// save your thumbnail to file
* ----------------------------------------------
* Note :
* - GD must Enabled
* - Autodetect file extension (.jpg/jpeg, .png, .gif)
* - If your GD not support 'ImageCreateTrueColor' function,
*   change one line from 'ImageCreateTrueColor' to 'ImageCreate'
*   (the position in 'show' and 'save' function)
*****************************************************************************/

class utils_image_resize {
	var $img;

	function utils_image_resize($image) {
		@$image_info = getimagesize($image);
		$this->img["width"] = $image_info[0];
		$this->img["height"] = $image_info[1];
		$this->img["width_thumb"] = $image_info[0];
		$this->img["height_thumb"] = $image_info[1];
		$this->img["type"] = $image_info[2];

		switch ($this->img["type"]) {
			case 1:
				$this->img["format"] = "gif";
				$this->img["mime"] = "image/gif";
				@$this->img["src"] = imagecreatefromgif ($image);
				break;
			case 2:
				$this->img["format"] = "jpg";
				$this->img["mime"] = "image/jpeg";
				@$this->img["src"] = imagecreatefromjpeg ($image);
				break;
			case 3:
				$this->img["format"] = "png";
				$this->img["mime"] = "image/png";
				@$this->img["src"] = imagecreatefrompng ($image);
				break;
			default:
				//unsupported format
		}

		//default settings;
		$this->jpeg_quality();
	}

	function size_height($size = 100, $keep_ratio = true) {
		//height
    	$this->img["height_thumb"] = $size;
		if ($keep_ratio) {
			@$this->img["width_thumb"] = ($this->img["height_thumb"]/$this->img["height"])*$this->img["width"];
		}
	}

	function size_width($size = 100, $keep_ratio = true) {
		//width
		$this->img["width_thumb"] = $size;
		if ($keep_ratio) {
			@$this->img["height_thumb"] = ($this->img["width_thumb"]/$this->img["width"])*$this->img["height"];
		}
	}

	function size_auto($mx_height = 100, $mx_width = 100) {
		//size
		if ($this->img["width"] >= $mx_width) {
    		$this->size_width($mx_width);
			if ($this->img["height_thumb"] > $mx_height) {
				$this->size_height($mx_height);
			}
		} elseif ($this->img["height"] > $mx_height) {
	    	$this->size_height($mx_height);
			if ($this->img["width_thumb"] > $mx_width) {
				$this->size_width($mx_width);
			}
 		}
	}

	function jpeg_quality($quality = 90) {
		//jpeg quality
		$this->img["quality"]=$quality;
	}

	function show() {
		@header("Content-Type: ".$this->img["mime"]);

		@$this->img["des"] = imagecreatetruecolor($this->img["width_thumb"],$this->img["height_thumb"]);
   		@imagecopyresampled ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["width_thumb"], $this->img["height_thumb"], $this->img["width"], $this->img["height"]);

		switch ($this->img["type"]) {
			case 1:
				return imagegif($this->img["des"]);
			case 2:
				return imagejpeg($this->img["des"], "", $this->img["quality"]);
			case 3:
				return imagepng($this->img["des"]);
			default:
				return false;
		}
	}

	function show_ds($ds_offset = 4, $ds_steps = 12, $ds_spread = 1) {
		@header("Content-Type: ".$this->img["mime"]);

		$width  = $this->img["width_thumb"] + $ds_offset;
		$height = $this->img["height_thumb"]  + $ds_offset;
		$image = imagecreatetruecolor($width, $height);

		$background = array("r" => 255, "g" => 255, "b" => 255);
		$step_offset = array("r" => ($background["r"] / $ds_steps), "g" => ($background["g"] / $ds_steps), "b" => ($background["b"] / $ds_steps));

		$current_color = $background;

		for ($i = 0; $i <= $ds_steps; $i++) {
		  $colors[$i] = imagecolorallocate($image, round($current_color["r"]), round($current_color["g"]), round($current_color["b"]));

		  $current_color["r"] -= $step_offset["r"];
		  $current_color["g"] -= $step_offset["g"];
		  $current_color["b"] -= $step_offset["b"];
		}

		imagefilledrectangle($image, 0,0, $width, $height, $colors[0]);
		
		for ($i = 0; $i < count($colors); $i++) {
		  imagefilledrectangle($image, $ds_offset, $ds_offset, $width, $height, $colors[$i]);
		  $width -= $ds_spread;
		  $height -= $ds_spread;
		}

		@$original_image = imagecreatetruecolor($this->img["width_thumb"],$this->img["height_thumb"]);
   		@imagecopyresampled ($original_image, $this->img["src"], 0, 0, 0, 0, $this->img["width_thumb"], $this->img["height_thumb"], $this->img["width"], $this->img["height"]);

		imagecopymerge($image, $original_image, 0,0,0,0, $this->img["width_thumb"], $this->img["height_thumb"], 100);

		$this->img["des"] = $image;

		switch ($this->img["type"]) {
			case 1:
				return imagegif($this->img["des"]);
			case 2:
				return imagejpeg($this->img["des"], "", $this->img["quality"]);
			case 3:
				return imagepng($this->img["des"]);
			default:
				return false;
		}
	}

	function save($filename_without_ext) {
		@$this->img["des"] = imagecreatetruecolor($this->img["width_thumb"],$this->img["height_thumb"]);
    	@imagecopyresampled ($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["width_thumb"], $this->img["height_thumb"], $this->img["width"], $this->img["height"]);

		switch ($this->img["type"]) {
			case 1:
				return imagegif($this->img["des"], $filename_without_ext.".".$this->img["format"]);
			case 2:
				return imagejpeg($this->img["des"], $filename_without_ext.".".$this->img["format"], $this->img["quality"]);
			case 3:
				return imagepng($this->img["des"], $filename_without_ext.".".$this->img["format"]);
			default:
				return false;
		}
	}
	function get_thumb_size($mx_height = 100, $mx_width = 100) {
		$this->size_auto($mx_height, $mx_width);
		return array(intval($this->img['width_thumb']), intval($this->img['height_thumb']));
	}

}
?>