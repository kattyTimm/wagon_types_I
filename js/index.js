_index = {   // interface to index.js
   is_valid_user:        function ()                { return is_valid_user(); },
    validate_ok:          function (ead, save_ead)   { validate_ok(ead, save_ead); },

    app_clnf:             function ()                { return app_clnf(); },
    app_ead:              function ()                { return app_ead(); },
    app_orgs:             function ()                { return app_orgs(); },
    app_orgsnm:           function ()                { return app_orgsnm(); },
    
    app_who:              function ()                { return app_who(); },
    app_pdfViewerType:    function ()                { return app_pdfViewerType(); },
    
    //view_data_changed:    function (orgs_to)         { view_data_changed(orgs_to); },   // for after fm_loader
    
    fill_sideout:         function ()                { fill_sideout(); },
  
   
   docget_ok_click:      function(data_sec, data_rid, doc_nm, doc_flg){docget_ok_click(data_sec, data_rid, doc_nm, doc_flg); },
   glob_fm_before_show:  function ()                { glob_fm_before_show(); },
   glob_fm_after_show:   function ()                { glob_fm_after_show(); },
   pg_performGo:         function (pg_id)           { pg_performGo(pg_id); }
}

$(function() {      // Shorthand for $(document).ready(function() {
    "use strict";
    
    window.addEventListener("popstate", function() { 
        if ($(".modal.show").length > 0)
            $(".modal.show").modal('hide');
    });
    
    $(window).resize(function() {
        measure_listsBody();
        measure_contentBody();
        
    });
    $(document).mouseup(function(e) {
        if ($(".popover.outhide").length > 0)
            $(".popover.outhide").each(function () {
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0) $(this).popover("dispose");
            });
            
            var sideout = $("#sideout");
        
        if (sideout.hasClass("active")) {
            // if the target of the click isn't button or menupanel nor a descendant of this elements
            if (!sideout.is(e.target) && sideout.has(e.target).length === 0)
               _sout.hide_sideout();
        }
            
        _common.close_tooltips();
    });
    
     $("#sideout").swipe( {
        swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
          if (direction == "left")
              _sout.hide_sideout();
        }
    });

    /*
    $(window).keydown(function(e){
        if (e.keyCode === 8) { // Backspace
            // Backspace in browsers used for 'Back' navigate.
            // See here: https://stackoverflow.com/questions/1495219/how-can-i-prevent-the-backspace-key-from-navigating-back
            var $target = $(e.target||e.srcElement);
            if (!$target.is('input,[contenteditable="true"],textarea'))
                e.preventDefault();
        }
        else check_keyDown(e);
    });
    */
    $.when($.getScript("/pktbbase/js/_common.js") ,
           $.getScript("/pktbbase/js/_fdb.js",
           $.getScript("/pktbbase/js/_help.js"),
           $.getScript("/pktbbase/js/_bbmon.js")),
           $.getScript("/pktbbase/js/_viewer.js"),
           $.getScript("/pktbbase/js/_sout.js"),
           $.getScript("/pktbbase/js/_docget.js")
           )
        .done(function () {
            //???????????????
            window.active_dsrc_ajax_orgs_id = "NONE";  // for prevent double dsrc ajax (load_vw_dsrc). Can't use '' - it's CL id.
            
            // all synchronous functions here
            set_initials();
            
            $("#content_types").removeClass("d-none");
            $("#content_info").removeClass("d-none");

            set_clnf_vars();        // load_mdls here
        });
        
}); // End of use strict

function set_initials() {
  
    setMainPanelsSz(); //_common.getStoredLocalInt('nxc_orgs_panel_sz')
}

function setMainPanelsSz() { 
   var orgs_sz = 20;
            
    //$("#content_types").css({ 'flex': '0 0 ' + orgs_sz + '%', '-ms-flex': '0 0 ' + orgs_sz + '%' });
    //$("#content_mdls").css({ 'flex': '1 0 ' + orgs_sz + '%', '-ms-flex': '1 0 ' + orgs_sz + '%' });            
    measure_contentBody();
}

// user validate section
function is_valid_user()   { return _common.getStoredSessionInt('itr') || _common.getStoredSessionInt('ptr'); }    // clinfO version

function validate_ok(ead, save_ead) {
    _common.storeLocal('nxc_login_ead', ead);
    _common.storeLocal('nxc_save_ead', Number(save_ead))
    
    _common.storeSession('nxc_clnf_ead', ead);

    set_clnf_vars();        // recreate_page here
}
// end: validate


