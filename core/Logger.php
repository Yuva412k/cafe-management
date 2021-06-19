<?php

/**
 * Logger Class
 */
namespace app\core;

class Logger{

    /**
     * log
     * 
     * @param string $header
     * @param string $message
     * @param string $filename
     * @param string $linenum
     * 
     */
    public static function log($header = '', $message = "", $filename='', $linenum='')
    {
        $logfile = APP."logs/log.txt";
        $data = date("d/m/Y h:i:s");
        $err = $data. " | ". $filename . " | " . $linenum ." | " . $header. "\n";

        $message = is_array($message) ? implode("\n", $message) : $message;
        $err .= $message . "\n*********************************\n\n";

        //log/write error to log file
        error_log($err, 3 , $logfile);
    }
}
