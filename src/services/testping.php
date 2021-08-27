<?
	$comando = "ping 127.0.0.1";
	$output = shell_exec($comando);
	echo nl2br($output);
?>