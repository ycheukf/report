<?php
namespace YcheukfReport\Lib\ALYS\Dboperate;



class Dboperate{
	var $_sSql;
	var $_sCondition;
	var $_sTable;
	var $_sField;
	var $_sOperator;
	var $_sOrder;
	var $_sGroup;
	var $_aParam;
	var $_aWhereAnd;
	var $_aWhereOr;
	var $_sLimit;
	var $_sPre;

	function __construct(){
		
		$this->_sSql = '';
		$this->_sCondition = '';
		$this->_sTable = '';
		$this->_sField = '*';
		$this->_sOperator = 'SELECT';
		$this->_sOrder = '';
		$this->_sGroup = '';
		$this->_sLimit = '';
		$this->_aParam = array();
		$this->_aWhereAnd = array();
		$this->_aWhereOr = array();
		$this->_sPre = "and";

	}

	function sql_setOrder($field, $order='DESC') {
		if( empty( $field ) ) {
			$this->_sOrder = '';
		} else {
			$_a = array();
			if( !is_array($field) ) $_a[] = $field;
			else $_a = $field;
			
			$str = implode(',', $_a);
			if(strstr(strtoupper($str), 'ASC') || strstr(strtoupper($str), 'DESC'))
				$this->_sOrder = ' ORDER BY ' . ' '.implode(',', $_a);
			else
				$this->_sOrder = ' ORDER BY ' . ' '.implode(',', $_a).' '.$order;
		}
	}

	function sql_setGroup($field){
		$_a = array();
		if( !is_array($field) ) $_a[] = $field;
		else
			$_a = $field;
		$this->_sGroup = ' GROUP BY ' . ' '.implode(',', $_a);
	}

	function sql_setOperator($operator){
		switch(strtoupper($operator)){
			case 'SELECT':
				$this->_sOperator = 'SELECT';
				break;
			case 'UPDATE':
				$this->_sOperator = 'UPDATE';
				break;
			case 'DELETE':
				$this->_sOperator = 'DELETE';
				break;
			default:
				die( $operator . ' wrong param_setOperator:81' );
		}
	}

	function sql_setFrom( $table ){
		$this->_sTable = $table;
	}

	function sql_setSelect( $field ){
		$_a = array();
		if( !is_array($field) ) $_a[] = $field;
		else $_a = $field;

		$this->_sField = ' '.implode(',', $_a);
	}
	
	

	function sql_setWhere($condition , $operator='and'){
		if($condition == '' || count($condition)<1) return '';
		if($operator == 'and' ) 
			$this->_aWhereAnd[] = $condition;
		else
			$this->_aWhereOr[] = $condition;

	}

	function sql_clear(){
		$this->_sSql = '';
		$this->_sCondition = '';
		$this->_sTable = '';
		$this->_sField = '*';
		$this->_sOperator = 'SELECT';
		$this->_sOrder = '';
		$this->_sGroup = '';
		$this->_sLimit = '';
		$this->_aParam = array();
		$this->_aWhereAnd = array();
		$this->_aWhereOr = array();
	}

