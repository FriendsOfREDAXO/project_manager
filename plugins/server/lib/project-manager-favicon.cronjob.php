<?php

class rex_cronjob_project_manager_favicon extends rex_cronjob
{

    public function execute()
    {

        $domains = rex_sql::factory()->setDebug(0)->getArray("SELECT * FROM " . rex::getTable('project_manager_domain') . " ORDER BY updatedate asc "); 
        $multi_curl = curl_multi_init();
        $ch = [];

        foreach($domains as $domain) {

            $ssl = $domain['is_ssl'];
            $protocol = ($ssl == 1) ? "https://" : "http://";      
            $ch[$domain['domain']] = curl_init();
            $fp[$domain['domain']] = fopen(rex_path::pluginAssets('project_manager', 'server', 'favicon/'.$domain['domain'].'.png'), 'w+');
            //curl_setopt($ch[$domain['domain']], CURLOPT_URL, "https://www.google.com/s2/u/0/favicons?domain=".$domain['domain']."&sz=64");
            
            $faviconUrl = self::getFavicon($protocol.$domain['domain']);
            
            if (is_array($faviconUrl)) {
              $favicon = $faviconUrl[0];
            } else {
              $favicon = $faviconUrl;
            }
            
            if ($favicon == 'FALSE')
              $favicon = rex::getServer().'/assets/addons/project_manager/plugins/server/favicon/redaxo-favicon.png';     
            
            curl_setopt($ch[$domain['domain']], CURLOPT_URL, $favicon);
            
            curl_setopt($ch[$domain['domain']], CURLOPT_FOLLOWLOCATION, true);
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
    
    public function getFavicon ($url) {
      
      $file_headers = @get_headers($url);
      $found = FALSE;
      // 1. CHECK THE DOM FOR THE <link> TAG
      // check if the url exists - if the header returned is not 404
      if($file_headers[0] != 'HTTP/1.1 404 Not Found') {
        $dom = new DOMDocument();
        $dom->strictErrorChecking = FALSE;
        @$dom->loadHTMLfile($url);  //@ to discard all the warnings of malformed htmls
        if (!$dom) {
         // $error[]='Error parsing the DOM of the file';
        } else {
          $domxml = simplexml_import_dom($dom);
          
          if ($domxml) {
            
            //check for the historical rel="shortcut icon"
            if ($domxml->xpath('//link[@rel="shortcut icon"]')) {
              $path = $domxml->xpath('//link[@rel="shortcut icon"]');
              $faviconURL = $path[0]['href'];              
              # check if absolute url or relative path
              $favicon_elems = parse_url($faviconURL);
              # if relative
              if(!isset($favicon_elems['host'])){
                $faviconURL = $url . $faviconURL;
              }  
              
              $favicon_headers = @get_headers($faviconURL);
              if (is_array($favicon_headers)) {              
                if(!in_array('HTTP/1.1 404 Not Found', $favicon_headers)) {
                  $found == TRUE;
                  return $faviconURL;
                }               
              }
              
              //check for the HTML5 rel="icon"
            } else if ($domxml->xpath('//link[@rel="icon"]')) {
              $path = $domxml->xpath('//link[@rel="icon"]');
              $faviconURL = $path[0]['href'];
              # check if absolute url or relative path
              $favicon_elems = parse_url($faviconURL);
              # if relative
              if(!isset($favicon_elems['host'])){
                $faviconURL = $url . $faviconURL;
              }
              
              $favicon_headers = @get_headers($faviconURL);
              if (is_array($favicon_headers)) {  
                if(!in_array('HTTP/1.1 404 Not Found', $favicon_headers)) {
                  $found == TRUE;
                  return $faviconURL;
                }
              }
              
            } else {
              //$error[]="The URL does not contain a favicon <link> tag.";
            }
            
            if ($found == FALSE) return 'FALSE';
            
          }
          
          
        }
        
        
        // 2. CHECK DIRECTLY FOR favicon.ico OR favicon.png FILE
        // the two seem to be most common
        if ($found == FALSE) {
          $parse = parse_url($url);
          $favicon_headers = @get_headers("http://".$parse['host']."/favicon.ico");
          if($favicon_headers[0] != 'HTTP/1.1 404 Not Found') {
            $faviconURL = "/favicon.ico";
            $favicon_elems = parse_url($faviconURL);
            # if relative
            if(!isset($favicon_elems['host'])){
              $faviconURL = $url . $faviconURL;
            }
            $found == TRUE;
            return $faviconURL;
          }
          $favicon_headers = @get_headers("http://".$parse['host']."/favicon.png");
          if($favicon_headers[0] != 'HTTP/1.1 404 Not Found') {
            $faviconURL = "/favicon.png";
            $favicon_elems = parse_url($faviconURL);
            # if relative
            if(!isset($favicon_elems['host'])){
              $faviconURL = $url . $faviconURL;
            }
            $found == TRUE;
            return $faviconURL;
          }
          if ($found == FALSE) {
            //$error[]= "Files favicon.ico and .png do not exist on the server's root.";
          }
        }
        // if the URL does not exists ...
      } else {
       // $error[]="URL does not exist";
      }
      
      if ($found == FALSE && isset($error) ) {
        return $error;
      }
    }
}
?>