<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!defined('MODX_API_MODE')) {
    define('MODX_API_MODE', false);
}

include(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php');
if (!defined('MODX_CORE_PATH')) define('MODX_CORE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/core/');

include_once (MODX_CORE_PATH . "model/modx/modx.class.php");
$modx = new modX();
$modx->initialize('web');
$modx->addPackage('raschet', $modx->getOption('core_path') . 'components/raschet/model/');
$modx->addPackage('android', $modx->getOption('core_path') . 'components/android/model/');

if(!isset($_GET['get_details']) and !isset($_GET['get_version'])){
	if (!$Orgs = $modx->getService('organizations', 'Organizations',$modx->getOption('organizations_core_path', null, $modx->getOption('core_path') . 'components/organizations/') . 'model/organizations/', array())) {
		error('Could not load Organizations class!');
	}
	$android_versions = explode(",",$modx->getOption('android_versions'));
	if(!in_array($_REQUEST['android_version'],$android_versions)){
		error('Загрузите новую версию приложения!');
	}
}

if(isset($_GET['send_raschet'])){
	//$modx->log(1,'android '.print_r($_REQUEST,1));
	$entityBody = file_get_contents('php://input');
	//$modx->log(1,'android '.$entityBody);
	$setRashet = json_decode($entityBody,1);
	
	if(!$api_key = $modx->getObject('AndroidItem',array('api_key'=>$_REQUEST['api_key']))){
	    error('Ошибка авторизации!');
	}
	if($org = $modx->getObject('Orgs', $api_key->org_id)){
	    $skidka = $org->discount;
	    $setRashet['site_raschet']['shortname'] = $org->shortname;
	    $setRashet['site_raschet']['sender'] = "Android";
	}
	//создаем расчет
	if($_REQUEST['rewrite'] == 0){
	    $raschet = $modx->newObject('raschetOrders');
	}else{
    	if($raschet = $modx->getObject('raschetOrders',$setRashet['site_raschet']['site_id'])){
    	    //очищаем расчет
    	    $affected = $modx->removeCollection(
				'raschetPurchases',
				array(
					"order_id" => $raschet->id
				)
			);
    	}else{
    	    error('Ошибка получения расчета!');
    	}
	}
	$order_data = array(
    	'userid' => $api_key->user_id,
    	'org_id'=>$api_key->org_id,
    	'date' => strftime('%Y-%m-%d %H:%M:%S'),
    	'sentdate' => strftime('%Y-%m-%d %H:%M:%S'),
    	'status' => 1,
    	'options'=> json_encode($setRashet['site_raschet']),
    	'o_object'=>$setRashet['site_raschet']['object'],
    	'o_obj_rasch_id'=>$setRashet['site_raschet']['obj_rasch_id'],
    );
    $raschet->fromArray($order_data); 
	if(!$raschet->save()){
	    error('Ошибка сохранения расчета!');
	}
	//забиваем расчет
	require_once "./raschetPrice.class.php";
	$cf_config = array();
	//$modx->log(1,'android '.print_r($setRashet['site_raschet_details'],1));
	foreach($setRashet['site_raschet_details'] as $row){
	    if($detal = $modx->getObject('modResource', $row['title'])){
	        $row['name'] = mb_strtolower(trim($detal->pagetitle), "UTF-8");
	        foreach($row as $key => $value){
				$row[$key] = str_replace( ".0", "", $value);
			}
			if($row['name']=="воздуховод сп" and $row['gauge'] == 0.5){
    			$row['gauge'] = 0.45;
    		}
			$raschetPrice = new raschetPrice($modx,$cf_config);
    		$raschetPriceOut = $raschetPrice->getPrice($row, 0);
    		$price = round($raschetPriceOut['price']/0.7, 2);
    		$sum =ceil(round($price*(100 - $skidka)/100, 2))*$row['count'];
    		$row['S'] = $raschetPriceOut['S'];
    		$row['SN'] = $row['S']*$row['count'];
    		$new_p = array(
							'p_id' => $row['title'],
							'order_id' => $raschet->id,
							'name' => $detal->pagetitle,
							'count' => $row['count'],
							'price' => $sum,
							'options' => json_encode($row),
							'data'=> json_encode(array('uri' => $detal->uri))
						);
    		//$modx->log(1,'android '.print_r($new_p,1));
    		if($purchase = $modx->newObject( 'raschetPurchases')){
    		    $purchase->fromArray($new_p);
    		    $purchase->save();
    		}
	    }
	}
	$square=0;
	$price_order=0;
	//считаем сумму и площадь расчета
	if($purchases = $modx->getIterator( 'raschetPurchases' , array('order_id'=>$raschet->id))){
		foreach($purchases as $purchase){
			$price_order += $purchase->price;
			$p_opt = json_decode($purchase->options, true);
			$square += $p_opt['SN'];
		}
		$opt = json_decode($raschet->options);
		$opt->skidka = $skidka;
		$opt->square = $square;
		$order_data = array(
			'date' => strftime('%Y-%m-%d %H:%M:%S'),
			'price' => $price_order,
			'options' => json_encode($opt,true)
		);
		$raschet->fromArray($order_data);
		if($saved = $raschet->save()){
			$data = array(
			    'raschet_id'=>$raschet->id,
			    'raschet_data'=>$raschet->date,
			    );
			success($data);
		}else{
			error('Ошибка сохранения расчета!');
		}
	}
	error('Ошибка загрузки расчета!');
}
if(isset($_GET['test_raschet'])){
	if($raschet = $modx->getObject('raschetOrders',$_GET['test_raschet'])){
		$data = array();
		if($raschet->date == $_GET['raschet_date']){
		    $data['raschet_sync'] = true;
		}else{
		    $data['raschet_sync'] = false;
		}
		success($data);
	}
	error('Ошибка получения расчета!');
}

if(isset($_GET['get_raschet'])){
	if($raschet = $modx->getObject('raschetOrders',$_GET['get_raschet'])){
		$site_details = array();
		$details = $modx->getIterator('raschetPurchases', array('order_id'=>$raschet->id));
		foreach($details as $d){
			$site_details[] = json_decode($d->options,1);
		}
		$options = json_decode($raschet->options,1);
		$data = array(
		'site_raschet'=>array('object'=>$options['object'],'obj_rasch_id'=>$options['obj_rasch_id'],'site_id'=>$raschet->id,'site_date'=>$raschet->date),
		'site_raschet_details'=>$site_details
		);
		//print_r($data);
		success($data);
	}
	error('Ошибка получения расчета!');
}
if(isset($_GET['get_api_key'])){
	//echo 1;
	$data = array(
		'username' => $_GET['l'],
		'password' => $_GET['p'],
		'rememberme' => 0,
		'login_context' => 'web',
	);    
	$response = $modx->runProcessor('/security/login', $data);
	if ($response->isError()) {
		error('Не верный логин или пароль!');
	}else{
		if($user = $modx->getObject('modUser', array('username' => $_GET['l']))){
			$defaultOrg = $Orgs->getDefaultOrg($user->id);
			//получаем данные орг
			if($defaultOrg == 0){
				error('Не найдена организация пользователя!');
			}else{
				if($api_key = $modx->newObject('AndroidItem')){
					$api_key->fromArray(array(
						'user_id'=>$user->id,
						'org_id'=>$defaultOrg,
						'api_key'=>generateCode()
					));
					if($api_key->save()) success(array('api_key'=>$api_key->api_key));
				}
			}
		}
	}
	error('Не удалось создать ключ API');
}

if(isset($_GET['get_version'])){
    $android_versions = explode(",",$modx->getOption('android_versions'));
	if(in_array($_GET['android_version'],$android_versions)){
		$check_version = 1;
	}else{
		$check_version = 0;
	}
	$out = array(
		'detail_base_version'=>$modx->getOption('detail_base_version'),
		'check_version'=>$check_version
	);
    echo json_encode($out);
}

if(isset($_GET['get_details'])){
    $scriptProperties = array();
    $pdoFetch = $modx->getService('pdoFetch');
    $pdoFetch->setConfig($scriptProperties);
    $pdoFetch->addTime('pdoTools loaded');
    
    /*$where = array(
        '`UserTestResultAnswers`.`result_id`'=>$_GET['result_id']
    );*/
    
    $default = array(
        'class' => 'modResource',
        //'where' => $modx->toJSON($where),
        'sortby' => array(
            'menuindex' => 'ASC',
        ),
        'parents'=>'17,18',
        'includeTVs' => 'used_param,images,image,detail_version',
        'fastMode' => true,
        'showUnpublished' => false,
        'return' => 'data',
        'limit' => 0,
    );
    $pdoFetch->config = array_merge($pdoFetch->config, $default, $scriptProperties);
    $Details = $pdoFetch->run();
    $dets = array();
    foreach($Details as $det){
        $detType = array();
        if(is_array($det['images'])){
            foreach($det['images'] as $k=>$type){
                $dt = array(
                    //'dt_id'=>$k,
                    'title'=>$type['title'],
                    'site_image'=>$modx->getOption('site_url')."assets/components/vk24raschet/images/".$type['image'],
                    );
                $detType[] = $dt;
            }
        }
        if($det['parent'] == 17){
            $sech = 'pryam';
        }else{
            $sech = 'krug';
        }
        $d = array(
            'sech'=>$sech,
            'pagetitle'=>$det['pagetitle'],
            'title'=>$det['id'],
            'menuindex'=>$det['menuindex'],
            'used_param'=>$det['used_param'],
            'detail_version'=>$det['detail_version'],
            'site_image'=>$modx->getOption('site_url')."assets/components/vk24raschet/images/".$det['image'],
            'detTypeCount'=>count($detType),
            'siteDetType'=>$detType,
            );
        $dets[] = $d;
    }
    //print_r($dets);
    echo json_encode($dets);
}

function error($mes){
	$out = array(
		'success'=>false,
		'message'=>$mes
	);
    echo json_encode($out);
	exit;
}

function success($data){
	$data['success'] = true;
	$data['message'] = '';
    echo json_encode($data);
	exit;
}

function generateCode($length = 50){
	$chars = 'abcdefghijklmnopqrstuvwxyz1234567890';
	$numChars = strlen($chars);
	$string = '';
	for ($i = 0; $i < $length; $i++) {
	$string .= substr($chars, rand(1, $numChars) - 1, 1);
	}
	return $string;
}