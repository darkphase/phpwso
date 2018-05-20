#!/D/App/PHP/PHP32.7.3.0-dev/php.exe
<?php

$o = new COM("Scripting.WindowSystemObject");

$f = $o->CreateForm(0,0,0,0);

$f->ClientWidth = 200;
$f->ClientHeight = 100;
$f->CenterControl();

$button = $f->CreateButton(10,10,75,25,"Test");
$button->Default = true;

$TTA = new class {
	function OnClick(){
		global $f;
		$f->MessageBox("Test");	
	}
};

// $sink = new TTA();

com_event_sink( $button, $TTA, "IControlEvents" );

// while($sink) {
//   com_message_pump(1000);
// }

$f->CreateEdit(10,40,100,25)->SetFocus();

$f->TextOut( 10, 70, "Press Enter" );

$f->Show();

$o->Run();

?>