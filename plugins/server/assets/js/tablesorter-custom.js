$(document).on('rex:ready', function (event, container) {
   $("#rex-page-project-manager-server-overview .project_manager-tablesorter").tablesorter({ 
       theme : 'default', 
       widthFixed: true,
       widgets: ["saveSort"],
       widgetOptions: {
         saveSort: true
       },
       dateFormat : "ddmmyyyy",
       headers: {
         0: { sorter: false},
         5: { sorter: "shortDate", dateFormat: "ddmmyyyy" },         
         6: { sorter: 'text' },
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
     dateFormat : "ddmmyyyy",
     headers: {
       0: { sorter: false},
       7: { sorter: "shortDate", dateFormat: "ddmmyyyy" }
     }     
  });
   $("#rex-page-project-manager-pagespeed-overview .project_manager-tablesorter").tablesorter({ 
     theme : 'default', 
     widthFixed: true,
     widgets: ["saveSort"],
     widgetOptions: {
       saveSort: true
     },
     dateFormat : "ddmmyyyy",
     headers: {
       0: { sorter: false},
       4: { sorter: "shortDate", dateFormat: "ddmmyyyy" }
     }     
  });
   
});