function measure_contentBody() {
    var nav_h = $(".navbar").offset().top + $(".navbar").outerHeight(true);
    
    $(".content-wrapper").css({ 'top': nav_h + 'px' });

 //   $(".orgs-card-cl").css('width', $(".orgs-card-any").innerWidth() + 'px' );
 //   $("#content_types_head").css('width', $(".orgs-card-cl").outerWidth(true) + 'px' );
    
    
     /*PDV. еще добавил этот обработчик в show_active_execution_info()*/
    var detail_exe_h = $("#exe_info").innerHeight() - 20;	
    
    $("#other_info").css({ 'height': (detail_exe_h - $("#main_info").innerHeight() - 80) + 'px' }); 
    $("#docs_block").css({ 'height': detail_exe_h + 'px' });
	
    $("#executions_list").css({ 'height': detail_exe_h + 'px' });  
    
    var content_mdls_h = $("#content_lists").innerHeight() - $("#content_types").outerHeight(true) - 30;	// 30: experimental
    $("#content_mdls").css({ 'height': content_mdls_h + 'px' });
    
}

/*
function measure_contentBody() {
    var nav_h = $(".navbar").offset().top + $(".navbar").outerHeight(true);
    
    $(".content-wrapper").css({ 'top': nav_h + 'px' });

    $(".orgs-card-cl").css('width', $(".orgs-card-any").innerWidth() + 'px' );
    $("#content_types_head").css('width', $(".orgs-card-cl").outerWidth(true) + 'px' );
    
    
     // PDV. еще добавил этот обработчик в show_active_execution_info()
    var detail_exe_h = $("#exe_info").innerHeight() - 20;	// 30: experimental
    $("#detail_exe").css({ 'height': detail_exe_h + 'px' });
    $("#docs_block").css({ 'height': detail_exe_h + 'px' });
	
    $("#executions_list").css({ 'height': detail_exe_h + 'px' });
    $("#content_mdls").css({ 'height': detail_exe_h + 'px' });            
}
*/

function measure_listsBody() {
    /*PDV */
    $('#content_mdls_body').css('height', ($('#content_lists').offset().top + $('#content_lists').innerHeight() - 
    $('#content_mdls_head').offset().top - $('#content_mdls_head').outerHeight(true)) + 'px');
}

       
function reset_clnf_vars(){
    _common.storeSession('cip', '');
    _common.storeSession('itr', ''); 
    _common.storeSession('ptr', '');
};

function clear_all() {
    reset_clnf_vars();
    
    $('#content_types_body').empty();
    $('#content_mdls_body').empty();
    $('#content_info').empty();
       
    //recreate_page();
}

function set_clnf_vars() {
    reset_clnf_vars();
    
    var postForm = {
      'part'  : 'get_app_globals' 
    };

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {
           // recreate_page();
        },
        success   : function(data) {    // always success
                        _common.storeSession('cip', data.cip); //data.ip
                        
                        _common.storeSession('itr', data.itr); //data.org      370dd604-806a-4406-a907-e69f536e8cb3
                        _common.storeSession('ptr', data.ptr);          
                        
                       // if (((flg >> 1) & 0x1) == 1){ // flg >> 1) & 0x1, >> означает сдвиг вправо на 1 бит, и если он равен 1, то загрузить страницу
                            recreate_page();
                           
                           // load_mdls(_common.getStoredSessionStr('present_type'));
                          
        }
    });
} 

function ta_refreshRows(table_id) {

    if ($('#ta_' + table_id).length > 0) {     
        $("[id^='tr_" + table_id + "-']")
            .mouseenter(function() {
                _common.swipe_showMenu($(this));
            })
            .mouseleave(function() {
                _common.swipe_hideMenu();
            });  
    }
    
    _common.refresh_tooltips();
}


function select_search_item(rid) {  // ex: rws:879876876.... перескакивает на нужное место в таблице
    
    drop_filter();
    
    var postForm = {
       'part' : 'search_get_mdls_row',
       'rid'  : rid
    };

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        success   : function(data) {
                               
                        if (data.success) {
                            // $$_scroll_to_id - глобальная переменная, используется в _common.process_scrollToID();
                            $$_scroll_to_id = '#mdl_rid-' + rid;  
                            
                           _common.storeSession('present_mdl', rid);

                           if (data.rownum >= 0){ 
                                start_gopage('index', Math.floor(data.rownum/10));  
                            }  
                        }
        }
    });
}



function start_gopage(pg_id, new_page) {
   _common.storeSession(pg_id + '_last_page', new_page);

   load_mdls(_common.getStoredSessionStr('present_type'));
}

function pg_performGo(pg_id) {  // function is called when pagination link is clicked
    switch (pg_id) {
        default:
            load_mdls(_common.getStoredSessionStr('present_type'));
         //   recreate_page();
    }
}


