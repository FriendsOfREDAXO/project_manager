<?php

if (!$this->hasConfig()) {
    $this->setConfig('project_manager_api_key', md5(time()));
}    
