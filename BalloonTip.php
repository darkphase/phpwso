#!/D/App/PHP/PHP32.7.3.0-dev/php.exe
<?php

$o = new COM("Scripting.WindowSystemObject");
$o->EnableVisualStyles = true;

$f = $o->CreateForm(0,0,0,0);

$f->ClientWidth = 500;
$f->ClientHeight = 200;
$f->CenterControl();

$f->TextOut(10,10,"Note. Themes must be enabled");
$Edit = $f->CreateEdit(10,30,480,25);

$f->Show();

$BalloonTip = $Edit->BalloonTip;
$BalloonTip->Title = "Text";
$BalloonTip->Text = "Enter text where";
$BalloonTip->Icon = 4;
$BalloonTip->Visible = true;

$Button = $f->CreateButton(10,120,75,25,"Close");

$SINK = new class{
	function onClick($sender){
		$sender->Form->Close();
	}
};

com_event_sink($Button, $SINK, "IControlEvents" );

$o->Run();

?>