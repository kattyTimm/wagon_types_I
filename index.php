<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php
        if (strlen(trim(session_id())) == 0) session_start();
        ?>
        
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">

        <title>ЦБ ППС Пользователь</title>

        <!-- Favicon: https://stackoverflow.com/questions/30824294/mobile-favicons -->
        <link rel="apple-touch-icon" sizes="128x128" href="/wagon_types/img/favicon/train_128.png">
        <link rel="icon" type="image/png" href="/wagon_types/img/favicon/train_64.png" sizes="48x48">
        <link rel="shortcut icon" href="/wagon_types/img/favicon/train_64.ico">
        <meta name="msapplication-TileImage" content="/wagon_types/img/favicon/train_128.png">
        <meta name="msapplication-TileColor" content="#FFFFFF">
        
        <link href="/site_lib/lib/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        
        <link href="/pktbbase/css/bootstrap.min.css" rel="stylesheet">
        <link href="/pktbbase/css/mprogress-gr.css" rel="stylesheet">
        <link href="/pktbbase/css/colorbox.css" rel="stylesheet"/>

        <link href="/pktbbase/css/_indx_root.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_comm.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_drag.css" rel="stylesheet"/>
        <link href="/pktbbase/css/_indx_srch.css" rel="stylesheet"/>

        <link href="css/index.css" rel="stylesheet">
    </head>
    <body spellcheck="false" ondrop='return false;' ondragover='return false;'>
        
        <?php
        include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';
        include_once 'php/assist.php';
        
        $current_browser = get_browser(null, true);
        if (strcasecmp($current_browser['browser'], 'ie') == 0 && intval($current_browser['majorver']) < 10) {
            echo "<div class='text-center align-middle' style='padding:20px;color:white;background:red;line-height:40px;'>Версии браузера Internet Explorer ниже 10 не поддерживаются!</div>";
            exit();
        }
        ?>

        <nav id="navbar_wrap" class="navbar navbar-expand navbar-light y-fixtop" style="background-color: rgba(82, 179, 217, 0.2);">  <!-- fixed-top -->
			<!--
            <div>
                <span id="favicon" class="d-inline-block">
                   <img src="/wagon_types/img/favicon/train_128.png" width="92" height="92">
                </span>&emsp;
                <span id="main_title" class="navbar-brand d-inline-block"><b>ЦБ ППС</b> <i class="y-steel-blue-text y-ver">Пользователь</i></span>
            </div>
			-->

            <span id="main_title" class="navbar-brand d-inline-block">
               <img src="/wagon_types/img/favicon/train_56.png" width="auto" height="36" style="margin-bottom:3px;">
			   &nbsp;
			   <b>ЦБ ППС</b> <i class="y-steel-blue-text y-ver">Пользователь</i>
			</span>
            
            <div class="input-group-k">
              <!--  <div id="tmp_filter_items"></div>
                         <img src="img/filter_128.png" width="24" hieght="20" title="Фильтр по типу подвижного состава" > onclick="show_filter_by_types(this);" -->

                <div id="search_tmp">
                    <input id="srch_box" type="text" placeholder="Поиск <по модели>" ondrop="return false;" ondragover="return false;" class="form-control">
                </div>
            </div>
        </nav>
        
        <div class="content-wrapper">
            <div id="content_lists">
                
                <div id="content_types" class="card flt-card flt-card-rw y-shad">
                    <div id="content_types_head" class="card-header k-bg-header"> <!-- y-steel-blue-text -->
                        <div id="tmp_filter_h6">
                            <div id="tmp_filter_items"></div>
                            <h6><b>Типы подвижного состава</b></h6> 
                        </div>                      
                    </div>
                    <div id="content_types_body"></div>    	<!-- y-flex-column-nowrap  -->
                </div>

                <div id="content_mdls" class="card flt-card flt-card-rw y-shad">
                    <div id="content_mdls_head" class="card-header k-bg-header">

                           <h6><b>Модели</b></h6>                    
                    </div>
                    <div id="content_mdls_body" class="noselect"></div>    <!--  y-flex-column-nowrap    mt-auto div in assist -->
                  
                </div>               
            </div>  
            
            <div id="executions_block"></div>
            
            <div id="content_info" class="d-none">
                <div id="exe_info"></div>
                <div id="docs"></div>
            </div>
        </div>
        
        <div id="pagination" class=""></div>
   
        <div id="foot_fst"><div id="fst_show" class="d-none p-0"></div></div>
       
        <footer class="sticky-footer y-flex-row-nowrap justify-content-between align-items-center bg-dark">
            <div id='foot_left_section'><span id='foot_left_info' class="y-gray-text">
                <!--<span id='foot_fst'></span>-->
                <span id='foot_partname' class='d-inline-block'>
                    <!--<i class="far fa-window-restore" style="color:#A6A653;"></i> &nbsp;-->
                    <span id="type_partname" data-app="type"></span>  <!--Пользователь-->  
                    <img src="/wagon_types/img/train_white_right_512_2.png" width="32" height="32">
                </span>
                <span id="nx_clnf_fio" class="y-lgray-text"></span></span>
            </div>
            <div id='foot_client_info' class="y-lgray-text y-fz08"></div>
            <div id='foot_right'>
                <span class="y-whitegray-text foot-copyright d-inline-block">
                    <span id='foot_copyright_str' class="y-steel-blue-text"> &nbsp;<?php echo assist::$copyright_str; ?> </span>
                    <span id='foot_copyright_float'></span>
                    <!--<span id='foot_fst' class="y-mrg-lr5"></span>-->
                  <!--  <img id="foot_help" src='/nxcomm/img/help_36.png' class="y-cur-point" onclick="_help.help_start(this);" data-toggle='tooltip' title='Справка'> -->
                </span>
            </div>
        </footer>
        
        
        <div class="y-ajax-wait"></div>   <!-- Waiting cursor -->
        
        <!--<img src='/nxcomm/img/control/win8_24.gif' class="y-dsrc-wait d-none">-->

        <div id="div_tmp"></div>
        <div id="div_tmpx"></div>
        
        <a id='a_download' href='javascript:;' class='d-none' download></a>
        
        <div id="pktb_appid" class="d-none" data-appnm="dmgnPsIns" data-applvl="С" data-oneid="153" data-helpid="153"><?php echo _assbase::app_id('wagon_types_Ins');?></div> 

        <?php _assbase::checkSttv('wagon_types_Ins'); ?>        
        
        <script src="/pktbbase/js/jquery.min.js"></script>
        <script src="/pktbbase/js/bootstrap.bundle.min.js"></script>

        <script src="/pktbbase/js/purl.min.js"></script>
        <script src="/pktbbase/js/jquery.noty.packaged.min.js"></script>
        <script src="/pktbbase/js/mprogress.min.js"></script>
        <script src="/pktbbase/js/bootstrap4-toggle.min.js"></script>
      
        <script src="/pktbbase/js/md5.min.js"></script>
        <script src="/pktbbase/js/jquery.colorbox-min.js"></script>
  
        <script src="/pktbbase/js/bootstrap-datepicker.min.js"></script>
        <script src="/pktbbase/js/bootstrap-datepicker.ru.min.js" charset="UTF-8"></script>
        <script src="/pktbbase/js/jquery.touchSwipe.min.js"></script>

        <script src="/pktbbase/js/jquery.autocomplete.corrected-me.min.js"></script>
        
        <script src="js/index.js"></script>
    </body>
</html>
