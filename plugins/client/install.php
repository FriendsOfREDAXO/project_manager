<?php

if (!$this->hasConfig()) {
    $this->setConfig('project_manager_api_key', bin2hex(random_bytes(24)));
}    
