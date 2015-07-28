<?php
namespace YcheukfReport\Lib\ALYS\Dboperate;

/**
	由CString扩展的sql操作类,封装sql拼凑与adode数据获取的操作

*/
class Sql extends \YcheukfReport\Lib\ALYS\Dboperate\Dboperate{
	public $_oDb;


	function __construct(){
		parent::__construct();
		$this->_oDb =  \YcheukfReport\Lib\ALYS\ALYSFunction::loadDb();
	}
	
	

	/**
	根据数组设置搜索条件
	@parm array aConf
	@example 数组格式
		array{ 
			'noRecord' => 0,	//1=>不执行sql语句, 直接返回空
			'limit' => 1,	//1=>使用limit,0=>不使用
			'start' => 0, 
			'length' => 20, 
			'orderby' => 'id', 
			'orderbyDesc' => 1, 
			'groupby' => 'id', 
			'table' =>'user', 
			'field' =>'id, name' 
			'condition' => array('id=?'=>1, 'name=?'=>'MIC') 
			'conditionOr' => array('id=?'=>1, 'name=?'=>'MIC') 
		}
	*/
	function getBySqlConfig($aConf) {
		$start = empty($aConf['start'])?0:$aConf['start'];
		$length = empty($aConf['length'])?20:$aConf['length'];
		$field = empty($aConf['field_array'])?'*': join(",", $aConf['field_array']);
		$orderbyDesc = empty($aConf['orderbyDesc'])?'ASC':'DESC';
		$limit = isset($aConf['limit'])?$aConf['limit']:true;

		$this->sql_setFrom($aConf['table']);
		$this->sql_setSelect($field);
		if(!empty($aConf['condition']))
		$this->sql_setWhere($aConf['condition']);
		if(!empty($aConf['conditionOr']))
			$this->sql_setWhere($aConf['conditionOr'], 'or');
		if($limit)
			$this->sql_setLimit($start, $length);
		if(!empty($aConf['orderby']))
			$this->sql_setOrder($aConf['orderby'], $orderbyDesc);
		if(!empty($aConf['groupby']))
			$this->sql_setGroup($aConf['groupby']);
		$this->sql_setSql();
//		echo $this->sql_getSql();
		return $this->sql_getSql();
	}

	/**
		获取所有搜索结果
		@parm array aConf 同sql_getByConfig参数
	*/
	function getAll($aConf){
		$sql = $this->getBySqlConfig($aConf);
		defined('__CHECKFIELD__') && __CHECKFIELD__ && $this->checkField($sql);
		$a = array();
		foreach($this->_aParam as $aParam){
			if(is_array($aParam))
				$a = array_merge($a, $aParam);
			else
				$a[] = $aParam;
		}

		$oStatement = $this->_oDb->query($sql);
		$result = $oStatement->execute($a);
		$aRe = array();
		while($result->valid()){
			$aTmp = $result->current();
			if($aTmp)
				$aRe[] = $aTmp;
			$result->next();
		}
		
		$nTotal = $this->_getCount($aConf);

		$data = $aConf['noRecord']==1?array():$aRe;
		return array($data, $nTotal);
	}
	
	
	/**
		获取所有搜索结果
		@parm array aConf 同sql_getByConfig参数
	*/
	function cacheGetAll($aConf, $cacheTime=3600){
		return $this->getAll($aConf);
	}
	/**
		获取所有搜索结果
		@parm array aConf 同sql_getByConfig参数
	*/
	/**
		获取所有搜索总数
		@parm array aConf 同sql_getByConfig参数
	*/
	function _getCount($aConf){
		$oStatement = $this->_oDb->query("SELECT FOUND_ROWS() as total;");
		$result = $oStatement->execute();
		$aData = $result->current();
		return $aData['total']; 
		$oStatement = $this->_oDb->query($this->getBySqlConfig($aConf));
		return $oStatement->count();
	}
	
}