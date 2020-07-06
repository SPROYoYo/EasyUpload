<?php

class EasyUpload {
	
	public static $destination;
	public static $extensions;
	public static $maxMemory;
	public static $file;
	public static $error;
	public static $resize;
	
	private static $status;
	private static $reply;
	
	private static function formatSize($size) {
		return $size * (1024*1024);
	}
	
	private static function formatBytes($size) {
		$size = $size * (1024*1024);
		$base = log($size) / log(1024);
		$suffix = array("Bytes", "Kilobytes", "Megabytes", "Gigabytes", "Terabytes")[floor($base)];
		return pow(1024, $base - floor($base)) . $suffix;
	}
	
	public static function resize($image, $w, $h, $saveto, $newfilename) {
		$crop=FALSE;
		list($width, $height) = getimagesize($image);
		$r = $width / $height;
		if ($crop) {
			if ($width > $height) {
				$width = ceil($width-($width*abs($r-$w/$h)));
			} else {
			$height = ceil($height-($height*abs($r-$w/$h)));
			}
			$newwidth = $w;
			$newheight = $h;
		}
		else {
			if ($w/$h > $r) {
				$newwidth = $h*$r;
				$newheight = $h;
			}
			else {
				$newheight = $w/$r;
				$newwidth = $w;
			}
		}
		$src = imagecreatefromjpeg($image);
		$dst = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		imagejpeg($dst, $saveto.'/'.$newfilename);
	}
	
	public static function upload($FILE) {
		if(isSet($_FILES[$FILE])) {
			$fileupl = $_FILES[$FILE];
			$folder = self::$destination;
			$filename = $fileupl["name"];
			$basename = substr($filename, 0, strripos($filename, '.'));
			$basename_md5 = MD5($basename);
			$extension = strtolower(substr($filename, strripos($filename, '.')));
			$memory = $fileupl["size"];
			if(empty($filename)) {
				self::$status = false;
				self::$error = 'Nu s-a selectat niciun fisier!';
			}
			else if(!in_array($extension,self::$extensions)) {
				self::$status = false;
				self::$error = 'Fisierul selectat nu este suportat. Sunt permise doar fisierele cu extensiile: '.implode(',',self::$extensions);
				unlink($fileupl["tmp_name"]);
			}
			else if($memory > self::formatSize(self::$maxMemory)) {
				self::$status = false;
				self::$error = 'Fisierul selectat este prea mare. Limita este de '.self::formatBytes(self::$maxMemory).'!';
				unlink($fileupl["tmp_name"]);
			}
			else {
				$newfilename = time().MD5($basename).$extension;
				if(file_exists($folder.'/'.$newfilename)) {
					self::$status = false;
					self::$error = 'Exista deja un fisier uploadat cu acest nume.';
					unlink($fileupl["tmp_name"]);
				}
				else {
					move_uploaded_file($fileupl["tmp_name"], $folder.'/'.$newfilename);
					for($i=0;$i<count(self::$resize);$i++) {
						self::resize($folder.'/'.$newfilename, self::$resize[$i][1], self::$resize[$i][2], self::$resize[$i][0], $newfilename);
					}
					self::$status = true;
					self::$file = $newfilename;
				}
			}
		}
		else {
			$status = false;
			self::$error = 'Nu s-au primit date.';
		}
		return self::$status;
	}
	
}
?>
