<?php
header('Content-type: application/json');
include_once('../../include/config.inc.php');
include_once('../../include/function.php');
include_once('../../include/func_session.php');
include_once('../../include/hubble.php');
$thisClass = $hubble;

class myself{
  private $module = 'nginx';
  private $subModule = 'version';
  private $arrFormat = array();

  function getList($param = array()){
    global $thisClass;
    $ret=array('code' => 1, 'msg' => 'Illegal Request', 'ret' => '');
    if($strList = $thisClass->get($this->module, $this->subModule, 'list', $param)){
      $arrList = json_decode($strList,true);
      if(isset($arrList['code']) && $arrList['code'] == 0 && isset($arrList['data']['content'])){
        $ret = array(
          'code' => 0,
          'msg' => 'success',
          'title' => array(
            '#',
            '名称',
            '版本',
            '是否废弃',
            '是否发布',
            '隶属单元',
            '用户',
            '时间',
            '#',
            ),
          'content' => array(),
        );
        if(isset($arrList['data']['count'])) $ret['count'] = $arrList['data']['count'];
        if(isset($arrList['data']['total_page'])) $ret['pageCount'] = $arrList['data']['total_page'];
        if(isset($arrList['data']['page'])) $ret['page'] = $arrList['data']['page'];
        $i=0;
        foreach($arrList['data']['content'] as $k => $v){
          $i++;
          $tArr = array();
          $tArr['i'] = $i;
          foreach($v as $key => $value){
            $tArr[$key] = $value;
          }
          $ret['content'][] = $tArr;
        }
      }else{
        $ret['code'] = 1;
        $arrList = json_decode($strList,true);
        $ret['msg'] = (isset($arrList['msg']))?$arrList['msg']:$strList;
      }
    }
    $ret['ret'] = $strList;
    return $ret;
  }

  function getInfo($param = array()){
    global $thisClass;
    $ret=array('code' => 1, 'msg' => 'Illegal Request', 'ret' => '');
    if($strList = $thisClass->get($this->module, $this->subModule, 'detail', $param)){
      $arrList = json_decode($strList,true);
      if(isset($arrList['code']) && $arrList['code'] == 0 && isset($arrList['data'])){
        $ret = array(
          'code' => 0,
          'msg' => 'success',
          'content' => array(),
        );
        $ret['content']=$arrList['data'];
      }else{
        $ret['code'] = 1;
        $arrList = json_decode($strList,true);
        $ret['msg'] = (isset($arrList['msg']))?$arrList['msg']:$strList;
      }
    }
    $ret['ret'] = $strList;
    return $ret;
  }

  function update($action = '', $param = array()){
    global $thisClass;
    $ret = array('code' => 1, 'msg' => 'Illegal Request', 'ret' => '');
    if($action){
      if($strList = $thisClass->get($this->module, $this->subModule, $action, $param)){
        $arrList = json_decode($strList,true);
        if(isset($arrList['code']) && $arrList['code'] == 0){
          $ret = array(
            'code' => 0,
            'msg' => 'success',
          );
        }else{
          $ret['code'] = 1;
          $arrList = json_decode($strList,true);
          $ret['msg'] = (isset($arrList['msg']))?$arrList['msg']:$strList;
        }
      }
      $ret['ret'] = $strList;
    }
    return $ret;
  }

  function format($arr = array(),$field = 'data'){
    if(!is_array($arr) || empty($arr)) return $arr;
    $ret = array();
    foreach($arr as $k => $v){
      if(in_array($k, $this->arrFormat)){
        $ret[$k] = $v;
      }else{
        $ret[$field][$k] = $v;
      }
    }
    return $ret;
  }
}
$mySelf=new myself();

/*权限检查*/
$pageForSuper = false;//当前页面是否需要管理员权限
$hasLimit = ($pageForSuper)?isSuper($myUser):true;
$myAction = (isset($_POST['action'])&&!empty($_POST['action']))?trim($_POST['action']):((isset($_GET['action'])&&!empty($_GET['action']))?trim($_GET['action']):'');
$myIndex = (isset($_POST['index'])&&!empty($_POST['index']))?trim($_POST['index']):((isset($_GET['index'])&&!empty($_GET['index']))?trim($_GET['index']):'');
$myPage = (isset($_POST['page'])&&intval($_POST['page'])>0)?intval($_POST['page']):((isset($_GET['page'])&&intval($_GET['page'])>0)?intval($_GET['page']):1);
$myPageSize = (isset($_POST['pagesize'])&&intval($_POST['pagesize'])>0)?intval($_POST['pagesize']):((isset($_GET['pagesize'])&&intval($_GET['pagesize'])>0)?intval($_GET['pagesize']):$myPageSize);

$fUnit=(isset($_POST['fUnit'])&&!empty($_POST['fUnit']))?trim($_POST['fUnit']):((isset($_GET['fUnit'])&&!empty($_GET['fUnit']))?trim($_GET['fUnit']):'');
$fIdx=(isset($_POST['fIdx'])&&!empty($_POST['fIdx']))?trim($_POST['fIdx']):((isset($_GET['fIdx'])&&!empty($_GET['fIdx']))?trim($_GET['fIdx']):'');

