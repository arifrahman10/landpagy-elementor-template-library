<?php
const DIR_URL = 'https://www.spiderthemes-demos.com/spider-themes-template-library/landpagy/';

define("DIR_PATH", realpath( __DIR__) );

isset($_REQUEST['action']) || exit;

$action = $_REQUEST['action'];

include('functions.php');
$processor = New \Data_Processor();

switch($action){
    case 'get_layouts':
        $getTab = isset($_GET['tab']) ? $_GET['tab'] : '';
        
        $tab = ['section', 'page'];
        if (!empty($getTab)) {
            $tab = [$getTab];
        }

        $api['templates'] = [];
        $api['tags'] = [];
        $api['type_tags'] = [];
        
        foreach( $tab as $v){
            $data = $processor->get_list($v);
            $api['templates'] = array_merge($data['templates'], $api['templates']);
            $api['tags'] = array_merge($data['tags'], $api['tags']);
            $api['type_tags'][$v] = array_keys($data['tags']);
        }
        
        echo json_encode($api);
        exit;
        break;

    case 'get_layout_data':
        $data = $processor->get_data($_GET['id']);
        if( !empty($data) ){
            $data = file_get_contents($data);
        }
        echo $data;
        break;
    
    case 'get_api_data': 
        $data = $processor->get_api_data($_GET['id']);
        if( !empty($data) ){
            $data = file_get_contents($data);
        }
        echo $data;
        break;
}