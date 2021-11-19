<?php
// include_once $_SERVER['DOCUMENT_ROOT'] . '/nxcomm/php/_db.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php'; 
include_once $_SERVER['DOCUMENT_ROOT'] . '/wagon_types/php/wagon_types_db.php';

class assist {
    public static $copyright_str = '&copy;ПКТБ Л, 2019&thinsp;&hellip;&thinsp;2021';
    
    public function __construct() {
        if (strlen(trim(session_id())) == 0)
            session_start();
    }    
    
    public static function siteRootDir() : string {    // site root must have index.php. directory will return with starting slash, ex: /IcmrM
        return _assbase::siteRootDir_($_SERVER['PHP_SELF']);
    }
  
    public function make_pagination(string $pg_id, int $offset, int $rows, int $currpage, int $totalrows) : string {
        $ass = new _assbase();
        $result = $ass->make_pagination($pg_id, $offset, $rows, $currpage, $totalrows);
        unset($ass);
        
        return $result;
    }
    
     public function get_fm(string $fm_id, $sparam = '') : string {
        $db = new wagon_types_db();
        $result = '';
        
        $path = $_SERVER['DOCUMENT_ROOT'] . '/wagon_types/php/forms/' .$fm_id. '.php'; // Катя, если идешь в другой проект путь д/б полный!
    
        if(file_exists($path)){
            $form = file_get_contents($path);
            
          if($fm_id == 'select_mdls_by_type'){
                $types_all = $db->typesList_Whole();
                $types_str = '';

                if(count($types_all) > 0)
                    foreach($types_all as $row)
                      $types_str .= '<div class="form-row">' .
                                       '<div class="custom-control custom-radio custom-control-inline">' .                                     
                                            '<input type="radio" class="custom-control-input" id="'.$row['rid'].'" onclick="get_mdls_4_type(this);" name="type_radio_select">'.
                                            '<label class="custom-control-label y-pad-t2" for="'.$row['rid'].'">'.$row['nm'].'</label>'.    
                                       '</div>'.
                                    '</div>'; 
                        
                        $form = str_replace('{items}', $types_str, $form);
            }
            
            else if ($fm_id == 'select_mdls_by_year'){
                $years = $db->get_prod_ys();
                $options_list = '';
                
                if(count($years) > 0)
                   foreach($years as $y) 
                      $options_list .= "<option value='" . $y['prod_ys'] . "'>" . $y['prod_ys'] . "</option>" ;
                
                $form = str_replace('{years_choose}', $options_list, $form);
                
            }
            
            else if($fm_id == 'select_mdls_by_country'){
                $countries = $db->get_countries();
                
                $options_list = '';
                
                if(count($countries) > 0)
                   foreach($countries as $c) 
                      $options_list .= "<option value='" . $c['country'] . "'>" . $c['country'] . "</option>" ;
                
                $form = str_replace('{types:countries_list}', $options_list, $form);
            }
            
            else if($fm_id == 'select_mdls_by_factory'){
                $factories = $db->get_factories();
                
                $options_list = '';
                
                if(count($factories) > 0)
                   foreach($factories as $f) 
                      $options_list .= "<option value='" . $f['factory'] . "'>" . $f['factory'] . "</option>" ;
                
                $form = str_replace('{factories_list}', $options_list, $form);
            }
            
             else if ($fm_id == 'select_mdls_by_multi'){
                $countries = $db->get_countries();
                $years = $db->get_prod_ys();
                $factories = $db->get_factories();
                
                $ys_list = "<option value=''></option>";
                
                if(count($years) > 0)
                   foreach($years as $y) 
                      $ys_list .= "<option value='" . $y['prod_ys'] . "'>" . $y['prod_ys'] . "</option>" ;
                
                $form = str_replace('{years_choose}', $ys_list, $form);
                
                $countries_list = "<option value=''></option>";
                
                if(count($countries) > 0)
                   foreach($countries as $c) 
                      $countries_list .= "<option value='" . $c['country'] . "'>" . $c['country'] . "</option>" ;
                
                $form = str_replace('{types:countries_list}', $countries_list, $form);
                
                $factories_list = "<option value=''></option>";
                
                if(count($factories) > 0)
                   foreach($factories as $f) 
                      $factories_list .= "<option value='" . $f['factory'] . "'>" . $f['factory'] . "</option>" ;
                
                $form = str_replace('{factories_list}', $factories_list, $form);
            }
            
            $result = $form;
        } else{
               $assbase = new _assbase();
                $result = $assbase->get_fm($fm_id, $sparam);
                unset($ass);
        }    
        unset($db);
        return $result;
    }
    
    
    
