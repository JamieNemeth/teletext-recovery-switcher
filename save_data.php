<?php
	$data = file_exists("data.json") ? json_decode(file_get_contents("data.json"), true) : array();
	if (isset($_POST["username"])) $data["username"] = trim($_POST["username"]);
	if (isset($_POST["recoveriesFolder"])) $data["recoveriesFolder"] = trim($_POST["recoveriesFolder"]);
	if (isset($_POST["runningRecovery"])) $data["runningRecovery"] = trim($_POST["runningRecovery"]);
	
	file_put_contents("data.json", json_encode($data));
?>