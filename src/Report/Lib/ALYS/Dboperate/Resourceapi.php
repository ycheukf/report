<?php
namespace YcheukfReport\Lib\ALYS\Dboperate;

/**
    由CString扩展的sql操作类,封装sql拼凑与adode数据获取的操作

*/
class Resourceapi extends \YcheukfReport\Lib\ALYS\Dboperate\Dboperate{
    public $_oDb;


    function __construct(){
        parent::__construct();
    }
    
    

    /**
    根据数组设置搜索条件
    @parm array aConf
    @example 数组格式
        array{ 
            'noRecord' => 0,    //1=>不执行sql语句, 直接返回空
            'limit' => 1,    //1=>使用limit,0=>不使用
            'start' => 0, 
            'length' => 20, 
            'orderby' => 'id', 
            'orderbyDesc' => 1, 
            'groupby' => 'id', 
            'table' =>'user', 
            'field' =>'id, name' 
            'field_array' =>'array('name', 'id')' 
            'condition' => array('id=?'=>1, 'name=?'=>'MIC') 
            'conditionOr' => array('id=?'=>1, 'name=?'=>'MIC') 
        }
    */
    function _getBySqlConfig($oSmHandle, $aConf) {
        $start = empty($aConf['start'])?0:$aConf['start'];
        $length = empty($aConf['length'])?10:$aConf['length'];
        $aField = array();
//                var_dump($aConf['field_array']);
//                var_dump($aConf);
//                var_dump($aConf['dimen_array']);

        $aTmp2 = array();
        foreach($aConf['dimen_array'] as $s=>$m){
            $aTmp2[$s] = str_replace("[EXPRESSION]", "", $m);
           if(is_string($m) && preg_match("/\[EXPRESSION\]/i", $m)){
                $aConf['dimen_array'][$s] = array('$expression' => str_replace("[EXPRESSION]", "", $m));
            }
        }
//                 var_dump($aConf['dimen_array']);
       if(!empty($aTmp2)){
            $aField = array_merge($aField, $aConf['dimen_array']);
            $aField['concatKey'] = array('$concat' => array_values($aTmp2));
        }
//                var_dump($aField);
//                exit;
        if(!empty($aConf['metric_array'])){
            $aField = array_merge($aField, $aConf['metric_array']);
        }
        if(!empty($aConf['field_array'])){
            foreach($aConf['field_array'] as $sTmp){
                if(is_array($sTmp)){
                    $aField = array_merge($aField, $sTmp);
                
                }else{
                    list($sField, $sLabel) = explode(" as ", $sTmp);
                    $sField = trim($sField);
                    $sLabel = trim($sLabel);
                    if(!isset($aField[$sLabel]))
                        $aField[$sLabel] = $sField;
                }
            }
        }
//        var_dump($aField);
        $aField = empty($aField) ? "*" : $aField;
        $access_token = \YcheukfCommon\Lib\OauthClient::getDbProxyAccessToken($oSmHandle);
        $aParams = array(
            'op'=> 'get',
            'resource'=> $aConf['table'],
            'resource_type'=> 'common',
            'access_token' => $access_token,
            'params' => array( 
                'select' => array ( 
                    "columns" => $aField,
                    "offset"=> $start,
                    "limit"=> $length,
                ),
            ),
        );
        if(isset($aConf['orderby']))
            $aParams['params']['select']['order'] = $aConf['orderby'];
        if(isset($aConf['groupby']))
            $aParams['params']['select']['group'] = $aConf['groupby'];
//        var_dump($aConf);
        if(isset($aConf['condition_array']) && count($aConf['condition_array'])>0){
            $aWhere = array('$and' => array());
            foreach($aConf['condition_array'] as $row){
                $sOperation = isset($row['group']) ? '$or' : '$and';
                $sValue = $this->_fmtValue($row['value']);
                switch($row['op']){
                    case 'in':
                        $aWhere[$sOperation][] = array($aField[$row['key']] => array('$in'=>explode(",", $sValue)));
                        break;
                    case '=':
                        $aWhere[$sOperation][] =  array($aField[$row['key']] => $sValue);
                        break;
                    case '>=':
                        $aWhere[$sOperation][] =  array($aField[$row['key']] => array('$gte'=>$sValue));
                        break;
                    case '<>':
                        $aWhere[$sOperation][] =  array($aField[$row['key']] => array('$ne'=>$sValue));
                        break;
                    case '<=':
                        $aWhere[$sOperation][] =  array($aField[$row['key']] => array('$lte'=>$sValue));
                        break;
                    case '>':
                        $aWhere[$sOperation][] =  array($aField[$row['key']] => array('$gt'=>$sValue));
                        break;
                    case '<':
                        $aWhere[$sOperation][] =  array($aField[$row['key']] => array('$lt'=>$sValue));
                        break;
                    case 'like':
                        $aWhere[$sOperation][] =  array($aField[$row['key']] => array('$regex'=>$sValue));
                        break;
                }
            }
//            $aWhere['$or'][] = array("a"=>1);
//            $aWhere['$or'][] = array("b"=>1);
           // var_dump($aWhere);
            $aParams['params']['select']['where'] = $aWhere;
//            { field: { $in: [<value1>, <value2>, ... <valueN> ] } }
        }
        return $aParams;
    }
    function _fmtValue($sValue){
        $sValue = str_replace("(", "", $sValue);
        $sValue = str_replace(")", "", $sValue);
        $sValue = str_replace("'", "", $sValue);
        $sValue = str_replace("\"", "", $sValue);
        return $sValue;
    }
    /**
        获取所有搜索结果
        @parm array aConf 同sql_getByConfig参数
    */
    function getAll($aConf){
        $oSmHandle = \YcheukfReport\Lib\ALYS\ALYSConfig::get('smHandle');
        $aParams = $this->_getBySqlConfig($oSmHandle, $aConf);

        $aReturn = \Application\Model\Common::getResourceList2($oSmHandle, $aParams);
        return array($aReturn['dataset'],$aReturn['count']);
    }
    
    /**
        获取所有搜索结果
        @parm array aConf 同sql_getByConfig参数
    */
    function cacheGetAll($aConf, $cacheTime=3600){
        return $this->getAll($aConf);
    }

    
}