    function show_mdls($offset, $rows, $currpage, $rid_type = '', $from='', $to='', $country = '', $factory = ''){
        $mdls_list = [];
        $result = [];
        
        $db = new wagon_types_db();
        $types_all = $db->typesList_Whole();
        $totalrows = $db->mdls_getRowcount($rid_type, $from, $to, $country, $factory);       
        
        if ($offset >= 0 && $offset < $totalrows && $rows > 0 && $rows < $totalrows){
            $mdls_list = $db->mdlsList_Subset($offset, $rows, $rid_type);
            
            if(strlen($from) > 0 || strlen($to) > 0 || mb_strlen($country) > 0 || mb_strlen($factory) > 0)
                $mdls_list = $db->mdlsList_Subset_by_multi($offset, $rows, $country, $factory, $from, $to);
            
           /*  if(strlen($from) > 0 && strlen($to) > 0){
                $mdls_list = $db->mdlsList_Subset_by_years($offset, $rows, $from, $to);
            }
            
            else if(mb_strlen($country) > 0){
                $mdls_list = $db->mdlsList_Subset_by_country($offset, $rows, $country);
            }
            
            else if(mb_strlen($factory) > 0){
                $mdls_list = $db->mdlsList_Subset_by_factory($offset, $rows, $factory);
            }*/
        }
        else {
            $mdls_list = $db->mdlsList_Whole($rid_type);
            
            if(strlen($from) > 0 || strlen($to) > 0 || mb_strlen($country) > 0 || mb_strlen($factory) > 0)
                $mdls_list = $db->mdlsList_whole_by_multi($country, $factory, $from, $to);
            
          /*  if(strlen($from) > 0 && strlen($to) > 0)
                $mdls_list = $db->mdlsList_Whole_by_years($from, $to);
            
            else if(mb_strlen($country) > 0){
                $mdls_list = $db->mdlsList_Whole_by_country($country);
            }
            
            else if(mb_strlen($factory) > 0){
                $mdls_list = $db->mdlsList_Whole_by_factory($factory);
            }*/
        }
        
        if($totalrows > 0) $result['pagination'] = $this->make_pagination('index', $offset, $rows, $currpage, $totalrows);

        unset($db);
        
        
        $result['filter'] = '<div id="filter_tmp" class="dropdown">'. 
                                '<img src="/wagon_types/img/filter_512.png" id="show_filter"  class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" width="24" hieght="30" title="Фильтр">'.                
                                
                              // '<i class="fas fa-filter" id="show_filter" style="color:SlateBlue4;" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Фильтр по типу подвижного состава"></i>'.
                                '<div>'.
                                    //'<i class="fas fa-times-circle" title="Сбросить фильтр" onclick="drop_filter();" style="color:Salmon;"></i>'.
                                '</div>' .
                                '<div id="filter" class="dropdown-menu" aria-labelledby="dropdownMenuButton">'.
                                     //'{items}'.
                                     '<a  class="dropdown-item" onclick="get_form_multi(this);">По параметрам</a>'.
                                    // '<a  class="dropdown-item" onclick="get_form_year(this);">По году начала производства</a>'.
                                  //   '<a  class="dropdown-item" onclick="get_form_country(this);">По стране производства</a>'.
                                   //  '<a  class="dropdown-item" onclick="get_form_factory(this);">По заводу-изготовителю</a>'.
                                     '<hr style="margin:0;">' . 
                                     '<a class="dropdown-item" onclick="drop_filter();">Все модели (сбросить фильтр)</a>' .  
                                '</div>'.
                                
                           '</div>';
        
        
        $result['tbl'] = "<div id='mdls_list'>" .
                        "<table id='ta_mdls' class='table table-hover table-colored table-striped-alt m-0 y-border-no-t'>" .
                            "<thead class='h-0'>" .
                                "<tr class='y-border-no'>" .
                                    "<th class='y-border-no m-0 p-0'></th>" .
                                "</tr>" .
                            "</thead>" .
                            "<tbody class='y-border-no-t'>".
                                "{REPLASE_THIS}".
                            "</tbody>" . 
                        "</table>" .
                    "</div>";
        
        $rows = '';

        if(count($mdls_list) > 0){  
            foreach($mdls_list as $row){

                $rows .= "<tr id='tr_mdls-".$row['rid']."' data-type-rid='".$row['rid_type']."' onclick='mdl_click(this);'>" . //data-flg='".$row['flg']."'
                            "<td id='mdl_rid-".$row['rid']."' class='flt-item h-100 align-middle y-border-no-t y-cur-point y-row-bborder position-relative' style='padding:5px 5px;'>" .
                                $row['nm'].

                                "<div class='acts-panel position-absolute text-center invisible' style='top:0;right:0;width:auto;'>" .
                                    "<div class='acts-inner' style='height:auto;width:auto;'></div>" . 
                                "</div>" . 
                            "</td>" .
                         "</tr>";
            }
            $result['tbl'] = str_replace("{REPLASE_THIS}", $rows, $result['tbl']);  
        }
        else $result['tbl'] = '';
        
       // $result['tbl'] = str_replace("{REPLASE_THIS}", $rows, $result['tbl']);
        return $result;
    }
    
