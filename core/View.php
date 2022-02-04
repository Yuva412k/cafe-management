<?php

/**
 * View Class
 */
namespace app\core;

 class View{
    
    public Controller $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Renders and returns output for the given file with its array of data
     * 
     * @param string $filePath
     * @param array $data
     * @return string Rendered output
     */
    public function render($filePath, $data= null){
        if(!empty($data)){
            extract($data);
        }

        ob_start();
        include $filePath. ".php";
        $renderedFile = ob_get_clean();

        $this->controller->response->setContent($renderedFile);
        return $renderedFile;
    }

    public function renderWithLayouts($layoutDir, $filePath, $data = null){

        if(!empty($data)) {
            extract($data);
        }

        ob_start();
        require_once $layoutDir . "header.php";
        require_once $filePath  . ".php";
        require_once $layoutDir . "footer.php";
        $renderedFile = ob_get_clean();

        $this->controller->response->setContent($renderedFile);
        return $renderedFile;
    }

    

    /**
     * formats timestamp string coming from the database to "Month Day, Year"
     *
     * @param  string  $timestamp MySQL TIMESTAMP
     * @return string  Date after formatting.
     */
    public function timestamp($timestamp){

        $unixTime = strtotime($timestamp);
        $date = date("F j, Y", $unixTime);

        // What if date() failed to format? It will return false.
        return (empty($date))? "": $date;
    }


    /**
     * formats Unix timestamp to be used in Date Picker in form of: "day/month/year"
     *
     * @param  integer	$unixtime Unix timestamp
     * @return string	Date after formatting.
     */
    public function datePicker($unixtime){

        $date = date("d/m/Y", (int)$unixtime);
        return (empty($date))? "": $date;
    }

    /**
     * Converts characters to HTML entities
     * This is important to avoid XSS attacks, and attempts to inject malicious code in your page.
     *
     * @param  string $str The string.
     * @return string
     */
    public function encodeHTML($str){
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * It's same as encodeHTML(), But, also use nl2br() function in PHP
     *
     * @param  string	The string.
     * @return string	The string after converting characters and inserting br tags.
     */
    public function encodeHTMLWithBR($str){
        return nl2br(htmlentities($str, ENT_QUOTES, 'UTF-8'));
    }
 }