

$(document).on('rex:ready', function (event, container) {
   
  var projectManagerTablesorter = $("#rex-page-project-manager-server-overview .project_manager-tablesorter");
 
  projectManagerTablesorter.tablesorter({ 
       theme : 'default', 
       widthFixed: true,
       widgets: ["saveSort"],
       widgetOptions: {
         saveSort: true
       },
       dateFormat: "dd-mm-yy",
       headers: {
         0: { sorter: false},
         6: { sorter: "shortDate", dateFormat: "dd-mm-yy" },
         7: { sorter: 'text' },
         8: { sorter: 'text' }
       }
       
   });
  
   $("#rex-page-project-manager-hosting-overview .project_manager-tablesorter").tablesorter({ 
     theme : 'default', 
     widthFixed: true,
     widgets: ["saveSort"],
     widgetOptions: {
       saveSort: true
     },
     dateFormat : "dd-mm-yy",
     headers: {
       0: { sorter: false},
       7: { sorter: "shortDate", dateFormat: "dd-mm-yy" }
     }     
  });
   $("#rex-page-project-manager-pagespeed-overview .project_manager-tablesorter").tablesorter({ 
     theme : 'default', 
     widthFixed: true,
     widgets: ["saveSort"],
     widgetOptions: {
       saveSort: true
     },
     dateFormat : "dd-mm-yy",
     headers: {
       0: { sorter: false},
       4: { sorter: "shortDate", dateFormat: "dd-mm-yy" }
     }     
  });
   
});