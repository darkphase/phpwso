<?php
	class WSOWrapper {
		function __construct(){
			// $this->unRegCOMServer();
			// return;
			// sleep(1);
			if( !$this->checkCOMServer() ) {
				$this->regCOMServer();
				sleep(1);
			}
		}

		public function init(){
			$wso = new COM("Scripting.WindowSystemObject");
			return $wso;
		}

		private function getCPUArch(){
			return 8 * PHP_INT_SIZE;
		}

		private function regCOMServer(){
			$UAC = new COM("Shell.Application"); 
			echo $UAC->ShellExecute( $this->findRegSvrPath(), "/s ".realpath("bin/WSO".$this->getCPUArch().".dll"), "", "runas", 1 );	
		}

		private function unRegCOMServer(){
			$UAC = new COM("Shell.Application"); 
			echo $UAC->ShellExecute( $this->findRegSvrPath(), "/u /s ".realpath("bin/WSO".$this->getCPUArch().".dll"), "", "runas", 1 );	
		}

		private function checkCOMServer(){
			try {
				new COM("Scripting.WindowSystemObject");
			} catch (Exception $e) {
				return false;
			}
			return true;	
		}

		private function findRegSvrPath(){
			exec("where regsvr32.exe",$output,$ret);
			return $output[0];
		}

		public function COMEvent( $object, $event, callable $fn, $type="Control" ){

			$handler = new class(){

				public function addMethod($methodName, $methodCallable){
			        // var_dump($methodCallable);
			        if (!is_callable($methodCallable)) {
			            throw new InvalidArgumentException('Second param must be callable');
			        }
			        $this->methods[$methodName] = Closure::bind( $methodCallable, $this );
			    }

				public function __call($methodName, array $args){
        			if (isset($this->methods[$methodName])) {
            			return call_user_func_array($this->methods[$methodName], $args);
        			}else{
        				// echo $methodName."\n";
        				return false;
        			}
        			// throw Exception('There is no method with the given name to call');
    			}

			};
			$handler->addMethod($event,$fn);
			// var_dump( $handler );

			$dispinterfaces = [ 
								"IControlEvents",
								"IFormEvents",
								"IActionEvents",
								"ITimerEvents",
								"IHeaderItemEvents",
								"IHeaderEvents",
								"IListViewEvents",
								"ITreeViewEvents",
								"IRichEditEvents",
								"IComboBoxEvents",
								"IFindReplaceDialogEvents",
								"IFileOpenSaveDialogEvents",
								"ISelectFolderDialogEvents",
								"ITrayIconEvents",
								"IEventHandlerEvents",
								"IFontDialogEvents",
								"IColorDialogEvents",
								"IListControlEvents", 
							];
			$di = "I".$type."Events";

			if( !in_array($di, $dispinterfaces ) ){
				throw new Exception("Undefined Dispinterface");
			}else{
				com_event_sink( $object, $handler, $di );	
			}
		}
	}
?>