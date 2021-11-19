<?php
// gate for .js ajax queries
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_dbbase.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_jgpktb.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/orgcomm/php/_jgcomm.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/dmgnPsAdm/php/db_depo.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/wagon_types/php/wagon_types_db.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pointsbase/php/_jgpoints.php';

include_once 'assist.php';

$form_data = [];    //Pass back the data

$part = strval($_POST['part']);
    
$form_data['success'] = false;

$assist = new assist();
$db_depo = new db_depo();
$db_type = new wagon_types_db();

/* бывшая проверка
if($part == 'clnf_get_rec_by_ip'){
    $ip = _dbbase::get_currentClientIP();
    //$ip = '2';
        $form_data['ip']  = $ip;
        $result = $db_depo->clnf_getRecByIP($ip);

        if (count($result) > 0) {
            $form_data['rid']  = $result['rid'];
            $form_data['org']  = $result['org'];
            $form_data['flg']   = $result['flg'];
            $form_data['ip']   = $result['ip'];
            $form_data['rem']  = $result['rem'];
            $form_data['success'] = true;
        }
}
*/

if ($part == 'get_app_globals') {
    $jg = new _jgpoints();
    $jg->jg_get_app_globals($form_data);     // $form_data pass by reference
    unset($jg);
    
    /* RESULT
        $form_data['max_photo_side_px']
        $form_data['colorbox_zoom']
    */
    
    $jg = new _jgpktb();
    $jg->jg_get_app_globals($form_data);     // $form_data pass by reference
    unset($jg);
    
    /* RESULT
        $form_data['ip']
        $form_data['ipls_id']
        $form_data['fdb_blk']
    */
    
    // extended
    $form_data['cip'] = _dbbase::get_currentClientIP();
    //$form_data['domain'] = _assbase::get_currentClientDomain();
    $form_data['itr']    = _assbase::isAdminIP('wagon_types_Ins') ? 1 : 0;                 // 1: Trusted IP
    //$form_data['dtr']    = _assbase::isAdminDomain('pointsC') ? 1 : 0;             // 1: ...
    $form_data['ptr']    = _assbase::isAdminPrefix('wagon_types_Ins') ? 1 : 0;             // 1: ...

    $form_data['success'] = true;
}

else if ($part == 'load_types_tbl' || $part == 'load_mdls_tbl'){ 

    if($part == 'load_types_tbl'){ 
        $result = $assist->show_types();
    }else if ($part == 'load_mdls_tbl'){
        $rid = strval($_POST['rid_type']);
        $offset = intval($_POST['offset']);  
        $rows = intval($_POST['rows']);    
        $currpage = intval($_POST['currpage']);  

        $from = (strlen(strval($_POST['from'])) > 0) ? strval($_POST['from']) : '';
        $to = (strlen(strval($_POST['to'])) > 0) ? strval($_POST['to']) : '';

        $country = (mb_strlen(strval($_POST['country'])) > 0) ? strval($_POST['country']) : '';

        $factory = (mb_strlen(strval($_POST['factory'])) > 0) ? strval($_POST['factory']) : '';  

        $result = $assist->show_mdls($offset, $rows, $currpage, $rid, $from, $to, $country, $factory); 
    }
   
    if($part == 'load_types_tbl'){
        if(strlen($result) > 0){
            $form_data['body'] = $result;
            $form_data['success'] = true;
        }
   }else{
        if(count($result) > 0){
            $form_data['filter'] = $result['filter'];
            $form_data['body'] = $result['tbl']; 
            $form_data['pagination'] = $result['pagination'];
            $form_data['success'] = true;
        }
    }
}
/*
else if($part == 'load_actType_info' || $part == 'load_actMdl_info'){
    $rid = strval($_POST['rid_type']);
    
    if($part == 'load_actType_info'){
       $result = $assist->show_type_info($rid);
    }else if ($part == 'load_actMdl_info'){
       $result = $assist->show_mdl_info($rid);
    }
    if(strlen($result) > 0){
       $form_data['body'] = $result;
       $form_data['success'] = true;
   }
}
*/
else if($part == 'load_exe_info'){ 
    $rid = strval($_POST['rid']);
    
    $result = $assist->show_execution_info($rid);
    
    if(strlen($result) > 0){
       $form_data['body'] = $result;
       $form_data['success'] = true;
   }
}

