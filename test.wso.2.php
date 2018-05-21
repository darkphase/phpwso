#!/D/App/PHP/PHP32.7.3.0-dev/php.exe 
<?php

	include("lib.dttk.wso.php");
	include("wsoconst.php");

	$wsow 	 = new WSOWrapper();
	$wso 	 = $wsow->init();
		
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

		$edit = $doc->createEdit(0,0,0,0,ES_MULTILINE);
		$edit->align = AL_CLIENT;
		$edit->text  = "Some text where";

		$doc->align = AL_CLIENT;
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
	    $panel->align = AL_LEFT;
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
	    $panel->align   = AL_BOTTOM;
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
	    $treeView->align    = AL_CLIENT;
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
	    $listBox->align = AL_CLIENT;
	    for ($i = 1; $i < 5; $i++) {
	        $listBox->add("Item 1." . $i);
	    }

	    return $indexPanel;
	}

	$doc1 = createDocument("Doc1");

	for ($i = 2; $i < 4; $i++) {
	    $doc = createDocument("Doc" . $i);
	    $doc1->docking->dockAsNeighbour($doc, AL_CLIENT);
	}

	$doc1->parent->visible = true;

	$searchPanel = createBottomPanel("Search");
	$consolePanel = createBottomPanel("Console");

	$conedit = $consolePanel->createEdit( 0, 0, 0, 0, ES_MULTILINE | ES_READONLY );
	$conedit->align = AL_CLIENT;
	$conedit->add("Line 1");
	$conedit->add("Line 2");
	$conedit->add("Line 3");
	$conedit->add("Line 4");
	        
	$srchedit = $searchPanel->createEdit(0, 0, 0, 0, ES_MULTILINE | ES_READONLY );
	$srchedit->align = AL_CLIENT;
	$srchedit->add("Search result 1");
	$srchedit->add("Search result 2");
	$srchedit->add("Search result 3");
	    
	$searchPanel->docking->dockAsNeighbour( $consolePanel, AL_CLIENT );
	$contextPanel = createContextPanel();
	$indexPanel = createIndexPanel();

	$contextPanel->docking->dockAsNeighbour( $indexPanel, AL_CLIENT );

	$helpPanel = createPanel("Help");

	$contextPanel->docking->dockAsNeighbour( $helpPanel, AL_CLIENT );
	$contextPanel->parent->visible = true;

	$helpPanel->textOut( 10, 10, "Some Help can be there" );

	$file = $form->menu->add("File");
	$exit = $file->add("Exit");

	$wsow->AttachCOMEvent($exit,"onExecute",
		function($sender){
			$sender->form->close();
		},
		"Action"
	);

	$windows = $form->menu->add("Windows");

	$i=0;
	foreach ($panels as $name=>$obj) {
	    // var_dump($name);
	    $item[$i] = $windows->add($name);
	    $wsow->AttachCOMEvent($item[$i],"onExecute",
	    	function($sender){
	    		global $panels;
		        $panel = $panels[$sender->text];
		        while (true){
		            $panel->visible = true;
		            if ($panel->type == "Form")
		                break;
		            $panel = $panel->parent;
		        }
	    	},
	    	"Action"
		);
	    $i++;
	}

	$layout = $form->menu->add("Layout");
	$save = $layout->Add("Save");
	$load = $layout->Add("Load");

	$wsow->AttachCOMEvent($save, "onExecute",
		function(){
			global $wso;
	        $text = $wso->saveLayout();
	        file_put_contents(".layout", $text);
		},
		"Action"
	);

	$wsow->AttachCOMEvent($load, "onExecute",
		function(){
			global $wso,$form;
	        if (!file_exists(".layout")) {
	            $form->messageBox("Layout File does not exists");
	            return;
	        }else{
	            $wso->loadLayout(file_get_contents(".layout"));    
	        }
		},
		"Action"
	);

	$form->Show();
	$wso->run();

?>