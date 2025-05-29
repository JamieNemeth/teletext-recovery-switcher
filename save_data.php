<?php
	$data = file_exists("data.json") ? json_decode(file_get_contents("data.json"), true) : array();
	if (isset($_POST["username"])) $data["username"] = trim($_POST["username"]);
	if (isset($_POST["localServicesFolder"])) $data["localServicesFolder"] = trim($_POST["localServicesFolder"]);
	if (isset($_POST["runningService"])) $data["runningService"] = trim($_POST["runningService"]);
	
	file_put_contents("data.json", json_encode($data));
?>