$myJson=(isset($_POST['data'])&&!empty($_POST['data']))?trim($_POST['data']):((isset($_GET['data'])&&!empty($_GET['data']))?trim($_GET['data']):'');
$arrJson=($myJson)?json_decode($myJson,true):array();

//记录操作日志
$logFlag = true;
$logDesc = '';
$arrRecodeLog=array(
  't_time' => date('Y-m-d H:i:s'),
  't_user' => $myUser,
  't_module' => 'Nginx_Ver',
  't_action' => '',
  't_desc' => 'Resource:' . $_SERVER['REMOTE_ADDR'] . '.',
  't_code' => '传入：' . $myJson . "\n\n",
);
//返回
$retArr = array(
  'code' => 1,
  'action' => $myAction,
);
if($hasLimit){
  $retArr['msg'] = 'Param Error!';
  switch($myAction){
    case 'list':
      $logFlag = false;//本操作不记录日志
      $arrJson = array(
        'page' => $myPage,
        'limit' => $myPageSize,
        'unit_id' => $fUnit,
        'name' => $fIdx,
      );
      if(!$fIdx) unset($arrJson['name']);
      $retArr = $mySelf->getList($arrJson);
      if(count($retArr['content'])>$myPageSize) $myPageSize=count($retArr['content']);
      $retArr['page'] = $myPage;
      $retArr['pageSize'] = $myPageSize;
      if(!isset($retArr['pageCount'])||$retArr['pageCount']<1) $retArr['pageCount']=1;
      if(!isset($retArr['count'])) $retArr['count']=count($retArr['content']);
      if($retArr['page'] > $retArr['pageCount']) $retArr['page'] = 1;
    break;
    case 'info':
      $logFlag = false;//本操作不记录日志
      $arrJson = array(
        'id' => $fIdx,
      );
      $retArr = $mySelf->getInfo($arrJson);
      break;
    case 'insert':
      if($myStatus > 0){ $retArr['msg'] = 'Permission Denied!'; break; }
      $arrRecodeLog['t_action'] = '创建';
      if(isset($arrJson) && !empty($arrJson)){
        if(isset($arrJson['id'])) unset($arrJson['id']);
        $arrJson['user'] = $myUser;
        $retArr = $mySelf->update('create', $arrJson);
        $logDesc = (isset($retArr['code']) && $retArr['code'] == 0) ? 'SUCCESS' : 'FAILED';
      }
      break;
    case 'generate':
      if($myStatus > 0){ $retArr['msg'] = 'Permission Denied!'; break; }
      $arrRecodeLog['t_action'] = '生成';
      if(isset($arrJson) && !empty($arrJson)){
        if(isset($arrJson['id'])) unset($arrJson['id']);
        $arrJson['user'] = $myUser;
        $retArr = $mySelf->update('generate', $arrJson);
        $logDesc = (isset($retArr['code']) && $retArr['code'] == 0) ? 'SUCCESS' : 'FAILED';
      }
      break;
    case 'deprecated':
      if($myStatus > 0){ $retArr['msg'] = 'Permission Denied!'; break; }
      $arrRecodeLog['t_action'] = '废弃';
      if(isset($arrJson) && !empty($arrJson)){
        $arrJson['user'] = $myUser;
        $retArr=$mySelf->update('deprecated', $arrJson);
        $logDesc = (isset($retArr['code']) && $retArr['code'] == 0) ? 'SUCCESS' : 'FAILED';
      }
    break;
    case 'release':
      if($myStatus > 0){ $retArr['msg'] = 'Permission Denied!'; break; }
      $arrRecodeLog['t_action'] = '发布';
      if(isset($arrJson) && !empty($arrJson)){
        $arrJson['user'] = $myUser;
        $retArr=$mySelf->update('release', $arrJson);
        $logDesc = (isset($retArr['code']) && $retArr['code'] == 0) ? 'SUCCESS' : 'FAILED';
      }
      break;
  }
}else{
  $retArr['msg'] = 'Permission Denied!';
}
//记录日志
if($logFlag){
  $arrRecodeLog['t_desc'] = ($logDesc) ? $logDesc.', '.$arrRecodeLog['t_desc'] : $arrRecodeLog['t_desc'];
  $arrRecodeLog['t_code'] .= '外部接口传入：' . json_encode($arrJson,JSON_UNESCAPED_UNICODE) . "\n\n";
  $arrRecodeLog['t_code'] .= '外部接口返回：' . str_replace(array("\n", "\r"), '', $retArr['ret']) . "\n\n";
  $arrRecodeLog['t_code'] .= '返回：' . json_encode($retArr,JSON_UNESCAPED_UNICODE);
  if(empty($arrRecodeLog['t_action'])) $arrRecodeLog['t_action'] = $myAction;
  logRecord($arrRecodeLog);
}
//返回结果
if(isset($retArr['action']) && !empty($retArr['action'])) $retArr['action'] = $myAction;
if(isset($retArr['ret'])) unset($retArr['ret']);
echo json_encode($retArr, JSON_UNESCAPED_UNICODE);
?>