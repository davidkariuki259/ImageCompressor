<?php
/*
function to compress uploaded images: ie- reduces size of jpeg image, and in the case of png, convert to jpeg (while preserving alpha channel) then compress accrodingly
*/

function compressImage($source, $destination, $quality) {

  $info = getimagesize($source);

  if ($info['mime'] == 'image/jpeg') 
    $image = imagecreatefromjpeg($source);

  /*elseif ($info['mime'] == 'image/gif') 
    $image = imagecreatefromgif($source);
	*/

  elseif ($info['mime'] == 'image/png') 
    $image = imagecreatefrompng($source);
	

  	//preserve image orientation/rotation
	@$exif = exif_read_data($source);	//suppress warning that comes with png (or any image without exif data) regarding exif_read_data support
	if ($image && $exif && isset($exif['Orientation']))
	{
		$ort = $exif['Orientation'];

		if ($ort == 6 || $ort == 5)
			$image = imagerotate($image, 270, null);
		if ($ort == 3 || $ort == 4)
			$image = imagerotate($image, 180, null);
		if ($ort == 8 || $ort == 7)
			$image = imagerotate($image, 90, null);

		if ($ort == 5 || $ort == 4 || $ort == 7)
			imageflip($image, image_FLIP_HORIZONTAL);
	}
	if ($info['mime'] == 'image/jpeg'){	//save jpeg as jpeg
		imagejpeg($image, $destination, $quality);
		imagedestroy($image);
	}
	
	if ($info['mime'] == 'image/png'){	//save png as jpg
	//code derived from accepted answer to https://stackoverflow.com/questions/1201798/use-php-to-convert-png-to-jpg-with-compression
	$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
	imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
	imagealphablending($bg, TRUE);
	imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
	imagedestroy($image);
	$quality = 50; // 0 = worst / smaller file, 100 = better / bigger file 
	imagejpeg($bg, $destination, $quality);
	imagedestroy($bg);
	//end of code derivation
	
	//if conversion to jpeg is not required/desired, then the corresponding method of saving the image is: imagepng($image, $destination, $quality). Note that the quality attribute is inverted wrt the "imagejpeg" quality attribute.
	}
  

}

?>