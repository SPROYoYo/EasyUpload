<?php
include 'EasyUpload.php';

EasyUpload::$destination = 'uploads/images'; /* path */
EasyUpload::$extensions = ['.jpg', '.jpeg', '.png', '.bmp']; /* allowed extensions list */
EasyUpload::$maxMemory = 2.5; /* set max file size to be allowed in MB */
EasyUpload::$resize = [
	["uploads/images-200x200", 200, 200],
	["uploads/images-300x300", 300, 300],
	["uploads/images-500x500", 500, 500]
];


if(EasyUpload::upload('UploadFile')) {
	echo EasyUpload::$file;
}
else {
	echo EasyUpload::$error;
}
?>
