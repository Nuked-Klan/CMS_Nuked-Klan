<?php

/**
 * Restless
 * Design by Homax
 * Developped by Samoth93
 * Thanks GuigZ for participation
 * April 2013
 */
final class tpl {
    const CONFIG_DIR = 'themes/Restless/config/';
    const TPL_DIR    = 'themes/Restless/views/';
    protected $vars = array();
    protected $content;

    public function assign($name, $value) {
        $this->vars[$name] = $value;
    }

    public function get($name) {
        return $this->vars[$name];
    }

    public function render($name, $data = null) {
        if(!is_null($data)){
            $this->assign('data', $data);
        }

        $this->getConfig($name);

        ob_start();

        $this->getView($name);

        $this->content = ob_get_contents();

        ob_end_clean();

        $this->parse();
    }

    private function getConfig($name) {
        $configFile = self::CONFIG_DIR . $name . '.php';

        if (file_exists($configFile))
            require $configFile;
    }

    private function getView($name) {
        if (file_exists(self::TPL_DIR . $name . '.tpl')) {
            require self::TPL_DIR . $name . '.tpl';
        } else {
            throw new Exception('No template file  ' . $name . '.tpl present in directory ' . $this->tplDir);
        }
    }

    private function parse() {
        $this->replaceString();

        $this->replaceArrayCall();

        $this->replaceForeach();

        eval(' ?>' . $this->content . '<?php ');
    }

    private function replaceString() {
        $this->content = preg_replace_callback('#\{\{([A-Za-z_\-0-9>\'\"\[\]\(\)\$]+)\}\}#',
            function ($matches){
                if(array_key_exists($matches[1], $this->vars)){
                    return $this->vars[$matches[1]];
                }
                else{
                    return '<?php $'.$matches[1].'; ?>';
                }
            },
            $this->content);
    }

    private function replaceArrayCall() {
        $this->content = preg_replace('#\{\{([A-Za-z_\-0-9]+)\.([A-Za-z_\-0-9]+)\}\}#',
            '<?php echo $$1[\'$2\']; ?>',
            $this->content);
    }

    private function replaceForeach() {
        $this->content = preg_replace('#@foreach\s*\(\s*(\$[A-Za-z_\-0-9]+|\$this\->[A-Za-z0-9_]+\(\'[A-Za-z0-9_]+\'\))\s*as\s*(\$[A-Za-z_\-0-9]+\s*)\)#',
            '<?php foreach ($1 as $2): ?>',
            $this->content);

        $this->content = preg_replace('#@endforeach#',
            '<?php endforeach; ?>',
            $this->content);
    }

    public function debug(){
        echo '<pre>';
        print_r($this->vars);
        echo '</pre>';
    }
}