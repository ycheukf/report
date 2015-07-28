<?php
require_once("ALYS/Shell.php");
class ALYSShell_Dechecker extends ALYSShell{



	public function shell_checker(){
		$aTarLocal = array();
		$aTarLocal[] = "#!/bin/sh";
		$aTarLocal[] = "cd ".$this->_get_config("adchecker_dir");
		$aTarLocal[] = "./".$this->_get_config("adchecker_file");
		$aTarLocal[] = "exit 1";
		return $aTarLocal;
	}
	/**
	* sync tabls from online database to localhost database
	*/
	public function getresult(){
		$aShell = array();

		$aShell[] = $this->shell_checker();

		//run script
		$this->go(__CLASS__.'-'.__FUNCTION__, $aShell); 

	}


}