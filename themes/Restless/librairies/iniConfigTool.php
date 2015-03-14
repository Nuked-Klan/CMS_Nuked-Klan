<?php

/**
 * A simple class to read/save set of var/array in a .ini file
 *
 * @author Samoth93 <samoth93@gmail.com>
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class iniConfigTool {

    /**
     * @var string Path of ini file
     */
    protected $filename;

    /**
     * @var array Parsed content of ini file
     */
    protected $content;

    /**
     * @var bool Toggle auto save of ini file
     */
    protected $autoSave = false;

    protected $iniComment = ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;\n;;\n;;   NE PAS MODIFIER CE FICHIER\n;;   DON'T EDIT THIS FILE\n;;\n;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;\n";

    /**
     * Read ini file and parse it
     *
     * @param $filename string Path to ini file
     * @throws Exception If unable to parse ini file
     */
    public function __construct($filename) {
        $this->filename = $filename;

        if ($this->content = parse_ini_file($this->filename, true)) {
            $this->parseMultiArrays();

            return true;
        }
        else {
            throw new Exception('Ini file can\'t be loaded !');
        }
    }

    /**
     * Parse a parsed ini file to add multidimensionnal arrays
     */
    private function parseMultiArrays() {
        foreach ($this->content as $section => $array) {
            foreach ($array as $row => $value) {
                if (preg_match('#\{\{([A-Za-z0-9_\-]+)\}\}#', $value, $matches)) {
                    $this->content[$section][$row] = $this->content[$matches[1]];
                    unset($this->content[$matches[1]]);
                }
            }
        }
    }

    /**
     * Revert process of parseMultiArrays()
     */
    private function unParseMultiArrays() {
        foreach ($this->content as $section => $array) {
            foreach ($array as $row => $value) {
                if (is_array($value)) {
                    $this->content[$section . 'Content'] = $value;
                    $this->content[$section][$row] = '{{' . $section . 'Content}}';
                }
            }
        }
    }

    private function getSection($section) {
        return $this->content[$section];
    }

    public function get($key, $subArray = null) {
        if (strpos($key, '.') !== false) {
            $tmp = explode('.', $key);
            if (is_array($this->content[$tmp[0]][$tmp[1]]) && !is_null($subArray)) {
                return $this->content[$tmp[0]][$tmp[1]][$subArray];
            }
            else {
                return $this->content[$tmp[0]][$tmp[1]];
            }
        }
        else {
            return $this->getSection($key);
        }
    }

    private function setSection($section, $key) {
        if (is_array($key)) {
            $this->content[$section] = $key;
        }

        if (strpos($section, '.') !== false) {
            $tmp = explode('.', $section);
            $this->setValue($tmp[0], $tmp[1], $key);
        }

        if ($this->autoSave === true) {
            $this->save();
        }

        return true;
    }

    private function setValue($section, $key, $value) {
        $this->content[$section][$key] = $value;

        if ($this->autoSave === true) {
            $this->save();
        }

        return true;
    }

    public function set($section, $key, $value = null) {
        if (is_null($value)) {
            return $this->setSection($section, $key);
        }
        else {
            return $this->setValue($section, $key, $value);
        }
    }

    public function save() {
        $this->unParseMultiArrays();
        $buffer = null;

        $buffer = $this->iniComment;

        if (is_writeable($this->filename)) {
            foreach ($this->content as $section => $array) {
                $buffer .= "[" . $section . "]\n";
                foreach ($array as $key => $value) {
                    $buffer .= "$key = $value\n";
                }
                $buffer .= "\n";
            }
            $file = fopen($this->filename, 'w');
            fwrite($file, $buffer);
            fclose($file);
            return true;
        }
        else {
            throw new Exception('Ini file can\'t be saved !');
        }
    }

    public function setAutoSave($bool) {
        if (is_bool($bool)) {
            $this->autoSave = $bool;
            return true;
        }

        return false;
    }

    public function debug() {
        echo '<pre>';
        print_r($this->content);
        echo '</pre>';
    }

}