#!/D/App/PHP/PHP32.7.3.0-dev/php.exe 
<?php

function findRegServerPath(){
	return trim(@system("where regsvr32.exe"));
	// $shell->Exec( "where", "regsvr32.exe", "", "open", 0 );
}

function register(){
	$UAC = new COM("Shell.Application"); 
	$UAC->ShellExecute( findRegServerPath(), "/s ".realpath("bin/WSO64.dll"), "", "runas", 1 );	
}

function unregister(){
	$UAC = new COM("Shell.Application"); 
	$UAC->ShellExecute( findRegServerPath(), "/u /s ".realpath("bin/WSO64.dll"), "", "runas", 1 );	
}

// function run(){
// 	$UAC = new COM("Shell.Application"); 
// 	$UAC->ShellExecute( "Timer.js", "", "", "open", 1 );		
// }

register();
// wsotest1();
// sleep(3);
// unregister();

?>