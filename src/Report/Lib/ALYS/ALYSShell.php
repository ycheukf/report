<?php
namespace YcheukfReport\Lib\ALYS;
/**
	@version V1.0 Nov 2011   (c) 2011-2012 (allyes.com). All rights reserved.
	shell
 */

class ALYSShell {
	var $aConfig;
	var $aPreConfig;
	var $runFlag;
	var $aPreConfigMemo;
	public function __construct($aConfig){
		$this->runFlag = true;
		$this->aConfig = $aConfig;
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		$this->_get_preconfigmemo();
		$this->aConfig['temdir'] = $_ALYSconfig['cache']['shell']['cacheDir'];
		if(isset($this->aConfig['remotepassword']))
			$this->aConfig['remotepassword'] = $this->_generalSshpsw($this->aConfig['remotepassword']);
		if(isset($this->aConfig['password']))
			$this->aConfig['password'] = $this->_generalSshpsw($this->aConfig['password']);


	}

	public function _get_preconfigmemo(){
		$this->aPreConfigMemo = array(
			'package_name' => 'string. package name, usually use the projectname', 
			'svnbash' => 'string. svn command bash', 
			'username' => 'string. svn author name',
			'password' => 'string. svn author password', 
			'localpath' => 'string. svn local path, checkout item',
			'url' => 'string. svn url', 
			'exportpath' => 'string. export path',
			'temdir' => 'string. temp path. create tmp sh file to run',  
			'user' => 'string. ssh user',
			'usergroup' => 'string. ssh user group',


			'remotehost' => 'string. remote online host',	
			'remoteport' => 'string. remote online host port', 
			'remoteuser' => 'string. ssh user', 
			'remotepassword' => 'string. ssh user password', 
			'remotepath' => 'string. remote place path, this script will untar the  $paclage_name.\'tar.gz\' into here (fold name is $paclage_name)',
			'remoteexpect' => 'string. expect word of remote host', 


			//////////////*********db sync**********////////////////
			'dbuser' => 'string. database user name ',
			'dbpwd' => 'string. database user password',
			'dbname' => 'string. database name',
			'dbport' => 'string. database host poat',
			'dbhost' => 'string. database host',
			'mysqlbash' => 'string. mysql bash', 
			'mysqldumppath' => 'string. mysqldump bash',

			'synctables' => 'array. tables need to export',

			'remotemysqldumppath' => 'string. mysqldump path',
			'remotedbuser' => 'string. database user name ',
			'remotedbpwd' => 'string. database user password',
			'remotedbname' => 'string. database name',
		//	'dbport' => 'string. database host poat',
			'remotedbhost' => 'string. database host',
			'remotemysqldumpbash' => 'string. mysqldump bash', //svn command bash
			'chmod' => 'array. files and mode. ex: array(777=>array("dir"), ..)',
		);
	}

	public function _chkConfig(){
		$sRunMemo = "";
		if(!$this->runFlag){
			$sRunMemo = <<<OUTPUT

######ERROR######
the above configs is wrong, plz check & try again.

OUTPUT;
		}
		$s = var_export($this->aPreConfig, 1);	
		echo <<<OUTPUT
			only run in linux.
			
======================================================================
{$s}
======================================================================
make sure the configs below is right. 
{$sRunMemo}

OUTPUT;
		
		if($this->runFlag){
			$sShellConfirm = array();
			$sShellConfirm = $this->shell_confirm();
			$flag = $this->_goshell(__FUNCTION__, 0, $sShellConfirm); 
			if(!$flag){
				return false;
			}
		}else{
			return false;
		}
		return true;
	}


	public function shell_confirm(){
		$a = array();
		$a[] = "#!/bin/sh";
		$a[] = 'echo -n "ready to run ? 1/0: "';
		$a[] = 'read flag';
		$a[] = 'case $flag in';
		$a[] = '1) exit 1;;';
		$a[] = '*) exit 0;;';
		$a[] = 'esac';
		return $a;
	}

	public function _get_config($name){
		if(!isset($this->aPreConfig[$name])){
			if(isset($this->aConfig[$name]))
				$this->aPreConfig[$name] =  $this->aConfig[$name];
			else{
				$this->runFlag = false;
				$this->aPreConfig[$name] =  (isset($this->aPreConfigMemo[$name]) ? "null ( ".$this->aPreConfigMemo[$name].")" : "null (needs description)");
			}
		}
		if(isset($this->aConfig[$name])){
			return $this->aConfig[$name];
		}
		return null;
	}


	/**
	* run shell script
	* @param aShell array shell script 
	*/
	public function go($sFun, $aShell){
		echo "============================start===========================\n";
		foreach($aShell as $i=>$shell){
			if(empty($shell))continue;
			echo  "--------shell {$i} start-----\n";
//			var_export($shell);
//			echo  "--------ouput split--------\n";
			$flag = $this->_goshell($sFun, $i, $shell);
			if($flag !== 1)
				die("\n\nsome errors!\n\n");
			echo  "--------shell {$i} end--------\n\n";
		}
		echo "============================end===========================\n";
	}

	public function _generalSshpsw($s){
		$s = str_replace("$", '\$', $s);
		$s = str_replace("[", '\[', $s);
		$s = str_replace("`", '\`', $s);
		return $s;
	}


	public function _goshell($sFun, $i, $shell){
		$sReturn = true;
		if(!file_exists($this->aConfig['temdir']))
			mkdir($this->aConfig['temdir']);
		$sTmp = join("\n", $shell);

		$filename = $this->aConfig['temdir']."/".$i."_".$sFun.".sh";

		$fp = fopen($filename, 'w');
		fwrite($fp, $sTmp."\n\n");
		fclose($fp);
		chmod($filename, 0770);

		if(preg_match("/win/i", PHP_OS))
			echo "cant run this script in windows.".$filename. "\n";
		else{
			echo "excu sh:  ".$filename. "\n";
			passthru($filename, $sReturn);
		}
		return $sReturn;
	}
}