# Changelog

## Version 1.2.2 // 29.01.2019

# Plugin: Server 1.2.0
* Tablesorter in Listenansicht Limitierung aufgehoben
* Ausschließen von Addons und Versionen bei der Prüfung
* Umstellung auf bin2hex Token (Mindestanforderung für das Server Plugin PHP 7)
* Tags können für ein Projekt vergeben werden 

# Plugin: Pagespeed 1.1.0
* Performance und API Anpassungen
* Screenshot wird an Server Hook übergeben

## Version 1.2.0 // 20.12.2018

# Plugin: Server 1.1.0
* Tablesorter in Listenansicht angepasst
* Konfigurationsseite
* Minimale PHP Version
* Minimale CMS Version
* Letzte Änderungen auf der Webseite nun in Listenansicht
* Letzte Änderungen auf der Webseite nun in Detailansicht
* Letzter Datenabgleich in Detailansicht
* Cronjob aus Listenansicht aufrufbar

# Plugin: Client 1.0.5
* Letzte Änderungen auf der Webseite

# Plugin: Pagespeed 1.0.4
* Cronjob aus Listenansicht aufrufbar

# Plugin: Hosting 1.0.2
* idn_to_ascii Anpassungen PHP 7.1
* Zertifikat Start/Enddatum werden abgerufen
* Cronjob aus Listenansicht aufrufbar


## Version 1.1.3 // 05.11.2018

# Plugin: Hosting 1.0.1
* ip-api.com Aufrufe geändert

## Version 1.1.2 // 04.11.2018

# Plugin: Server 1.0.7
* Listenansicht hat nun einen Tablesorter
* Anzahl der Domains / Projekte wird in der Übersicht angezeigt

# Plugin: Pagespeed 1.0.4
* Listenansicht hat nun einen Tablesorter
* Anzahl der Domains / Projekte wird in der Übersicht angezeigt
* Extensionpoint Aufruf angepasst

# Plugin: Hosting 1.0.0
* Neues Plugin für Hosting Übersicht erstellt


## Version 1.1.1 // 11.10.2018

# Plugin: Server 1.0.6
* Curl Aufruf mit SSL Zertifikaten (SSL_VERIFYPEER nun false)
* Bei Redaxo 4 Projekten wird nun kein Syslog und keine Updates in der Übersicht angezeigt
* Limit beim Favicon Cronjob entfernt


## Version 1.1.0 // 08.10.2018

* Erweiterung um Rechtevergabe für das Addon und deren Plugins
 * project_manager[]
  
# Plugin: Server 1.0.5
* Schreibweise der Funktionen verkürzt
* Erweiterung um Rechtevergabe des Plugins
* project_manager_server[]
* Automatisches Anlegen der Crobjobs: rex_cronjob_project_manager_data, rex_cronjob_project_manager_favicon

# Plugin: Client 1.0.4
* Erweiterung um Rechtevergabe des Plugins
* project_manager_client[]

# Plugin: PageSpeed 1.0.3 
* Erweiterung um Rechtevergabe des Plugins
* project_manager_pagespeed[]  
* Automatisches Anlegen des Crobjobs: rex_cronjob_project_manager_pagespeed
  


## Version 1.0.9 // 05.10.2018

# Plugin: Server 
* Detailview zeigt nun direkt die Addon Updates im Accordion
* Wenn etwas im Syslog steht, wird nun ein Ausrufezeichen in der Übersicht und im Accordion in der Detailview angezeigt

# Plugin: Client 
* Erweiterung um MySQL Version für REDAXO 4


## Version 1.0.8 // 05.10.2018

* Diverse Sprachersetzungen

# Plugin: Server 
* API Key Validierung entfernt
* Erweiterung um MySQL Version
* Cronjobs www entfernt

# Plugin: Client 
* Erweiterung um MySQL Version
  
# Plugin: PageSpeed
* Domainanzeige bei Pagespeed


## Version 1.0.5 // 05.10.2018

* Direkteinstieg zum Server Plugin über die Hauptnavigation nun möglich - Danke @ Dirk Schürjohann

## Version 1.0.4 // 05.10.2018

* Demoplugin entfernt

# Plugin: Client
* Notices entfernt
* Client Version wird mit übermittelt
* REDAXO 4 Client Datei im Install Ordner

# Plugin: Server
* Assets Ordner wird nun bei der Installation vom Server Plugin angelegt
* REDAXO Hauptversion kann pro Projekt angelegt werden. Der Aufruf zum Client wird nun unterschieden und für alte REDAXO 4 Instanzen gibt es eine eigene PHP Datei
* YFORM Abhängigkeit hinzugefügt 
* Notices entfernt

# Plugin: Pagespeed
* Error Handling im Cronjob
* Notices entfernt


## Version 1.0.0 // 02.10.2018

* Projekt Manager Addon