	function sql_filterParam($param){
		$_a = array();
		if( !is_array($param) )  $param = array($param);
		foreach( $param as $k => $v ){
			if(preg_match("/\?/i", $k, $m)){
//				echo $k."##".$v.'<br>';
				switch(true){
					case preg_match("/(.+)\s+like\s+(.+)/i", $k, $m):
						$v = str_replace('%', '\%', trim($v));
						$v = str_replace('*', '%', trim($v));
						$v = str_replace('_', '\_', trim($v));

						$_a[] = ' ' . trim($m[1]) . ' LIKE ' . $m[2];
						break;
					case preg_match("/(.+?)(\s+in\s+.+)/i", $k, $m):
						$_a[] = ' ' . trim($m[1]) . ' ' . $m[2];
						break;
					
					case preg_match("/(.+?)((?:!=|=|<>|>=|<=).+)/i", $k, $m):

						$_a[] = ' ' . trim($m[1]) . ' ' . $m[2];
						break;

					default:
						die( $v . ' wrong param_filterParam:146');
				}	
				
				$this->_aParam[] = $v;

							
			}elseif($k === "nofilter"){
				$_a[] = implode(' and ', $v);
			}else{
//				echo $k."#CC#".$v.'<br>';
				switch(true){
					case empty($v):
						$_a[] = $k;
						break;
					case preg_match("/(.+)\s+like\s+(.+)/i", $v, $m):
						$m[2] = str_replace('%', '\%', trim($m[2]));
						$m[2] = str_replace('*', '%', trim($m[2]));
						$_a[] = ' ' . trim($m[1]) . ' LIKE ' . $m[2];
						break;
					case preg_match("/(.+?)(\s+in\s+.+)/i", $v, $m):

						$_a[] = ' ' . trim($m[1]) . ' ' . $m[2];
						break;
					
					case preg_match("/(.+?)((?:!=|=|<>|>=|<=|>|<).+)/i", $v, $m):

						$_a[] = ' ' . trim($m[1]) . ' ' . $m[2];
						break;
//					case preg_match("/sql/i", $k, $m):
//
//						$_a[] = $v;
//						break;
					default:
						die( $v . ' wrong param_filterParam:179');
				
				}	
			}
//			$this->_aParam[] = $v;
		}
		
		return $_a;
	
	}

	function sql_setPre($pre = "or") {
		$this->_sPre = $pre;
	}

	function sql_setSql() {
		switch($this->_sOperator){
			case 'SELECT':
			
				$aryWhere = array();
				$this->_aParam = array();
				
				
				if(count($this->_aWhereAnd)>0) {
					$aryTmp = array();
					
					for($i=0 ; $i<count($this->_aWhereAnd) ; $i++){
						if(is_string($this->_aWhereAnd[$i]))
							$aryTmp[] = $this->_aWhereAnd[$i];
						else
							$aryTmp[] = implode( ' and ', $this->sql_filterParam( $this->_aWhereAnd[$i] ));	
					}
//					$aryWhere[] = ''.implode(' and ', $aryTmp).'';
					$aryWhere[] = '('.implode(' and ', $aryTmp).')';
				}
				if(count($this->_aWhereOr)>0) {
					$aryTmp = array();
					for($i=0 ; $i<count($this->_aWhereOr) ; $i++){
						if(is_string($this->_aWhereOr[$i]))
							$aryTmp[] = $this->_aWhereOr[$i];
						else
							$aryTmp[] = implode( ' or ', $this->sql_filterParam( $this->_aWhereOr[$i] ));				
					}
//					$aryWhere[] = ''.implode(' or ', $aryTmp).'';
					$aryWhere[] = '('.implode(' or ', $aryTmp).')';
				}

				$this->_sCondition = implode(' '.$this->_sPre.' ', $aryWhere);

				$this->_sSql = 'SELECT SQL_CALC_FOUND_ROWS ' . $this->_sField . 
							   ' FROM ' . $this->_sTable . 
				               (($this->_sCondition=='')?'':' WHERE '.$this->_sCondition) . 
							   (($this->_sGroup=='')?'':$this->_sGroup) .  
							   (($this->_sOrder=='')?'':$this->_sOrder) .
							   (($this->_sLimit=='')?'':$this->_sLimit);
				break;
			case 'UPDATE':
				$this->_sOperator = 'UPDATE';
				break;
			case 'DELETE':
				$this->_sOperator = 'DELETE';
				break;
			default:
				throw new \YcheukfReport\Lib\ALYS\ALYSException('_sOperator wrong param_setSql',$this->_sOperator);
		}

		return $this->_sSql;
	
	}

	function sql_getSql(){
		return $this->_sSql;
	}

	function sql_setLimit($start, $length){
		if( intval($length) != 0)
			$this->_sLimit = " LIMIT $start, $length"; 
		else
			$this->_sLimit = "";
	}

}