    public function show_execution($mdl_rid){
          
          $db = new wagon_types_db();
          $records = $db->get_executions_list($mdl_rid);
          $rowset = '';
          unset($db);
          $i=0;
          
          if(count($records) > 0){
              foreach($records as $row){
				if ($i > 0)
					$rowset .= "<div class='text-center m-0 p-0' style='color:silver;font-size:0.7rem;'>&bull;</div>";
				  
                $rowset .=  "<div id='exe_rid-".strval($row['rid'])."' class='y-wdt-col-12 ".($i%2 != 0 ? 'k-bg' : '')."' onclick='execution_click(this);' " . 
				 
								" style='padding-top:5px;padding-bottom:5px;'" .
                         
                              "data-flg='".$row['flg']."' data-factory='".$row['factory']."' data-country='".$row['country']."' ".
                              "data-spec='".$row['spec_n']."' data-desigDraw-techCon='".$row['desigDraw_techCon']."' ".
                              "data-comm-draw='".$row['comm_draw']."' data-w-base='".$row['w_base']."' data-w-length='".$row['w_length']."' " .
                              "data-b-length='".$row['b_length']."' data-b-width='".$row['b_width']."' data-b-height='".$row['b_height']."' ". 
                              "data-composition='".$row['composition']."' data-layout='".$row['layout']."' data-type-coupDev='".$row['type_coup_dev']."' ".
                              "data-tM-cart='".$row['tM_cart']."' data-weight-tare='".$row['weight_tare']."' data-payload='".$row['payload']."' " .
                              "data-size-bC='".$row['size_bC']."' data-quan-plcs='".$row['quan_sit_plcs']."' data-speed='".$row['constr_speed']."' ".
                              "data-EHTK='".$row['existType_EHTK']."' data-type-brake='".$row['type_brake']."' data-type-transDev='".$row['type_trans_dev']."'>" .      
                         
                                    "<span id='exe_nm-".strval($row['rid'])."' class='y-lgray-text y-fz010 y-wdt-col-12 k-pad-lr-5'>" . strval($row['nm'])."</span>" .  
                                    "<span id='exe_editDelete_tmp'>" .
                                     //   "<img src='/pktbbase/img/edit_24.png' widht='20' height='20' onclick='edit_exe(this);'>".
                                    //    "<img id='delete_exe-".$row['rid']."' src='/pktbbase/img/delete_24.png' widht='20' height='20' onclick='delete_exe_click(this);'>".
                                    "</span>".
                            "</div>";
                 
                 $i++;
              }
          }
          
            $result = "<div id='executions_list' class='card detail-card y-shad y-mrg-t10'>" . // y-mrg-b10
                        "<div class='card-header k-bg-header' id='header_executions_list'>".
                            "<h6><b>Исполнения</b></h6>" .
                        "</div>" .                                        

                            "<div class='card-body p-0'>" .	//k-pad-lr-5   y-pad-tb5
                                //"<div class='y-flex-row-wrap file-panel'>".
                    
                                    "<div class='y-wdt-col-12'>".      // y-pad-lr10   y-mrg-b10 
                                       "{executions}" .
                                    "</div>". 
                                                      
                                //"</div>".               
                            "</div>" .                                            
                    "</div>";
            
            $innerText = (strlen($rowset) > 0) ? $rowset : '';
            
            $result = str_replace("{executions}", $innerText, $result);
              
        return $result;
    }                                 
    