else if($part == 'load_execution'){
    $rid = strval($_POST['rid_mdl']);
    
    $result = $assist->show_execution($rid);
    
    if(strlen($result) > 0){
       $form_data['body'] = $result;
       $form_data['success'] = true;
   }
}

else if ($part == 'get_docs'){
    $pid = strval($_POST['pid']);
    
    $result = $assist->receive_docs($pid);
    
    $form_data['success'] = true;
    $form_data['body'] = $result;
}

else if ($part == 'search_get_mdls_row'){
    $rid = trim(strval($_POST['rid']));
    $result = $db_type->table_getRidRowNumber('mdls', 'nm', $rid);

    $form_data['rownum'] = $result;
    $form_data['success'] = true;
}


else if ($part == 'get_fm') {
    $fm_id = trim(strval($_POST['fm_id']));
   
    $result = $assist->get_fm($fm_id);

    $form_data['html'] = $result;
    if (strlen($result) > 0)
        $form_data['success'] = true;
}
/*
else if($part == 'type_data' || $part == 'mdl_data'){
    $nm =  strval($_POST['nm']);
    $flg = intval($_POST['flg']);
    $rem = strval($_POST['rem']);
    $rid = strval($_POST['rid']);
    
    if($part == 'type_data'){
       $result = $db_type->add_orEdit_type($rid, $nm, $flg, $rem);
    }else if($part == 'mdl_data'){
       $rid_type = strval($_POST['rid_type']);
       $result = $db_type->add_orEdit_mdl($rid, $nm, $rid_type, $flg, $rem);
    }
    if(strlen($result > 0)){
        $form_data['success'] = true;
        $form_data['rid'] = $result;
    }       
}

else if ($part == 'delete_type' || $part == 'delete_mdl'){  
    $rid = strval($_POST['rid']);
    
    if($part == 'delete_type')
        $result = $db_type->delete_type_by_rid($rid);
    else if ($part == 'delete_mdl')
        $result = $db_type->delete_mdl_by_rid($rid);
    
    if($result) $form_data['success'] = true;
}

else if($part == 'add_docs'){
        $tbl = trim(strval($_POST['tbl']));
        $pid = trim(strval($_POST['pid']));
        $fnm = _dbbase::shrink_filename(trim(strval($_POST['fnm'])), 50);
        $nm  = mb_substr(trim(strval($_POST['nm'])), 0, 50);
        $flg = intval($_POST['doc_flg']);
        $rdat = strval($_POST['rdat']);
        
        $result = $db_type->docs_addFile($tbl, $pid, $fnm, $nm, $flg, $rdat);
        
        if (strlen($result) > 0) {
            $form_data['pid'] = $pid;
            $form_data['docs_rid'] = $result;
            $form_data['success'] = true;
        }            
}

else if($part == 'delete_doc'){
    $rid = strval($_POST['rid']);
    
    $result = $db_type->delete_doc_by_rid($rid);
    
    if($result) $form_data['success'] = true;
}
*/
else if($part == 'file_put_tmp'){
        $rid = trim(strval($_POST['val']));

        $result = $db_type->get_document($rid);

        if (count($result) > 0) {
            $fname = _assbase::dataUri2tmpFile($_SERVER['DOCUMENT_ROOT'] . assist::siteRootDir() . '/tmp', $result['fnm'], $result['rdat']);

            if (mb_strlen($fname) > 0) {
                $form_data['frelname'] = assist::siteRootDir() . '/tmp/' . $result['fnm'];
                $form_data['success'] = true;
            }
        }
}

else if ($part == 'remove_tmp_file') {
    $jg = new _jgcomm();
    $jg->jg_remove_tmp_file($form_data);   // $form_data pass by reference
    unset($jg);
    
    /* RESULT
        --SUCCESS:
        $form_data['success'] = true;
        --ERROR:
        $form_data['success'] = false;
    */
}


//var_dump(_assbase::isAdminIP('wagon_types_Ins') );

unset($assist);
unset($db_depo);
unset($db_type);
//Return the data back
echo json_encode($form_data);
?>
