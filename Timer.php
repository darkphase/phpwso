#!/D/App/PHP/PHP32.7.3.0-dev/php.exe
<?php
$wso = new COM("Scripting.WindowSystemObject");
$POSITION_NONE = $wso->translate("POSITION_NONE");

$form = $wso->createForm();
$form->clientWidth = 150;
$form->clientHeight = 150;
$form->centerControl();

$a = 3.14/2;
$b = 3.14/2;

$largeCircle = $form->circle(0, 0, 10);
$smallCircle = $form->circle(0, 0, 5);
$smallCircle->color = 0x000000FF;

$timer = $wso->createTimer();

$TTA = new class {
	function onExecute(){
		global $POSITION_NONE, $largeCircle, $smallCircle;
		$a = $a + 0.6;
		$largeCircle->setBounds(50 * cos($a) + 75, 50 * sin($a) + 75, $POSITION_NONE, $POSITION_NONE);
		$b = $b + 1.1;
		$smallCircle->setBounds(50 * cos($b) + 75, 50 * sin($b) + 75, $POSITION_NONE, $POSITION_NONE);	
	}
};

com_event_sink( $timer, $TTA, "ITimerEvents" );

$timer->interval = 100;
$timer->active = true;

$form->show();

$wso->run();
?>