    public function show_execution_info($exe_rid){
        
        $db = new wagon_types_db();
        $records = $db->get_execution_record_by_self_rid($exe_rid); 
        
        $mdl_records = $db->mdl_record_by_self_rid($records['rid_mdl']);
        $mdl_nm = $mdl_records['nm'];
        
        $type_records = $db->type_record_by_self_rid($mdl_records['rid_type']);
        $type_nm = $type_records['nm'];
        unset($db);
        
        $rows = '';
        $rows_other = '';
        $name = (strlen($records['nm']) > 0) ? strval($records['nm']) : '';
        
        $td_class_l = "class='text-left align-middle y-border-no-t y-border-b y-gray-text y-fz08 position-relative k-td-title' "; //col-5
        $td_class_r = "class='text-left align-middle y-border-no-t y-border-b y-fz09 y-steel-blue-text k-td-val' ";
        
        $td_style_l = " style='padding:2px 10px 2px 20px;' ";
        $td_style_r = " style='padding:2px 10px;' ";
        
        $result = "<div id='detail_exe' class='card detail-card y-shad y-mrg-t10 position-relative'>" . //card detail-card
                        "<div class='card-header' id='header_exe_info'>".
                            "<h6 class='y-dgray-text' style='padding-top:5px;'><b>".$name."</b></h6>" . 
                        "</div>".

                   "</div>".
                
                
                    "<div id='main_info' class='card detail-card y-shad'>" .
                       "<div class='card-header y-align-items-center'>" . //y-flex-row-nowrap
                            "<div class='y-steel-blue-text'>Основная информация</div>" .
                        "</div>" .
                
                        "<div>" .                           
                              "<table class='table table-striped y-border-no m-0'>" .
                                "<thead class='h-0'>" .
                                    "<tr class='y-border-no'>" .
                                        "<th class='y-border-no m-0 p-0' colspan='2'></th>" .    //y-wdt-col-12                                 
                                    "</tr>" .
                                "</thead>" .
                
                                "<tbody class='y-border-no'>" .
                                    "<tr>" .
                                        "<td " . $td_class_l . $td_style_l . ">Тип -</td>" . //
                                        "<td " . $td_class_r . $td_style_r . ">" . $type_nm . "</td>" . //
                                    "</tr>".
        
                                    "<tr>" .
                                       "<td " . $td_class_l . $td_style_l . ">Модель - </td>" .
                                        "<td " . $td_class_r . $td_style_r . ">" . $mdl_nm . "</td>" .
                                    "</tr>" .
                                      
                                    "{exe:info}".  
                                "</tbody>" .
                              "</table>" .               
                            "</div>" .  
                
                        "</div>"  . // end of main_info

                 
                /*   "<div class='text-center y-mrg-b10 y-pad-lr20' style='color:silver;font-size:1.0rem;'>&bull;&nbsp;&bull;&nbsp;&bull;</div>" .  */
                
                 "<div id='other_info' class='card detail-card y-shad y-mrg-t10'>" . 
                
                    "<div class='card-header y-align-items-center'>" . //y-flex-row-nowrap
                        "<div class='y-steel-blue-text'>Прочая информация</div>" .
                    "</div>" .

                    "<div id='other_info_body'>" . 
                         "<table class='table table-striped y-border-no m-0'>" .
                           "<thead class='h-0'>" .
                                "<tr class='y-border-no'>" .
                                    "<th class='y-border-no m-0 p-0' colspan='2'></th>" .         //y-wdt-col-12                            
                                "</tr>" .
                            "</thead>" .

                          "<tbody class='y-border-no'>" .
                                "{exe:info_other}".  
                            "</tbody>" .
                          "</table>" .            
                                    
                    "</div>"  .
                
                "</div>"; // end of other_info
        
        if(count($records) > 0){                     
            $rows .=  "<tr>" .
                          "<td " . $td_class_l . $td_style_l . ">Наименование -</td>" . //
                          "<td " . $td_class_r . $td_style_r . ">" . $name . "</td>" . //
                      "</tr>".

                      $this->get_exe_info_div($records['country'], "Страна производства -") .
                      $this->get_exe_info_div($records['factory'], "Завод-изготовитель -") .
                      $this->get_exe_info_div($records['prod_ys'], "Год начала производства -"); 
         
            $rows_other .=  $this->get_exe_info_div($records['spec_n'],  			"Специализация -") .
                            $this->get_exe_info_div($records['desigDraw_techCon'], 	"Обозначение (номер) чертежа -") .
                            $this->get_exe_info_div($records['comm_draw'], 			"Чертеж общего вида с вариантом окраски -").
                            $this->get_exe_info_div($records['w_base'], 			"База вагона -") .
                            $this->get_exe_info_div($records['w_length'], 			"Длина вагона по осям автосцепок -") .
                            $this->get_exe_info_div($records['b_length'], 			"Длина кузова -") .
                            $this->get_exe_info_div($records['b_width'], 			"Ширина кузова -") .
                            $this->get_exe_info_div($records['b_height'], 			"Высота кузова от рельса до оси автосцепки -") .
                            $this->get_exe_info_div($records['composition'], 		"Составность -") .
                            $this->get_exe_info_div($records['layout'], 			"Планировка с размерами и наименованиями помещений -") .
                            $this->get_exe_info_div($records['type_coup_dev'], 		"Тип сцепного устройства, поглощающего аппарата -") .
                            $this->get_exe_info_div($records['tM_cart'], 			"Тип, модель тележек -") .
                            $this->get_exe_info_div($records['weight_tare'], 		"Mасса тары -") .
                            $this->get_exe_info_div($records['payload'], 			"Грузоподъёмность -") .
                            $this->get_exe_info_div($records['size_bC'], 			"Габарит кузова, тележек, очертание -") .
                            $this->get_exe_info_div($records['quan_sit_plcs'], 		"Количество мест (для пассажиров проводников для сидения) -") .
                            $this->get_exe_info_div($records['constr_speed'], 		"Конструкционная скорость -") .
                            $this->get_exe_info_div($records['existType_EHTK'], 	"Наличие, тип ЭЧТК, емкость -") .
                            $this->get_exe_info_div($records['type_brake'], 		"Тип тормоза -") .
                            $this->get_exe_info_div($records['type_trans_dev'], 	"Тип переходного устройства -") .
                            $this->get_exe_info_div($records['type_B_dev'], 		"Тип буксовых узлов -") .
                            $this->get_exe_info_div($records['systemElectr'], 		"Система электроснабжения (номер схемы, характеристика) -") .
                            $this->get_exe_info_div($records['type_gen'], 			"Тип генератора -") .
                            $this->get_exe_info_div($records['DGY'], 				"Наличие ДГУ -") .
                            $this->get_exe_info_div($records['syfle'], 				"Суфле -") .
                            $this->get_exe_info_div($records['video'], 				"Система видеонаблюдения -") .
                            $this->get_exe_info_div($records['type_drive'], 		"Тип привода -") .
                            $this->get_exe_info_div($records['vent_cond'], 			"Система вентиляции, кондиционирования воздуха -") .
                            $this->get_exe_info_div($records['air_decon'], 			"Наличие обеззараживателя воздуха -") .
                            $this->get_exe_info_div($records['aqua_decon'], 		"Наличие обеззараживателя воды -") .
                            $this->get_exe_info_div($records['F_MV_B_C'], 			"Наличие холодильника, МВ печи, кипятильника, охладителя питьевой воды -") .
                            $this->get_exe_info_div($records['BRISS'], 				"Наличие БРИСС -") .
                            $this->get_exe_info_div($records['sys_heat'], 			"Cистема отопления, наличие системы жидкостного отопления -") .
                            $this->get_exe_info_div($records['cert'], 				"Сертификат соответствия (при наличии) -") .
                            $this->get_exe_info_div($records['serv_life'], 			"Назначенный срок службы -") .
                            $this->get_exe_info_div($records['runs'], 				"Mежремонтные периоды (сроки) и пробеги в соответствии с КД -");
        }
        
        $result = str_replace("{exe:info}", $rows, $result);
        $result = str_replace("{exe:info_other}", (strlen($rows_other) > 0 ? $rows_other : "<div id='no_other_info' class='text-center mt-10 p-0 y-steel-blue-text'>Данные отсутствуют</div>"), $result);          
       
        return $result;         
    }  
	
