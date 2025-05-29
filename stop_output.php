<?php
	$_POST["runningService"] = "";
	include "save_data.php";
	
	exec("pidof vbit2", $vbit2Pids);
	exec("pidof teletext", $teletextPids);
	
	if (count($vbit2Pids) > 0) 
	{
		foreach ($vbit2Pids as $vbit2Pid)
		{
			exec("sudo kill -9 " . $vbit2Pid);
		}
	}
	
	if (count($teletextPids) > 0)
	{
		foreach ($teletextPids as $teletextPid)
		{
			exec("sudo kill -9 " . $teletextPid);
		}
	}
	
	exec('sudo /home/' . $data["username"] . '/raspi-teletext/./tvctl off');
	exec('sudo rm /etc/cron.d/teletext-service-switcher || true');
?>