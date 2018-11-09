$(document).on('rex:ready', function (event, container) {
   $(".project_manager-tablesorter").tablesorter({ 
       theme : 'default', 
       widthFixed: true,
       headers: { 0: { sorter: false} }
    });
});