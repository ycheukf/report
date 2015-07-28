<?php
namespace YcheukfReport\Lib\ALYS;
use PEAR\Cache\Lite;
/**
* ������
* �������ֻ�����ʽ��
*	1���ļ�����
*	2��memcache����
*
*/
class ALYSCache{
	public $cac=null;
	public $cacheType='';//Ĭ��Ϊpear cache
	public $cacheHandelKey='';//Ĭ��Ϊphp
	function __construct()
	{			
		$this->setType();		
	}	
	function setType($type='', $aConfig=array())
	{	//$type=file/mmc  //���û�������
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();	
		$this->cacheType = empty($type)? $_ALYSconfig['cache']['type'] : $type;	
		$this->cacheHandelKey = $_ALYSconfig['cache']['file_mem_path'] ;	
		$aConfig = empty($aConfig) ? $_ALYSconfig['cache'] : $aConfig;	
		switch($this->cacheType)
		{			
			case 'mmc':	
				$this->cac=new \Memcache;
				$this->cac->connect($aConfig['mmc']['host'],
									$aConfig['mmc']['port']							
									);					
				break;
			case 'file':			
				$this->cac=new \Cache_Lite($aConfig[$this->cacheHandelKey]);		
				break;
			case 'zf2cache':
			default:	
				$this->cac = \YcheukfCommon\Lib\Functions::getCacheObject($_ALYSconfig['smHandle'], 'filesystem');
			break;
		}
	}

	function setHandle($key='php',$type="file"){
//		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();	
//		$this->cacheHandelKey = $key;	
//		$this->cac=new Cache_Lite($_ALYSconfig['cache'][$key]);	
	}

	function save($key,$data,$reqTime=0)
	{
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();		
		if($this->cacheType=='file')
		{						
			$reqTime=($reqTime==0) ? $_ALYSconfig['cache'][$this->cacheHandelKey]['lifeTime'] : $reqTime;
			$saveArray=array(
							'data'		=>$data,							
							'lifetime'	=>$reqTime
							);
			 $this->cac->save(json_encode($saveArray),$key);
			 return $this->cac->_file;
		}
		elseif($this->cacheType=='mmc')
	   {
			$reqTime=($reqTime==0) ? $_ALYSconfig['cache']['mmc']['lifeTime'] : $reqTime;
			return $this->cac->set($key,$data,MEMCACHE_COMPRESSED,$reqTime);
		}
		elseif($this->cacheType=='zf2cache')
	   {
			$reqTime=($reqTime==0) ? 0 : $reqTime;
			$saveArray=array(
							'data'		=>$data,							
							'lifetime'	=>$reqTime
							);
			 $this->cac->setItem($key, json_encode($saveArray));
		}
		else
			return false;
	}
	function _secTime(){
		return (int)strtotime(date("Y-m-d H:i:s"));
	}
	function get($key)
	{ // ��ȡ��������		
		$_ALYSconfig = \YcheukfReport\Lib\ALYS\ALYSConfig::get();
		if($key=='w2fdebug' && $_ALYSconfig['debug']!=1)
			return "";
		if($this->cacheType=='file')
		{				
			$life=(int)json_decode($this->cac->get($key))->lifetime;
			$last=(int)$this->cac->lastModified();
			$add=$last + $life;
			$now=$this->_secTime();
			$diff=$add - $now;					
			$buffer= $diff > 0 ? json_decode($this->cac->get($key))->data : $this->cac->setLifeTime(0);			
     		return 	$buffer;			

		}
		elseif($this->cacheType=='mmc')
		{
			return  $this->cac->get($key);
		}
		else
			return false;		
	}
	function delete($key,$timeout=0)
	{ //ɾ����������
		if($this->cacheType=='file')
		{
			return $this->cac->remove($key);
		}
		elseif($this->cacheType=='mmc')
		{
			return  $this->cac->delete($key,$timeout);
		}
		else
			return false;
	}
	function flush()
	{//mmc ������л������ݣ��������ɾ����������ݣ�ֻ��ʹ���б����Ļ�����ڣ�ʹ�ڴ��е����ݱ���д
		if($this->cacheType=='file')
		{
			$this->cac->clean();
		}
		elseif($this->cacheType=='mmc')
		{
			$this->cac->flush();
		}
		else
			return false;
	}
}

?>