function recreate_page() {
            //  теперь без регистрации
 //   if(_common.getStoredSessionStr('cip').length > 0){
        var postForm = {
           'part'      : 'load_types_tbl'
        };

        $.ajax({
            type       : 'POST',
            url        : 'php/jgate.php',
            data       : postForm,
            dataType   : 'json',
         //   localCache : true,
        //    cacheKey   : vw_id,
            beforeSend: function() {
               $('#content_types_body').empty();
               
            },
            complete: function() {
                set_initials();
                $('#type_rid-' + _common.getStoredSessionStr('present_type')).addClass('tbl-act-cell'); 
                measure_listsBody();
              
                load_mdls(_common.getStoredSessionStr('present_type'));
            },
            error: function (jqXHR, exception) {
                //$('#content_orgs_body').html(_common.err_span());
            },
            success   : function(data) {
                            if (data.success) {
                              
                               $('#content_types_body').html(data.body);
                            }
                            ta_refreshRows('type');

                        }
        }).always(function () {
          //  measure_orgsBody();
        });
  //  }else clear_all();
    
}


function check_active_type(){   
    var present_type = _common.getStoredSessionStr('present_type');
    
    if(present_type.length == 0 || $('#type_rid-'+present_type).length == 0){
        
        present_type = _common.value_fromElementID($('[id^="type_rid-"]').first().attr('id'));
        _common.storeSession('present_type', present_type);
        
        select_active_type(present_type);
    }
    else select_active_type(present_type);
}

function select_active_type(rid){     
    $("[id^='type_rid-']").removeClass('tbl-act-cell');
   // $('#type_rid-' + rid).addClass('tbl-act-cell');      

// раньше не было
      set_clnf_vars();
}

/*
function type_click(e){
    var rid = _common.value_fromElementID($(e).attr('id'));
     _common.storeSession('present_type', rid);
     check_active_type();
}
*/


function type_click(e){
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('country', ''); 
   _common.storeSession('factory', ''); 
    
    var rid = _common.value_fromElementID($(e).attr('id'));
    _common.storeSession('present_type', rid);
    check_active_type();
}


function load_mdls(rid_type){ 
  
    var lastpage = _common.get_currentLastpage('index');
    var pg = 'index';
    rid_type = (rid_type.length > 0) ?  rid_type : '';

    var postForm = {
       'part'      : 'load_mdls_tbl',
        'offset': lastpage * 10, // 24
        'rows': 10,
        'currpage': lastpage,
        'rid_type' : rid_type,
        'from': _common.getStoredSessionStr('from'),
        'to': _common.getStoredSessionStr('to'),
        'country': _common.getStoredSessionStr('country'),
        'factory': _common.getStoredSessionStr('factory')
    };
    
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        beforeSend: function() {
           $('#content_mdls_body').empty();
           setTimeout(function(){ setMainPanelsSz();}, 200);  
        },
        complete: function() {
            check_active_mdl();
        },
        success: function(data){
            if (data.success) {
                $('#tmp_filter_items').html(data.filter);
                $('#pagination').html(data.pagination);
                $('#content_mdls_body').html(data.body);

             //  if(_common.getStoredSessionStr('from').length > 0)
                   // $('#pagination').empty();
                
                 ta_refreshRows('mdls');   
                 
                   if ($('.' + pg + '-pagination').length == 0)
                    _common.storeSession(pg + '_last_page', -1);
                    else {
                        var li_id = '#' + pg + '_li_to_page-' + lastpage;

                        $(li_id + '.active').removeClass('active');
                        $(li_id).addClass('active');                                     
                   } 

                     _common.process_scrollToID();

                     $('#srch_box')
                                .autocomplete({
                                    serviceUrl: '/wagon_types/php/mdls_search.php',
                                    paramName:  'srch_box',
                                    autoSelectFirst: true,
                                    //maxHeight: 350,
                                    triggerSelectOnValidInput: false,   // block onselect firing on browser activate
                                    showNoSuggestionNotice: true,
                                    noSuggestionNotice: 'Совпадений не найдено',
                                    minChars: 2,
                                    //lookupLimit: 100,
                                    onSelect: function (suggestion) {

                                        select_search_item(suggestion.data.trim()); // suggestion.data: rid, suggestion.value: pname

                                        $('#srch_box').val('');   //  и есть контекст
                                    }
                                }); 
                                
                 if(data.body.length == 0){
                    _common.say_noty_err("По заданным параметрам моделей не существует");

                    $('#pagination').html(data.pagination);
                    $('#content_mdls_body').html(data.body);
                    $('#executions_block .card-body').empty();
                    $('#exe_info').empty();
                    $('#docs').empty();  
                 }                                                
            }
             _common.refresh_tooltips();
        }
    });
}


