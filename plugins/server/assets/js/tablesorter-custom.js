$(document).on('rex:ready', function (event, container) {
   $(".project_manager-tablesorter").tablesorter({ 
       theme : 'default', 
       widthFixed: true,
       widgets: ["saveSort"],
       widgetOptions: {
         saveSort: true
       },
       headers: { 0: { sorter: false} }
    });
});