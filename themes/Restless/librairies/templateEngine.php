<?php

/**
 * A tiny template engine
 *
 * @author Samoth93 <samoth93@gmail.com>
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class tpl {
    const CONFIG_DIR         = 'themes/Restless/config/';
    const TPL_DIR            = 'themes/Restless/views/';
    const PATTERN_VARS       = '#\{\{\s*([*|$]?)([A-Za-z0-9_.]+)\s*\}\}#';
    const PATTERN_INCLUDES   = '#@include\s*\(\s*([A-Za-z0-9_\-]+)\s*(,\s*[A-Za-z0-9_\-]+)*\)#';
    const PATTERN_FOREACH    = '#@foreach\s*\(\s*([A-Za-z0-9_.]+)\s*as\s*([A-Za-z0-9_]+)\s*(=>)*\s*([A-Za-z0-9_]*)\s*\)#';
    const PATTERN_ENDFOREACH = '#@endforeach#';
    const PATTERN_IF         = '#@if\s*\(\s*(.*)\s*\)#';
    const PATTERN_ELSEIF     = '#@else\s*if\s*\(\s*(.*)\s*\)#';
    const PATTERN_ELSE       = '#@else#m';
    const PATTERN_ENDIF      = '#@endif#';
    const PATTERN_FUNCTIONS  = '#%([A-Za-z0-9_\-]+)\s*\(\s*(.*)\s*\)#';
    protected $vars        = array();
    protected $content;
    protected $output;
    protected $intoPhpTags = false;
    protected $isInclude   = false;

    public function assign($name, $value) {
        $this->vars[$name] = $value;
    }

    public function get($name) {
        if (array_key_exists($name, $this->vars))
            return $this->vars[$name];
        else
            return false;
    }

    public function render($name, $data = null) {
        if (!is_null($data)) {
            $this->assign('data', $data);
        }

        $this->getConfig($name);

        ob_start();

        $this->getView($name);

        $this->content = ob_get_contents();

        ob_end_clean();

        $this->parse();

        if ($this->include === false) {
            echo $this->output;
        }
    }

    private function getConfig($name) {
        $configFile = self::CONFIG_DIR.$name.'.php';

        if (file_exists($configFile))
            require $configFile;
    }

    private function getView($name) {
        if (file_exists(self::TPL_DIR.$name.'.tpl')) {
            require self::TPL_DIR.$name.'.tpl';
        }
        else {
            throw new Exception('No template file  '.$name.'.tpl present in directory '.$this->tplDir);
        }
    }

    private function parse() {

        $this->parseIfElse();

        $this->parseFunctions();

        $this->parseVariables();

        $this->parseIncludes();

        $this->parseForeach();

        /*$this->debugTpl();
        return;*/

        eval(' ?>'.$this->content.'<?php ');
    }

    private function parseVariables($content = null) {
        if (is_null($content)) {
            $this->content = preg_replace_callback(self::PATTERN_VARS, 'self::formatVariables', $this->content);
        }
        else {
            return preg_replace_callback(self::PATTERN_VARS, 'self::formatVariables', $content);
        }
    }

    private function formatVariables($matches) {
        $withEcho = true;
        $printTags = true;

        if ($this->intoPhpTags === true) {
            $withEcho = $printTags = false;
        }

        if (array_key_exists($matches[2], $this->vars)) {

            if ($this->intoPhpTags === true) {
                return '$this->get(\''.$matches[2].'\')';
            }
            return $this->vars[$matches[2]];
        }
        else if ($matches[1] == '*') {
            if (defined(strtoupper($matches[2]))) {
                if ($this->intoPhpTags === true) {
                    return strtoupper($matches[2]);
                }
                return constant(strtoupper($matches[2]));
            }
            else
                return null;
        }
        else if ($matches[1] == '$') {
            return $this->printTagsPhp('$'.$matches[2], $withEcho, $printTags);
        }
        else if (strpos($matches[2], '.')) {
            $var = $this->formatArrayVar($matches[2]);
            return $this->printTagsPhp('$'.$var, $withEcho, $printTags);
        }
        else {
            return $this->printTagsPhp('$'.$matches[2], $withEcho, $printTags);
        }


        return null;
    }

    private function printTagsPhp($content, $withEcho = false, $printTags = true) {
        $return = null;

        if ($printTags)
            $return .= '<?php ';

        if ($withEcho)
            $return .= ' echo ';

        $return .= $content;

        if ($withEcho)
            $return .= '; ';

        if ($printTags)
            $return .= ' ?>';

        return $return;
    }

    private function parseIncludes() {
        $this->content = preg_replace_callback(self::PATTERN_INCLUDES, 'self::formatIncludes', $this->content);
    }

    private function formatIncludes($matches) {
        $param = null;
        if (!empty($matches[2])) {
            $var = trim(str_replace(',', null, $matches[2]));
            if (array_key_exists($var, $this->vars)) {
                $param = ', $this->get(\''.$var.'\')';
            }
            else {
                $param = ', $'.$var;
            }
        }

        return $this->printTagsPhp('$GLOBALS[\'tpl\']->render(\''.$matches[1].'\' '.$param.')');
    }

    private function parseForeach() {
        $this->content = preg_replace_callback(self::PATTERN_FOREACH, 'self::formatForeach', $this->content);

        $this->content = preg_replace(self::PATTERN_ENDFOREACH, $this->printTagsPhp('endforeach;', false), $this->content);
    }

    private function formatForeach($matches) {
        $array = '$';
        if (array_key_exists($matches[1], $this->vars)) {
            $array .= 'this->get(\''.$matches[1].'\')';
        }
        else if (strpos($matches[1], '.')) {
            $array .= $this->formatArrayVar($matches[1]);
        }
        else {
            $array .= $matches[1];
        }

        $key = '$'.$matches[2];

        if (!empty($matches[4])) {
            $key .= ' => $'.$matches[4];
        }

        $return = 'foreach('.$array.' as '.$key.'):';

        return $this->printTagsPhp($return);
    }

    private function parseIfElse() {
        $this->intoPhpTags = true;

        $this->content = preg_replace_callback(self::PATTERN_IF, 'self::formatIf', $this->content);

        $this->content = preg_replace_callback(self::PATTERN_ELSEIF, 'self::formatElseIf', $this->content);

        $this->content = preg_replace(self::PATTERN_ELSE, $this->printTagsPhp('else:', false), $this->content);

        $this->content = preg_replace(self::PATTERN_ENDIF, $this->printTagsPhp('endif;', false), $this->content);

        $this->intoPhpTags = false;
    }

    private function formatIf($matches) {
        $conditions = $this->parseVariables($matches[1]);

        return $this->printTagsPhp('if('.$conditions.'):');
    }

    private function formatElseIf($matches) {
        $conditions = '('.$this->parseVariables($matches[1]).')';

        return $this->printTagsPhp('else if'.$conditions.':');
    }

    private function parseFunctions(){
        $this->intoPhpTags = true;

        $this->content = preg_replace_callback(self::PATTERN_FUNCTIONS, 'self::formatFunctions', $this->content);

        $this->intoPhpTags = false;
    }

    private function formatFunctions($matches){
        $params = $this->parseVariables($matches[2]);

        return $this->printTagsPhp($matches[1].'('.$params.');');
    }

    private function formatArrayVar($variable) {
        $arrayTemp = explode('.', $variable);
        $return = null;
        $i = 0;
        foreach ($arrayTemp as $var) {
            if ($i == 0) {
                $return = $var;
                if (array_key_exists($var, $this->vars)) {
                    $return = 'this->get(\''.$var.'\')';
                }
            }
            else {
                $return .= '[\''.$var.'\']';
            }
            $i++;
        }

        return $return;
    }

    public function debugVars() {
        echo '<pre>';
        print_r($this->vars);
        echo '</pre>';
    }

    public function debugTpl() {
        echo '<pre style="background:#fff;">';
        print_r(htmlentities(html_entity_decode($this->content)));
        echo '</pre>';
    }
}