<?php

class View
{
    private $_file;
    private $_header = '';
    private $_css = [];
    public $_js = [];
    public $_content = [] ;
    public $_template = [] ;
    public $_headerContent = [] ;

    public function __construct($action = null) {
        if( $action ) {
            $this->_file = 'views/view' . $action . '.php';
        }
    }

    private function setJSON ($path, $file, $target = 'content') {
        $location = $path . $file . '.json' ;
        if (file_exists($location)) {
            $json = json_decode(file_get_contents($location), true);
            $sh = $_SESSION['defaultLang']['shortcut'];
            if (!key_exists($sh, $json))
                $sh = 'FR';
            switch($target) {
                case 'template': $this->_template = $json[$sh]; break;
                case 'header': $this->_headerContent = $json[$sh];
                default: $this->_content = $json[$sh]; break;
            }
        }
    }

    public function generateView($data = null) {
        $file = $this->_file ;
        $file = str_replace('views/', '', $file) ;
        $file = str_replace('.php', '', $file) ;
        $this->setJSON('views/content/', $file) ;

        $html = $this->generateFile($this->_file, $data);
        if (!empty($this->_header)) {
            $this->setJSON('templates/content/Front/', 'header', 'header');
        }

        $view = $this->generateFile('views/template.php',
            [
                'header' => $this->_header,
                'content' => $html
            ]);
        echo $view;
    }

    public function generateTemplate($file, $data) {
        $this->setJSON('templates/content/', $file, 'template') ;
        return $this->generateFile('templates/' . $file . '.php', $data);
    }

    private function generateFile($file, $data=null){
        if (file_exists($file)) {
            if( $data != null )
                extract($data);

            ob_start();
            require $file;
            return ob_get_clean();
        } else
            throw new Exception("File " . $file . 'not found');

    }

    public function getJsonArrayNames ($path, $file) {
        $location = $path . $file . '.json' ;
        if (file_exists($location)) {
            $json = json_decode(file_get_contents($location), true);
            $sh = $_SESSION['defaultLang']['shortcut'];
            if (!key_exists($sh, $json))
                $sh = 'FR';
            return $json[$sh];
        }
        return null;
    }
}