    public function get_exe_info_div(string $field, string $title) : string {
        $td_class_l = "class='text-left align-middle y-border-no-t y-border-b y-gray-text y-fz08 position-relative k-td-title' ";
        $td_class_r = "class='text-left align-middle y-border-no-t y-border-b y-fz09 y-steel-blue-text k-td-val' ";
        
        $td_style_l = " style='padding:2px 10px 2px 20px;' ";
        $td_style_r = " style='padding:2px 10px;' ";
        
        return  (mb_strlen($field) > 0 && preg_match('#^-$#', $field) != 1 ?
                            "<tr>" .
                                "<td " . $td_class_l . $td_style_l . ">".$title."</td>" . //
                                "<td " . $td_class_r . $td_style_r . ">" . $field . "</td>" . //
                            "</tr>" : '');
    }
    
    public function receive_docs($pid){
       $db = new wagon_types_db();
       $records = $db->get_docs_by_pid($pid); 
      // $exe_record = $db->get_execution_record_by_self_rid();
        unset($db);  
        
        $panels = '';
           
        $result =  "<div id='docs_block' class='card detail-card y-shad y-mrg-t10' style='min-height:200px;'>" .
                    "<div class='card-header k-bg-header' id='header_docs'>".
                        "<h6 class='y-steel-blue-text'><b>Документация</b></h6>" .
                    "</div>".

                    "<div class='card-body y-pad-tb5'>" .
                        "<div class='file-panel'>".
                           "{cards}".
                        "</div>".               
                    "</div>" .
            "</div>";

        if(count($records) > 0){
      
            foreach($records as $row){

                $ftype = _assbase::getFtypeByFname($row['fnm']); 

                $img = $ftype == "unk" ? "" :
                                "<img class='display-block' src='/pktbbase/img/file/" . $ftype . "_64.png'>";

                $fnm_ttip = mb_strlen($row['fnm']) > 30 ? "data-toggle='tooltip' title='" . $row['fnm'] . "' data-delay='100'" : "";

                $fnm = "<p class='y-gray-text' " . $fnm_ttip . ">" . 
                                    _dbbase::shrink_filename($row['fnm'], 30) . "</p>"; 
                
                //substr(строка, откуда, [сколько]);
                //$str_footer = (mb_strlen($row['fnm']) > 7) ?  mb_substr($row['fnm'], 0, 10) . "." : $row['fnm'];
				$str_footer = "&nbsp;";
                
                $card_footer =  "<div class='card-footer y-flex-row-nowrap justify-content-between y-align-items-center p-0' style='height:2.5rem;'>" .
                                    "<div style='width:22px;'>&nbsp;</div>" .   // 22px - experimental for org show at footer center                      
                                    "<div class='p-0 y-llgray-text'>".$str_footer."</div>" .
                                    "<div class='dropdown' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>" .
                                       //  "<img id='a_delete_doc-" . $row['rid'] . "' src='img/delete_15.png' onclick='delete_doc_click(this);' " .
                                         //   "data-fnm='".$row['fnm']."' data-pid='".$pid."' data-flg='".$row['flg']."'>".                                        
                                    "</div>" .
                                "</div>";
     
                $panels .=  "<div id='doc_rid-'".$row['rid']." class='card file-card y-shad-light' data-pid='".$pid."'>" . //style='min-width:190px;'
                                "<div class='card-body y-pad-tb5 y-pad-lr10 text-center y-cur-point' onclick='doc_view_click(this);' " .
									"data-doc='" .$row['rid']."'>" . //text-justify  text-center card-body
                                   $img . $fnm .                                                 
                                "</div>" .
                            //    $card_footer .
                            "</div>";
            }            
        }
       $result = str_replace("{cards}", $panels, $result);
       return $result; 
    }

