<?php
$key = "a"; # needs to be set here and in the plugin config file

$dir = "ResourcePacks/";
if (!file_exists($dir)) mkdir($dir);

$file = "";

function isZip($zip) {
	$pointer = @fopen($zip, "r");

	if ($pointer) {
		$signature = fgets($pointer, 5);
		fclose($pointer);
		
		return strpos($signature, 'PK') !== false;
	}
	return false;
}

function isValidName($name) {
	$pattern = "/^([0-9a-z]{1,})$/";
	return preg_match($pattern, $name);
}

function generateRandomString() {
	return hash("sha512", time() . rand(0, 1000));
}

if (isset($_POST["key"]) && is_string($_POST["key"]) && $_POST["key"] === $key) { # check if key is valid
	if (isset($_FILES["file"]) && isZip($_FILES["file"]["tmp_name"])) { # check if zip is uploaded
		$file = $dir . generateRandomString() . ".zip"; # create name for uploaded file
		if (move_uploaded_file($_FILES["file"]["tmp_name"], $file)) { # save file in directory
			$status = "ok";
		} else $status = "error";
	} else if (isset($_POST["rmFile"]) && is_string($_POST["rmFile"]) && isValidName($_POST["rmFile"])) { # check if request has correct remove file parameters
		$file = $dir . basename($_POST["rmFile"]) . ".zip"; # get actual file name
		if (file_exists($file)) { # check if file exists
			unlink($file); # delete file
			$status = "ok";
		} else $status = "file not found";
	} else $status = "invalid request";
} else $status = "invalid request";

echo json_encode(array("status" => $status, "file" => $file));
?>
