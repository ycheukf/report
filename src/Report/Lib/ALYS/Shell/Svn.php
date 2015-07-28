<?php
require_once("ALYS/Shell.php");
class ALYSShell_Svn extends ALYSShell{


	public function shell_exporttar(){
		$aTarLocal = array();
		$aTarLocal[] = "#!/bin/sh";
		$aTarLocal[] = $this->_get_config("svnbash")." export --username ".$this->_get_config("username")."  --force --password ".$this->_get_config("password")." --no-auth-cache ".$this->_get_config("url")." ".$this->_get_config("exportpath")."/".$this->_get_config("package_name");
		$aTarLocal[] = "exit 1";
		return $aTarLocal;
	}


	public function shell_tarpackage(){
		$aTarLocal = array();
		$aTarLocal[] = "#!/bin/sh";
		$aTarLocal[] = "cd ".$this->_get_config("exportpath");
		$aTarLocal[] = "pwd";
		$aTarLocal[] = "tar -czvf ".$this->_get_config("exportpath")."/".$this->_get_config("package_name").".tar.gz ".$this->_get_config("package_name")."/";
		$aTarLocal[] = "exit 1";
		return $aTarLocal;
	}


	public function shell_rsync2remote(){
		$aFTPUploadTar = array();
		$aFTPUploadTar[] = "#!/usr/bin/expect -f";
		$aFTPUploadTar[] = "set timeout -1";
		$aFTPUploadTar[] = 'spawn rsync -avze "ssh -p '.$this->_get_config("remoteport").'" '.$this->_get_config("exportpath").'/'.$this->_get_config("package_name").'.tar.gz '.$this->_get_config("remoteuser").'@'.$this->_get_config("remotehost").':'.$this->_get_config("remotepath").'/'.$this->_get_config("package_name").".tar.gz";
		$aFTPUploadTar[] = 'expect "password: "';
		$aFTPUploadTar[] = 'send "'.$this->_get_config("remotepassword")."\\r\"";
		$aFTPUploadTar[] = 'send "exit\r"';
		$aFTPUploadTar[] = 'expect eof';
		$aFTPUploadTar[] = "exit 1";

		return $aFTPUploadTar;
	}

	public function shell_untarremote(){
		$aUntarRemote = array();
		$aUntarRemote[] = "#!/usr/bin/expect -f";
		$aUntarRemote[] = "set timeout -1";
		$aUntarRemote[] = 'spawn ssh -p '.$this->_get_config("remoteport").' '.$this->_get_config("remoteuser").'@'.$this->_get_config("remotehost");
		$aUntarRemote[] = 'expect "password: "';
		$aUntarRemote[] = 'send "'.$this->_get_config("remotepassword")."\\r\"";
		$aUntarRemote[] = 'expect "'.$this->_get_config("remoteexpect").'"';
		$aUntarRemote[] = 'send "cd '.$this->_get_config("remotepath")."\\r\"";
		$aUntarRemote[] = 'send "tar -czvf '.$this->_get_config("package_name").".".date("YmdHis").'.tar.gz '.$this->_get_config("package_name").' '."\\r\"";
		$aUntarRemote[] = 'send "tar -xzvf '.$this->_get_config("package_name").'.tar.gz '."\\r\"";
		if(!is_null($this->_get_config("chmod"))){
			foreach($this->_get_config("chmod") as $nMod => $aPath){
				foreach($aPath as $sPath){
					$aUntarRemote[] = 'send "chmod -cR '.$nMod.' '.$this->_get_config("package_name")."/".$sPath."\\r\"";
				}
			}
		}
		$aUntarRemote[] = '#expect "'.$this->_get_config("remoteexpect").'$ "';
		$aUntarRemote[] = 'send "exit\r"';
		$aUntarRemote[] = 'expect eof';
		$aUntarRemote[] = "exit 1";

		return $aUntarRemote;
	}

	/**
	* sync tabls from online database to localhost database
	*/
	public function release2remote(){
		$aShell = array();

		$aShell[] = $this->shell_exporttar();
		$aShell[] = $this->shell_tarpackage();
		$aShell[] = $this->shell_rsync2remote();
		$aShell[] = $this->shell_untarremote();

		//run script
		if($this->_chkConfig()){
			$this->go(__CLASS__.'-'.__FUNCTION__, $aShell); 
		}else{
			die("faild! \n\n");
		}

	}


}