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
    $(this).addClass('loading');
    
    $.ajax({
        url: callUrl,
        type: 'GET',
        cache: false,
        success: function(data){
          location.reload();
        }
    });
  
    event.preventDefault();
  });
  
  
});