function check_active_mdl(){
   var present_mdl = _common.getStoredSessionStr('present_mdl');      
   
   if(present_mdl.length == 0 || $('#mdl_rid-'+present_mdl).length == 0){
       
       if($('[id^="mdl_rid-"]').first().length == 0){
           $('#exe_info').empty();
           $('#docs').empty();
           $('#executions_list .card-body').empty();
       }      
       
       present_mdl = _common.value_fromElementID($('[id^="mdl_rid-"]').first().attr('id'));
           
       
        _common.storeSession('present_mdl', present_mdl);
        
        select_active_mdl(present_mdl);
    }
    else select_active_mdl(present_mdl); 
   
}

function select_active_mdl(rid){

    $("[id^='mdl_rid-']").removeClass('tbl-act-cell');
    $('#mdl_rid-' + rid).addClass('tbl-act-cell');
    
    show_active_mdl_executions(rid);
}

function mdl_click(e){
  
    var rid = _common.value_fromElementID($(e).attr('id'));
    _common.storeSession('present_mdl', rid);
    check_active_mdl();
}

function check_active_exe(){
   var now_exe = _common.getStoredSessionStr('now_exe');      
   
   if(now_exe.length == 0 || $('#exe_rid-'+now_exe).length == 0){
       var elem = $('[id^="exe_rid-"]').first();
      
       if(elem.length == 0){
           now_exe = null;

           $('#exe_info').empty();
           $('#docs').empty();
       }else{
            now_exe = _common.value_fromElementID(elem.attr('id'));

            _common.storeSession('now_exe', now_exe);

            select_active_execution(now_exe);
       } 

    }
    else select_active_execution(now_exe);
}

function select_active_execution(rid){
    $('[id^="exe_rid-"]').removeClass('tbl-act-cell');
    $('#exe_rid-'+rid).addClass('tbl-act-cell');
    
    show_active_execution_info(rid);
    show_docs(rid);
}

function execution_click(e){
    _common.storeSession('now_exe', _common.value_fromElementID($(e).attr('id')));
    check_active_exe();
}

function show_active_mdl_executions(rid){
    var postForm = {
       'part'      : 'load_execution',
       'rid_mdl' : rid
    };
    
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        beforeSend: function() {
 
        },
        complete: function() {
            check_active_exe();
        },
        success: function(data){
            if (data.success) {
                $('#executions_block').html(data.body); 
            }
             _common.refresh_tooltips();
        }
    });
}


function show_active_execution_info(rid){

    var postForm = {
       'part'      : 'load_exe_info',
       'rid' : rid
    };
    if(rid.length > 0){
        $.ajax({
            type       : 'POST',
            url        : 'php/jgate.php',
            data       : postForm,
            dataType   : 'json',
            beforeSend: function() {
               //setTimeout(function(){ setMainPanelsSz();}, 200);	// PDV 25.06 block
            },
            complete: function() {
                // setTimeout(function(){ setMainPanelsSz();}, 200);
            },
            success: function(data){

                if (data.success) {
                    $('#exe_info').html(data.body); 
					
					measure_contentBody();	// PDV 25.06
                }
                 _common.refresh_tooltips();
            }
        });
    }else $('#exe_info').html(''); 
}

function show_docs(pid){
    var postForm = {
        'part': 'get_docs',
        'pid': pid
    };
           
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        beforeSend: function() {
 
        },
        complete: function() {
        },
        success: function(data){
            if (data.success) {
                $('#docs').html(data.body); 
				
				measure_contentBody();	// PDV 25.06
                
            }
          //   _common.refresh_tooltips();
        }
    });
}

function doc_view_click(e){

    _common.close_dropdowns();
    _common.stop_propagation(e);
    
    $(e).tooltip('hide');
    
    _viewer.viewer_view("file_put_tmp", "rid", $(e).attr('data-doc'), false);
}


function glob_fm_before_show() {
      // $("#div_ta_registr").css({ 'overflow-y': 'hidden' });  // IE can show scrollbar over modal
}

function glob_fm_after_show() {
         //   $("#div_ta_registr").css({ 'overflow-y': 'auto' });
}


/************************* filter  *************************///////////


function drop_filter(){

   _common.storeSession('present_type', '');
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('country', ''); 
   _common.storeSession('factory', '');
   
   set_clnf_vars();  
}

