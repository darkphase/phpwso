#!/D/App/PHP/PHP32.7.3.0-dev/php.exe 
<?php

$wso = new COM("Scripting.WindowSystemObject");

$wso->enableVisualStyles = true;

$form = $wso->createForm();

$form->clientWidth = 800;
$form->ClientHeight = 600;
$form->centerControl();
$form->text = "Docking Framework Example";
$panels = [];
$form->docking->uniqueId = "Form1";
$form->docking->dropTarget = true;
$form->autoSplit = true;

function createDocument($name){
    global $form, $wso, $panels;
    $doc = $form->createFrame(10, 10, 100, 100);
    $doc->text = $name;

	$edit = $doc->createEdit(0,0,0,0,$wso->translate("ES_MULTILINE"));
	$edit->align = $wso->translate("AL_CLIENT");
	$edit->text  = "Some text where";

	$doc->align = $wso->translate("AL_CLIENT");
	$doc->docking->alwaysDockTab = true;
	$doc->docking->dropTarget = true;
	$doc->docking->uniqueId = $name;
	
	$panels[$name] = $doc;
	return $doc;	
}

function createPanel($name) {
    global $form, $wso, $panels;

    $panel = $form->createFrame(10, 10, 150, 100);
    $panel->text = $name;
    $panel->align = $wso->translate("AL_LEFT");
    $panel->docking->alwaysDockPage = true;
    $panel->docking->dropTarget = true;
    $panel->docking->uniqueId = $name;

    $panels[$name] = $panel;
    return $panel;
}

function createBottomPanel($name) {
    global $form, $wso, $panels;

    $panel = $form->createFrame(10, 10, 100, 100);
    $panel->text    = $name;
    $panel->align   = $wso->translate("AL_BOTTOM");
    $panel->docking->alwaysDockPage = true;
    $panel->docking->dropTarget = true;
    $panel->docking->uniqueId = $name;

    $panels[$name] = $panel;
    return $panel;
}


function createContextPanel() {
    global $wso;

    $contextPanel       = createPanel("Context");
    $treeView           = $contextPanel->createTreeView(0, 0, 0, 0);
    $treeView->align    = $wso->translate("AL_CLIENT");
    $root               = $treeView->items->add("Item 1");
    
    for ($i = 1; $i < 5; $i++) {
        $root->add("Item 1." . $i);
    }
    
    $root->expand();

    return $contextPanel;
}

function createIndexPanel() {
    global $wso;

    $indexPanel = createPanel("Index");

    $listBox = $indexPanel->createListBox();
    $listBox->align = $wso->translate("AL_CLIENT");
    for ($i = 1; $i < 5; $i++) {
        $listBox->add("Item 1." . $i);
    }

    return $indexPanel;
}

$doc1 = createDocument("Doc1");

for ($i = 2; $i < 4; $i++) {
    $doc = createDocument("Doc" . $i);
    $doc1->docking->dockAsNeighbour($doc, $wso->translate("AL_CLIENT"));
}

$doc1->parent->visible = true;

$searchPanel = createBottomPanel("Search");
$consolePanel = createBottomPanel("Console");

$conedit = $consolePanel->createEdit( 0, 0, 0, 0, $wso->translate( "ES_MULTILINE | ES_READONLY" ) );
$conedit->align = $wso->translate( "AL_CLIENT" );
$conedit->add("Line 1");
$conedit->add("Line 2");
$conedit->add("Line 3");
$conedit->add("Line 4");
        
$srchedit = $searchPanel->createEdit(0, 0, 0, 0, $wso->translate("ES_MULTILINE | ES_READONLY"));
$srchedit->align = $wso->translate("AL_CLIENT");
$srchedit->add("Search result 1");
$srchedit->add("Search result 2");
$srchedit->add("Search result 3");
    
$searchPanel->docking->dockAsNeighbour( $consolePanel, $wso->translate( "AL_CLIENT" ) );
$contextPanel = createContextPanel();
$indexPanel = createIndexPanel();

$contextPanel->docking->dockAsNeighbour( $indexPanel, $wso->translate( "AL_CLIENT" ) );

$helpPanel = createPanel("Help");

$contextPanel->docking->dockAsNeighbour( $helpPanel, $wso->translate( "AL_CLIENT" ) );
$contextPanel->parent->visible = true;

$helpPanel->textOut( 10, 10, "Some Help can be there" );

$file = $form->menu->add("File");
$exit = $file->add("Exit");

// com_print_typeinfo($exit);

$exitEvents = new Class{
    function onExecute($sender){
        // echo "RUN!!!\n";
        $sender->form->close();
    }
};
com_event_sink($exit, $exitEvents, "IActionEvents");

$windows = $form->menu->add("Windows");

// var_dump($panels);
$panelEvents = new Class{
    function onExecute($sender){
        global $panels;
        $panel = $panels[$sender->text];
        while (true){
            $panel->visible = true;
            if ($panel->type == "Form")
                break;
            $panel = $panel->parent;
        }
    }
};

$i=0;
foreach ($panels as $name=>$obj) {
    // var_dump($name);
    $item[$i] = $windows->add($name);
    com_event_sink($item[$i], $panelEvents, "IActionEvents");
    $i++;
}

$layout = $form->menu->add("Layout");
$save = $layout->Add("Save");
$load = $layout->Add("Load");

$saveEvents = new Class {
    function onExecute(){
        global $wso;
        $text = $wso->saveLayout();
        file_put_contents(".layout", $text);
    }
};

$loadEvents = new Class {
    function onExecute(){
        global $wso,$form;
        
        if (!file_exists(".layout")) {
            $form->messageBox("Layout File does not exists");
            return;
        }else{
            $wso->loadLayout(file_get_contents(".layout"));    
        }
    }
};

com_event_sink($save, $saveEvents, "IActionEvents");
com_event_sink($load, $loadEvents, "IActionEvents");

$form->Show();
$wso->run();
