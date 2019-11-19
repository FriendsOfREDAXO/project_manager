<?php

class rex_cronjob_project_manager_favicon extends rex_cronjob
{

    public function execute()
    {

        $domains = rex_sql::factory()->setDebug(0)->getArray("SELECT * FROM " . rex::getTable('project_manager_domain') . " ORDER BY updatedate asc "); 
        $multi_curl = curl_multi_init();
        $ch = [];

        foreach($domains as $domain) {
            $ch[$domain['domain']] = curl_init();
            $fp[$domain['domain']] = fopen(rex_path::pluginAssets('project_manager', 'server', 'favicon/'.$domain['domain'].'.png'), 'w+');
            curl_setopt($ch[$domain['domain']], CURLOPT_URL, "https://www.google.com/s2/favicons?domain=".$domain['domain']);
            curl_setopt($ch[$domain['domain']], CURLOPT_HEADER, 0);
            curl_setopt($ch[$domain['domain']], CURLOPT_FILE, $fp[$domain['domain']]);
            curl_multi_add_handle($multi_curl,$ch[$domain['domain']]);
        }
        $active = null;
        do {
            curl_multi_exec($multi_curl, $active);
        } while ($active > 0);
        
        foreach($domains as $domain) {
            curl_multi_remove_handle($multi_curl,$ch[$domain['domain']]);
            fwrite($fp[$domain['domain']], "");
            fclose($fp[$domain['domain']]);
        }

        curl_multi_close($multi_curl);

        return true;

    }
    public function getTypeName()
    {
        return rex_i18n::msg('project_manager_cronjob_favicon_name');
    }

    public function getParamFields()
    {
        return [];
    }
}
?>