    public function show_types(){
        $db = new wagon_types_db();
        $types_list = $db->typesList_Whole();
        unset($db);
        
        $result = "<div id='types_list'>" .
                                    "<table id='ta_type' class='table table-hover table-colored table-striped-alt m-0 y-border-no-t'>" .
                                        "<thead class='h-0'>" .
                                            "<tr class='y-border-no'>" .
                                                "<th class='y-border-no m-0 p-0'></th>" .
                                            "</tr>" .
                                        "</thead>" .
                                        "<tbody class='y-border-no-t'>".
                                            "{REPLASE_THIS}".   
                                        "</tbody>" .
                                    "</table>" .
                                "</div>";
        
        $rowset = '';
 
        foreach($types_list as $row){
                        
               $rowset .= "<tr class='y-border-no' id='tr_type-".$row['rid']."' data-flg='".$row['flg']."' onclick='type_click(this);'>" .
                               "<td id='type_rid-".$row['rid']."' class='flt-item h-100 align-middle y-border-no-t y-cur-point y-row-bborder position-relative' style='padding:5px 5px;'>" .
                                   $row['nm'] .
                       
                                    "<div class='acts-panel position-absolute text-center invisible' style='top:0;right:0;width:auto;'>" .
                                        "<div class='acts-inner' style='height:auto;width:auto;'></div>" .  
                                    "</div>" .
                               "</td>" .
                          "</tr>";
        }
        $result = str_replace("{REPLASE_THIS}", $rowset, $result); 
        
        return $result;
    }
}

// End of: assist
/*

$db = new wagon_types_db();
   $mdl_list = $db->mdl_record_by_self_rid('310162d9-d509-4964-b15c-d48c1d112c3b');        
        
        if(count($mdl_list) > 0){
           $mdl_nm = $mdl_list['nm'];
           $rid_owner_mdl = $mdl_list['rid_type'];
        }   
        
 $owner_records = $db->type_record_by_self_rid($rid_owner_mdl);   
 
           $nm_owner_mdl = strval($owner_records['nm']);
 
var_dump($nm_owner_mdl);
*/

//$ass = new assist();
//var_dump($ass->get_fm('select_mdls_by_type'));
?>

