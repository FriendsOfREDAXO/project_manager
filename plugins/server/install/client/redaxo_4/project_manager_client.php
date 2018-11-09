<?php
// Gibt Basis-Informationen zur Website an das Projekt Manager Addon weiter. 

if($_GET['rex-api-call'] == "project_manager" && $_GET['api_key'] !== "") {
    $project_manager["php_version"] = phpversion();
    $project_manager["pm_version"] = "1.0.0-legacy";
    $project_manager["client_version"] = "1.0.0-legacy";
    $project_manager["status"] = 1;

    $rex_master_file = "redaxo/include/master.inc.php";
    if(file_exists($rex_master_file)) {
        include($rex_master_file);
        $project_manager["cms"] = "REDAXO";
        $project_manager["cms_version"] = $REX['VERSION'].".".$REX['SUBVERSION'].".".$REX['MINORVERSION'];
        $project_manager["rex_version"] = $REX['VERSION'].".".$REX['SUBVERSION'].".".$REX['MINORVERSION'];
    }

}
echo json_encode($project_manager);