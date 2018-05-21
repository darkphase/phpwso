#!/D/App/PHP/PHP32.7.3.0-dev/php.exe 
<?php
	include("lib.dttk.wso.php");
	include("wsoconst.php");

	$wsow 	 = new WSOWrapper();
	$wso 	 = $wsow->init();
	
	$wso->enableVisualStyles = true;
	$form = $wso->createForm();
	$form->clientWidth = 300;
	$form->ClientHeight = 100;
	$form->centerControl();
	$form->text = "WSO TEST #1";
	$form->Icon = "mmres.dll,8";

	$button = $form->CreateButton(10,10,75,25,"Test");
	$button->Default = true;

	$wsow->AttachCOMEvent( $button, "onclick", 
		function(){
			global $form;
			$form->MessageBox("Test","TEST TITLE", MB_OKCANCEL);
		} 
	);

	// $form->CreateEdit(10,40,100,25)->SetFocus();
	// $form->TextOut( 10, 70, "Press Enter" );
	

	$form->Show();
	
	$wso->run();
?>