function get_form_multi(e){
   _common.storeSession('present_type', '');
    
    var postForm = {
       'part' : 'get_fm',
       'fm_id' : 'select_mdls_by_multi'
   };
   
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
            $('#div_tmp').empty().html(data.html);
                
            $('#select_mdls_by_multi').attr('data-rid', '')
           .on('show.bs.modal', function () {      
               
                $('#multi_ok').click(function(){
                    send_multi_params();
               });

           }).on('shown.bs.modal', function () {

              
           }).modal('show');
        }
    });
}


function send_multi_params(){  
    _common.storeSession('from', $('#from_year option:selected').text());
    _common.storeSession('to', $('#to_year option:selected').text());
    _common.storeSession('country', $('#countries_list option:selected').text()); 
    _common.storeSession('factory', $('#factories_list option:selected').text());

    $('#select_mdls_by_multi').modal('hide'); 
    set_clnf_vars();  

}



/*
function get_mdls_4_type(e){
    _common.storeSession('present_type', $(e).attr('id'));   

    set_clnf_vars();  

    setTimeout(function() { $('#select_mdls_by_type').modal('hide')}, 400);
}

function drop_filter(){

   _common.storeSession('present_type', '');
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('country', ''); 
   _common.storeSession('factory', '');
   
   set_clnf_vars();  
}

function get_form_type(){
    
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('country', '');  
   _common.storeSession('factory', '');
    
   var postForm = {
       'part' : 'get_fm',
       'fm_id' : 'select_mdls_by_type'
   };
   
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
        
            $('#div_tmp').empty().html(data.html);
                
            $('#select_mdls_by_type').attr('data-rid', '')
           .on('show.bs.modal', function () {                    

           }).on('shown.bs.modal', function () {

              
           }).modal('show');
        }
    });
}

function get_form_year(){
   _common.storeSession('present_type', '');  
   _common.storeSession('country', '');  
   _common.storeSession('factory', '');
   
   var postForm = {
       'part' : 'get_fm',
       'fm_id' : 'select_mdls_by_year'
   };
   
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
            $('#div_tmp').empty().html(data.html);
                 
            $('#select_mdls_by_year').attr('data-rid', '')
           .on('show.bs.modal', function () {                    

               $('#select_year_ok').click(function(){
                    send_years_data();
               });
               
                   
           }).on('shown.bs.modal', function () {

              
           }).modal('show');
        }
    });
}

function send_years_data(){
    var from = $('#from_year').val();
    var to = $('#to_year').val();

    if(from.length > 0 && to.length > 0){
        if(Number(from) <= Number(to)){

            _common.storeSession('from', $('#from_year option:selected').text());
            _common.storeSession('to', $('#to_year option:selected').text());
            set_clnf_vars(); 
            $('#select_mdls_by_year').modal('hide'); 

         } else _common.say_noty_err("Значение первого поля не может быть больше второго");
    }else _common.say_noty_err("Года производства должны быть выбраны!");
}


function get_form_country(){
    
   _common.storeSession('present_type', '');  
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('factory', '');
   
   var postForm = {
       'part' : 'get_fm',
       'fm_id' : 'select_mdls_by_country'
   };
   
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
            $('#div_tmp').empty().html(data.html);
                 
            $('#select_mdls_by_country').attr('data-rid', '')
           .on('show.bs.modal', function () {                    

               $('#select_country_ok').click(function(){
                    send_country_data();
               });
               
                   
           }).on('shown.bs.modal', function () {

              
           }).modal('show');
        }
    });
}

function send_country_data(){
    _common.storeSession('country', $('#countries_list option:selected').text());
    set_clnf_vars(); 
    $('#select_mdls_by_country').modal('hide'); 
}

function get_form_factory(){
   _common.storeSession('present_type', '');  
   _common.storeSession('from', '');
   _common.storeSession('to', ''); 
   _common.storeSession('country', '');
   
   var postForm = {
       'part' : 'get_fm',
       'fm_id' : 'select_mdls_by_factory'
   };
   
    $.ajax({
        type       : 'POST',
        url        : 'php/jgate.php',
        data       : postForm,
        dataType   : 'json',
        success: function(data){
           
            $('#div_tmp').empty().html(data.html);
                 
            $('#select_mdls_by_factory').attr('data-rid', '')
           .on('show.bs.modal', function () {                    

               $('#select_factory_ok').click(function(){
                    send_factory_data();
               });
               
                   
           }).on('shown.bs.modal', function () {

              
           }).modal('show');
        }
    });
}

function send_factory_data(){
     _common.storeSession('factory', $('#factories_list option:selected').text());

    set_clnf_vars(); 
    $('#select_mdls_by_factory').modal('hide'); 
}

*/