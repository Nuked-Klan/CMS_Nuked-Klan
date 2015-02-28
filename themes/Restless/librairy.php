<?php

final class viewTpl {
    protected $configDir = 'themes/Restless/config/';
    protected $tplDir = 'themes/Restless/views/';
    protected $vars = array();
    protected $configFile;
    public function __construct($name = null, $data = null){
        if($data != null){
            $this->data = $data;
        }
        if ($name == null) {
            throw new Exception('You must define a valid template Name');
        } else {
            $this->tplName = $name;
            $this->configFile = $this->configDir.$this->tplName.'.php';
            if(file_exists($this->configFile))
                require($this->configFile);            
        }
        if (file_exists($this->tplDir.$this->tplName.'.tpl')){
            include $this->tplDir.$this->tplName.'.tpl';
        } else {
            throw new Exception('No template file  ' . $this->tplName . '.tpl present in directory ' . $this->tplDir);
        }
    }
    public function __set($name, $value){
        $this->vars[$name] = $value;
    }
    public function __get($name){
        return $this->vars[$name];
    }
}