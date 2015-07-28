<?php
require_once("ALYS/Shell.php");
class ALYSShell_Database extends ALYSShell{
//	public function __construct($aConfig){
//		parent::__construct($aConfig);
//	}

	public function shell_tarremotetables(){
		$aTarRemoteDb = array();
		$aTarRemoteDb[] = "#!/usr/bin/expect -f";
		$aTarRemoteDb[] = "set timeout -1";
		$aTarRemoteDb[] = 'spawn ssh -p '.$this->_get_config("remoteport").' '.$this->_get_config("remoteuser").'@'.$this->_get_config("remotehost");
		$aTarRemoteDb[] = 'expect "password: "';
		$aTarRemoteDb[] = 'send "'.$this->_get_config("remotepassword")."\\r\"";
		$aTarRemoteDb[] = 'expect "'.$this->_get_config("remoteexpect").'"';
		$aTarRemoteDb[] = "send \"mkdir -p ". $this->_get_config("remotemysqldumppath")."\\r\"";
		$aTarRemoteDb[] = "send \"cd ". $this->_get_config("remotemysqldumppath")."\\r\"";
		$aTables = $this->_get_config("synctables");
//		$aTarRemoteDb[] = "send \"rm -fR *".."\\r\"";
		if(count($aTables)){
			foreach($aTables as $sTable){
				$aTarRemoteDb[] = "send \"".$this->_get_config("remotemysqldumpbash")." -h".$this->_get_config("remotedbhost")."  -u".$this->_get_config("remotedbuser")." -p".$this->_get_config("remotedbpwd")."  --add-drop-table ".$this->_get_config("remotedbname")." {$sTable} > ".$this->_get_config("remotemysqldumppath")."/{$sTable}.sql"."\\r\"";
			}
		}
		//$aTarRemoteDb[] = "send \"rm -fR ".$this->_get_config("remotemysqldumppath")."/".$this->_get_config("dbname").".tar.gz "."\\r\"";
		$aTarRemoteDb[] = "send \"tar -czvf ".$this->_get_config("remotemysqldumppath")."/".$this->_get_config("dbname").".tar.gz ./"."\\r\"";
		$aTarRemoteDb[] = 'send "exit\r"';
		$aTarRemoteDb[] = 'expect eof';
		$aTarRemoteDb[] = 'exit 1';
		return $aTarRemoteDb;
	}
	
	public function shell_synctar2local(){
		$aFTPDownTar = array();
		$aFTPDownTar[] = "#!/usr/bin/expect -f";
		$aFTPDownTar[] = "set timeout -1";
		$aFTPDownTar[] = "spawn mkdir -p ". $this->_get_config("mysqldumppath");
		$aFTPDownTar[] = 'spawn rsync -avze "ssh -p '.$this->_get_config("remoteport").'"  '.$this->_get_config("remoteuser").'@'.$this->_get_config("remotehost").':'.$this->_get_config("remotemysqldumppath").'/'.$this->_get_config("dbname").'.tar.gz '.$this->_get_config("mysqldumppath").'/'.$this->_get_config("dbname").".tar.gz";
		$aFTPDownTar[] = 'expect "password: "';
		$aFTPDownTar[] = 'send "'.$this->_get_config("remotepassword")."\\r\"";
		$aFTPDownTar[] = 'send "exit\r"';
		$aFTPDownTar[] = 'expect eof';
		$aFTPDownTar[] = 'exit 1';
		return $aFTPDownTar;
	}

	public function shell_untarpackage(){
		$aUntar = array();
		$aUntar[] = "#!/bin/sh";
		$aUntar[] = "cd ". $this->_get_config("mysqldumppath");
		$aUntar[] = 'tar -xzvf '.$this->_get_config("dbname").".tar.gz";
		$aUntar[] = 'exit 1';
		return $aUntar;
	}

	public function shell_importtables(){
		// mysqldump -u wcnc -p  --add-drop-table smgp_apps_wcnc >d:\wcnc_db.sql
		$sTimeSpan = date("YmdHis");
		$sBakDir = "bak/".$sTimeSpan;
		$aDbImport = array();
		$aDbImport[] = "#!/bin/sh";
		$aDbImport[] = "cd ". $this->_get_config("mysqldumppath");
		$aDbImport[] = "mkdir -p ".$sBakDir;
		$aTables = $this->_get_config("synctables");
		if(count($aTables)){
			foreach($aTables as $sTable){
				$aDbImport[] = 'mysqldump  -h'.$this->_get_config("dbhost").'  -u'.$this->_get_config("dbuser").' -p'.$this->_get_config("dbpwd").'  --add-drop-table '.$this->_get_config("dbname").' '.$sTable.' > '.$sBakDir.'/'.$sTable.'.sql ';
			}
			$aDbImport[] = "cd ". $this->_get_config("mysqldumppath")."/bak";
			$aDbImport[] = "tar -czvf ".$sTimeSpan.".tar.gz ".$sTimeSpan;
			$aDbImport[] = "rm -fR ".$sTimeSpan;
			$aDbImport[] = "cd ". $this->_get_config("mysqldumppath");
			foreach($aTables as $sTable){
				$aDbImport[] = $this->_get_config("mysqlbash").'  -h'.$this->_get_config("dbhost").'  -u'.$this->_get_config("dbuser").' -p'.$this->_get_config("dbpwd").'  '.$this->_get_config("dbname").' < '.$sTable.'.sql ';
			}
		}
		$aDbImport[] = 'exit 1';
		return $aDbImport;
	}

	/**
	* sync tabls from online database to localhost database
	*/
	public function syncO2L(){
		$aShell = array();

		$aShell[] = $this->shell_tarremotetables();
		$aShell[] = $this->shell_synctar2local();
		$aShell[] = $this->shell_untarpackage();
		$aShell[] = $this->shell_importtables();

		//run script
		if($this->_chkConfig()){
			$this->go(__CLASS__.'-'.__FUNCTION__, $aShell); 
		}else{
			die("faild! \n\n");
		}

	}


}