$(document).ready(function(){
  
  $( ".rex-table-php-version" ).each(function() {
    var check = ($( this ).find('span').data('color'));
    if (check != "") $(this).addClass(check);    
  });
  $( ".rex-table-cms-version" ).each(function() {
    var check = ($( this ).find('span').data('color'));
    if (check != "") $(this).addClass(check);    
  });
  $( ".rex-table-validTo" ).each(function() {
    var check = ($( this ).find('span').data('color'));
    if (check != "") $(this).addClass(check);    
  });
  
  $('a.callCronjob').click(function(event){

    var callUrl = $(this).data('cronjob');
    var reloadUrl = $(this).attr('href');
    
    $(this).addClass('loading');
    
    $.ajax({
        url: callUrl,
        type: 'GET',
        cache: false,
        success: function(data){
          $(location).attr('href', reloadUrl).reload();              
        }
    });
  
    event.preventDefault();
  });
  
  $('.btn.btn-project-manager-update').click(function(event){
    
    var protocol = $(this).data('protocol');
    var domain = $(this).data('domain');
    var api_key = $(this).data('api_key');
    var param = $(this).data('param');
    var func = $(this).data('func');
    var confirmText = '';
    
    if (func == "delLog") confirmText = "Systemlog löschen?"
    if (func == "updateData") confirmText = "Projekt Daten aktualisieren?"
    
    if (confirm(confirmText)) {
        var protocol = $(this).data('protocol');
        var domain = $(this).data('domain');
        var api_key = $(this).data('api_key');
        var func = $(this).data('func');    
        var callUrl = "/?rex-api-call=project_manager_server&func="+func+"&protocol="+protocol+"&domain="+domain+"&api_key="+api_key+"&param="+param;
        $(this).addClass('loading');
        
        $.ajax({
            url: callUrl,
            type: 'GET',
            dataType: 'json',
            cache: false,
            success: function(data){
              
              location.reload();
              
            }
        });
      
        event.preventDefault();
      }
    });
  
 
  
  
});