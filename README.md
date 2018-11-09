
REDAXO-AddOn: Project Manager
================================================================================

Bietet Unterstützung bei der Verwaltung und Überprüfung der eigenen REDAXO-Installationen. 

## Projektübersicht 
![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/project_manager/assets/project_manager_server.png)

## Projektdetailansicht
![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/project_manager/assets/project_manager_server_detail.png)

## Inhaltsübersicht

* [Einleitung](#einleitung)
  - [Über das AddOn](#ueber-das-addon)
  - [Features](#section1)
  - [Installation](#section2)
  - [Plugins](#section3)
 
* [Einstellungen](#einstellungen)
* [Server-Plugin](#server-plugin)
  - [Server](#server)
  - [Editiermodus](#editiermodus)
  - [Sync-Cronjob](#status-cronjob)
* [Client-Plugin](#client-plugin)  
  - [Client](#client)
* [Hosting-Plugin](#hosting-plugin)  
  - [Hosting](#hosting)
* [Allgemein]
  - [Changelog](#section4)
  - [Credits](#section5)
  - [Bug-Meldungen, Hilfe und Links](#section6)    
  
<a name="einleitung"></a>
## Einleitung

<a name="ueber-das-addon"></a>
### Über das Addon

Dieses Addon bietet Unterstützung bei der Verwaltung und Überprüfung der eigenen REDAXO-Installationen. 

&uarr; [zurück zur Übersicht](#top)

<a name="section1"></a>
### Features

Das **Client-Plugin** ist für den Abruf der einzelnen Parameter zuständig.

* Hinterlegen eines API-Keys in den Einstellungen
* Abruf von Parametern der Installation, z.B. 
  * Aktuelle PHP-Version
  * Installierte und updatefähige REDAXO-Addons
  * Vorhandene Module
  * Verwendete YRewrite-Domains
  * Letzte Logins
  * Letzte Änderungen im Medienpool
  * Letzte Meldungen aus dem Syslog
  * weiter geplant: Letzte Änderungen, Medienpool-Verzeichnisgröße, Backup-Status 
  * weiter geplant: EXTENSION_POINT, um eigene Prüfregeln zu hinterlegen

Das **Server-Plugin** dient zur Verwaltung der REDAXO Projekte

* Verwaltung der REDAXO-Projekte und deren API-Keys
* Darstellung der wichtigsten Parameter in der Listenansicht
* Darstellung aller Parameter in der Detailansicht
* Abruf und Überwachung der Parameter von den Clients
* EXTENSION_POINT **PROJECT_MANAGER_SERVER_DETAIL_HOOK** zur Einbindung von weiteren Plugins und zur Darstellung in der Detailansicht
* Cronjob zum automatisierten Abruf aller Parameter
* Cronjob zum automatisierten Abruf der Favicons

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/project_manager/assets/project_manager_server.png)

Das **PageSpeed-Plugin** dient zur Anzeige der Google PageSpeedwerte
* Abrufen der Desktop und Mobile PageSpeed Werte
* Darstellung der Werte in der Listenansicht
* Darstellung aller Parameter in der Detailansicht im **Server-Plugin**
* Cronjob zum automatisierten Abruf der Werte

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/project_manager/assets/project_manager_pagespeed.png)

Das **Hosting-Plugin** dient zur Anzeige von Hosting Informationen
* Abrufen von ISP, Organisation sowie die aktuelle IP Adresse über den IP-API.com Dienst
* Darstellung der Werte in der Listenansicht
* Darstellung aller Parameter in der Detailansicht im **Server-Plugin**
* Cronjob zum automatisierten Abruf der Werte

![Screenshot](https://raw.githubusercontent.com/FriendsOfREDAXO/project_manager/assets/project_manager_hosting.png)

&uarr; [zurück zur Übersicht](#top)



<a name="section2"></a>
### Installation

Voraussetzung für die aktuelle Version des Projekt Manager Addons: REDAXO 5.3, Cronjob-Addon, MarkItUp-Addon
Nach erfolgreicher Installation gibt es im Backend unter AddOns einen Eintrag "Projekt Manager".

&uarr; [zurück zur Übersicht](#top)

<a name="section3"></a>
### Plugins

Auf den REDAXO Projekten sollte nur das Plugin **Client** installiert und konfiguriert werden.
Der Projekt Manager Server benötigt das Plugin **Server**.

&uarr; [zurück zur Übersicht](#top)

<a name="server-plugin"></a>
## Server-Plugin

<a name="server"></a>
### Server

Unter dem Reiter **Übersicht** werden REDAXO-Installationen verwaltet.

Es wird eine Übersicht der wichtigsten Parameter in der Listenansicht dargestellt.
Neue Projekte können angelegt und vorhandene Projekte geändert werden.

Die einzelnen Felder sind:

* Name des Projektes
* Website (Domain aus dem System oder Domain des YRewrite-Projekts, z.B. `domain.de`)
* SSL Verschlüsselung
* API-Key
* REDAXO Hauptversion (Wird für den entsprechenden Aufruf zum Client benötigt)


<a name="editiermodus"></a>
### Editiermodus

Im **Editiermodus** lässt sich das ausgewählte Projekt verwalten.

<a name="details"></a>
### Details

Unter Details kann das Projekt gewählt werden und alle relevanten Inhalte zum Projekt angezeigt werden.

<a name="status-cronjob"></a>
### Sync-Cronjob

Um die Daten von den REDAXO Clients in den Projekt Manager zu laden, gibt es zwei Cronjobs welche im Cronjob Addon mit der Installation angelegt werden.
* Projekt Manager: Hole Domaindaten
* Projekt Manager: Hole Favicon


<a name="client-plugin"></a>
## Client-PlugIn

<a name="client"></a>
### Client

Unter dem Reiter **Client** wird der API Key für die REDAXO Instanz verwaltet.

Die einzelnen Felder sind:

* API-Key

Beim Installieren und Aktivieren des Plugins wird automatisch ein API-Key eingerichtet.


&uarr; [zurück zur Übersicht](#top)

<a name="einstellungen"></a>
### Einstellungen

Unter dem Reiter **Einstellungen** lässt sich ein API-Key hinterlegen. Bei der Installation des Plugins wird automatisch ein API-Key voreingestellt. Anschließend lassen sich die Parameter über die URL abrufen:

```
http://www.domain.de/?rex-api-call=project_manager&api_key=<api_key>
```

&uarr; [zurück zur Übersicht](#top)


<a name="redaxo4"></a>
### REDAXO 4

Für REDAXO 4 existiert unter /plugins/client/install/client/redaxo_4 eine Datei Namens **project_manager_client.php**. 
Diese muss in das ROOT der Client Instanz auf der REDAXO 4 läuft kopiert werden.
Der Abruf erfolgt dann über den Projekt Manager Server. Dort muss das Projekt mit REDAXO 4 als Hauptversion konfiguriert sein.

```
http://www.domain.de/project_manager_client.php?rex-api-call=project_manager&api_key=legacy
```

&uarr; [zurück zur Übersicht](#top)

<a name="pagespeed-plugin"></a>
## PageSpeed-Plugin


<a name="pagespeed"></a>
### PageSpeed

Unter dem Reiter **Einstellungen** wird der Google PageSpeed API Key verwaltet.

Die einzelnen Felder sind:

* API-Key


### Installation

Nach der Installation des Plugins muss in den Einstellungen der API-Key eingerichtet werden.

&uarr; [zurück zur Übersicht](#top)


<a name="einstellungen"></a>
### Einstellungen

Unter dem Reiter **Einstellungen** lässt sich ein API-Key hinterlegen. Bei der Installation des Plugins wird automatisch ein API-Key voreingestellt. Anschließend lassen sich die Parameter über die URL abrufen:


<a name="pagespeed-cronjob"></a>
### PageSpeed-Cronjob

Um die Daten von den REDAXO Projekten in den Projekt Manager zu laden, gibt es einen Cronjobs welcher im Cronjob Addon mit der Installation angelegt werden.
* Projekt Manager: PageSpeed Daten

&uarr; [zurück zur Übersicht](#top)



<a name="hosting-plugin"></a>
## Hosting-Plugin

Das Hosting-Plugin holt ISP, Organisation sowie die aktuelle IP Adresse über den IP-API.com Dienst. Achtung! Es exisitert eine Limitierung von 150 Calls/Minute, daher wurde ein Timing verbaut.


### Installation

Nach der Installation des Plugins sollte der Cronjob einmal ausgeführt werden.

&uarr; [zurück zur Übersicht](#top)


<a name="hosting-cronjob"></a>
### Hosting-Cronjob

Um die Hostingdaten von den REDAXO Projekten in den Projekt Manager zu laden, gibt es einen Cronjobs welcher im Cronjob Addon mit der Installation angelegt werden.
* Projekt Manager: Hosting Daten

&uarr; [zurück zur Übersicht](#top)


## Allgemein

<a name="section4"></a>
### Changelog

siehe CHANGELOG.md des AddOns


<a name="section6"></a>
### Bug-Meldungen, Hilfe und Links

* Auf Github: https://github.com/FriendsOfREDAXO/project_manager/issues
* im Forum: https://www.redaxo.org/forum/
* im Slack-Channel: https://friendsofredaxo.slack.com/


### Lizenz

siehe [LICENSE](https://github.com/FriendsOfREDAXO/project_manager/blob/master/LICENSE)

### Autor

**Friends Of REDAXO**

* http://www.redaxo.org
* https://github.com/FriendsOfREDAXO

**Projekt-Lead**
[Ronny Kemmereit](https://github.com/rkemmere)

**Development-Team / Autoren**
* [Ronny Kemmereit](https://github.com/rkemmere) 
* [Pascal Schuchmann](https://github.com/pschuchmann)
* [Alexander Walther](https://github.com/alexplusde)  


<a name="section5"></a>
### Credits

Großes Danke geht an Alexander Walther - alexplusde welcher uns eine gute Codebasis für die Erstellung des Addons zur Verfügung gestellt hat.
[Alexander Walther](https://github.com/alexplusde) 
