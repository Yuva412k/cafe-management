<?php

/**
 * Validation class
 *
 */
namespace app\core\components;

use app\core\Database;
use Exception;

class Validation {

    /**
     * validation errors
     *
     *
     * @var array
     */
    private $errors = [];

    /**
     * Custom rule messages.
     *
     * @var array
     */
    private $ruleMessages = [];

     /**
      * Start the validation using values and rules passed in $data
      *
      * @param  array  $data
      * @param  bool   $skip To skip validations as soon as one of the rules fails.
      *
      * @throws Exception if rule method doesn't exist
      * @return bool
      */
    public function validate($data, $skip = false){

        $passed = true;

        foreach($data as $placeholder => $rules){

            $value = $rules[0];
            $rules = explode('|', $rules[1]);

            // no need to validate the value if the value is empty and not required
            if(!$this->isRequired($rules) && $this->isEmpty($value)){
                continue;
            }

            // it doesn't make sense to continue and validate the rest of rules on an empty & required value.
            // instead add error, and skip this value.
            if($this->isRequired($rules) && $this->isEmpty($value)){
                $this->addError("required", $placeholder, $value);
                $passed = false;
                continue;
            }

            foreach($rules as $rule){

                $method = $rule;
                $args = [];

                // if it was empty and required or not required,
                // it would be detected by the previous ifs
                if($rule == "required") {
                    continue;
                }

                if($this->isruleHasArgs($rule)){

                    // get arguments for rules like in max(), min(), ..etc.
                    $method = $this->getRuleName($rule);
                    $args   = $this->getRuleArgs($rule);
                }

                if(!method_exists($this, $method)){
                    throw new Exception("Method doesnt exists: " . $method);
                }

                if(!call_user_func_array([$this, $method], [$value, $args])) {

                    $this->addError($method, $placeholder, $value, $args);
                    $passed = false;

                    if($skip){ return false; }
                }
            }
        }

        // possible change is to return the current validation object,
        // and use passes() instead.
        return $passed;
    }

    /**
     * Determine if a given value is empty,
     * excluding '0', false, 0, 0.0, and files uploaded with UPLOAD_ERR_NO_FILE error,
     * because these could be perfectly valid values,
     * then, the validation methods has to decide if this value is valid or not.
     *
     * @param  mixed  $value
     * @return bool
     *
     */
    private function isEmpty($value){

        if(is_null($value)) {
            return true;
        }
        else if(is_string($value)){
            if(trim($value) == '') return true;
        }
        else if (empty($value) && $value !== '0' && $value !== false && $value !== 0 && $value !== 0.0){
            return true;
        }
        else if (is_array($value) && isset($value['name'], $value['type'], $value['tmp_name'], $value['error'])) {
            return (int)$value['error'] == UPLOAD_ERR_NO_FILE;
        }
        return false;
     }

    /**
     * Determine if a given rules has 'required' rule
     *
     * @param  array  $rules
     * @return bool
     */
    private function isRequired($rules){
        return in_array("required", $rules, true);
    }

    /**
     * Determine if a given rule has arguments, Ex: max(4)
     *
     * @param  string  $rule
     * @return bool
     */
    private function isruleHasArgs($rule) {
        return isset(explode('(', $rule)[1]);
    }

    /**
     * get rule name for rules that have args
     *
     * @param  string  $rule
     * @return string
     */
    private function getRuleName($rule){
        return explode('(', $rule)[0];
    }

    /**
     * get arguments for rules that have args
     *
     * @param  string  $rule
     * @return array
     */
    private  function getRuleArgs($rule){

        $argsWithBracketAtTheEnd = explode('(', $rule)[1];
        $args = rtrim($argsWithBracketAtTheEnd, ')');
        $args = preg_replace('/\s+/', '', $args);

        // as result of an empty array coming from user input
        // $args will be empty string,
        // So, using explode(',', empty string) will return array with size = 1
        // return empty($args)? []: explode(',', $args);
        return explode(',', $args);
    }

    /**
     * Add a custom rule message.
     * This message will be displayed instead of default.
     *
     * @param  string  $rule
     * @param  string  $message
     * @return array
     */
    public function addRuleMessage($rule, $message){
        $this->ruleMessages[$rule] = $message;
    }

    /**
     * Add an error
     *
     * @param  string  $rule
     * @param  string  $placeholder for field
     * @param  mixed   $value
     * @param  array   $args
     *
     */
    private function addError($rule, $placeholder, $value, $args = []){

        if(isset($this->ruleMessages[$rule])){
            $this->errors[] = $this->ruleMessages[$rule];
        }
        else{

            // get the default message for the current $rule
            $message = self::defaultMessages($rule);

            if(isset($message)){

                // if $message is set to empty string,
                // this means the error will be added inside the validation method itself
                // check attempts()
                if(trim($message) !== ""){

                    // replace placeholder, value, arguments with their values
                    $replace = ['{placeholder}', '{value}'];
                    $value   = is_string($value)? $value: "";
                    $with    = array_merge([$placeholder, $value], $args);
                    $count   = count($args);

                    // arguments will take the shape of: {0} {1} {2} ...
                    for($i = 0; $i < $count; $i++) $replace[] = "{{$i}}";

                    $this->errors[] = str_replace($replace, $with, $message);
                }

            } else{

                // if no message defined, then use this one.
                $this->errors[] = "The value you entered for " . $placeholder . " is invalid";
            }
        }
    }

    /**
     * Checks if validation has passed.
     *
     * @return bool
     */
    public function passes(){
        return empty($this->errors);
    }

    /**
     * get all errors
     *
     * @return array
     */
    public function errors(){
        return $this->errors;
    }

    /**
     * clear all existing errors
     *
     * @return bool
     */
    public function clearErrors(){
        $this->errors = [];
    }

    /** *********************************************** **/
    /** **************    Validations    ************** **/
    /** *********************************************** **/

    /**
     * Is value not empty?
     *
     * @param  mixed  $value
     * @return bool
     */
    /*private function required($value){
        return !$this->isEmpty($value);
    }*/

    /**
     * min string length
     *
     * @param  string  $str
     * @param  array  $args(min)
     *
     * @return bool
     */
    private function minLen($str, $args){
        return mb_strlen($str, 'UTF-8') >= (int)$args[0];
    }

    /**
     * max string length
     *
     * @param  string  $str
     * @param  array  $args(max)
     *
     * @return bool
     */
    private function maxLen($str, $args){
        return mb_strlen($str, 'UTF-8') <= (int)$args[0];
    }


    /**
     * check if value is contains alphabetic characters and numbers
     *
     * @param  mixed   $value
     * @return bool
     */
    private function alphaNum($value){
        return preg_match('/\A[a-z0-9]+\z/i', $value);
    }

    /**
     * check if value is contains alphabetic characters, numbers and spaces
     *
     * @param  mixed   $value
     * @return bool
     */
    private function alphaNumWithSpaces($value){
        return preg_match('/\A[a-z0-9 ]+\z/i', $value);
    }

    /**
     * check if password has at least
     * - one lowercase letter
     * - one uppercase letter
     * - one number
     * - one special(non-word) character
     *
     * @param  mixed   $value
     * @return bool
     */
    private function password($value) {
        return preg_match_all('$\S*(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$', $value);
    }

    /**
     * check if value is a valid email
     *
     * @param  string  $email
     * @return bool
     */
    private function email($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /** *********************************************** **/
    /** ************  Database Validations  *********** **/
    /** *********************************************** **/

    /**
     * check if a value of a column is unique.
     *
     * @param  string  $value
     * @param  array   $args(table, column)
     * @return bool
     */
    private function unique($value, $args){
        $table = $args[0];
        $col   = $args[1];

        $database = Database::openConnection();
        $database->prepare("SELECT * FROM {$table} WHERE {$col} = :{$col}");
        $database->bindValue(":{$col}", $value);
        $database->execute();

        return $database->countRows() == 0;

    }

    private function equals($value, $args){
        return $value == $args[0];
    }

    /**
     * check if email is unique
     * This will check if email exists and activated.
     *
     * @param  string  $email
     * @return bool
     */
    private function emailUnique($email){

        $database = Database::openConnection();

        // email is unique in the database, So, we can't have more than 2 same emails
        $database->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $database->bindValue(':email', $email);
        $database->execute();
        $user = $database->fetchAssociative();

        if ($database->countRows() == 1) {
            return true;
        }
        return false;
    }

    /** *********************************************** **/
    /** ************    Login Validations   *********** **/
    /** *********************************************** **/

    /**
     * check if user credentials are valid or not.
     *
     * @param  array   $user
     * @return bool
     * @see Login::doLogin()
     */
    private function credentials($user){
        if(empty($user["hashed_password"]) || empty($user["user_id"])) {
            return false;
        }
        return password_verify($user["password"], $user["hashed_password"]);
    }

    /**
     * check if user has exceeded number of failed logins or number of forgotten password attempts.
     *
     * @param  array   $attempts
     * @return bool
     */
    private function attempts($attempts){

        if(empty($attempts['last_time']) && empty($attempts['count'])) {
            return true;
        }

        $block_time = (10 * 60);
        $time_elapsed = time() - $attempts['last_time'];

        // TODO If user is Blocked, Update failed logins/forgotten passwords
        // to current time and optionally number of attempts to be incremented,
        // but, this will reset the last_time every time there is a failed attempt

        if ($attempts["count"] >= 5 && $time_elapsed < $block_time) {

            // here i can't define a default error message as in defaultMessages()
            // because the error message depends on variables like $block_time & $time_elapsed
            $this->errors[] = "You exceeded number of possible attempts, please try again later after " .
                date("i", $block_time - $time_elapsed) . " minutes";
            return false;

        }else{

            return true;
        }
    }



    /** *********************************************** **/
    /** ************   Default Messages     *********** **/
    /** *********************************************** **/

    /**
     * get default message for a rule
     *
     * Instead of passing your custom message every time,
     * you can define a set of default messages.
     *
     * The pitfall of this method is, if you changed the validation method name,
     * you need to change it here as well.
     *
     * @param  string  $rule
     * @return mixed
     */
    private static function defaultMessages($rule){
        $messages = [
            "required" => "{placeholder} can't be empty",
            "minLen"   => "{placeholder} can't be less than {0} character",
            "maxLen"   => "{placeholder} can't be greater than {0} character",
            "rangeNum" => "{placeholder} must be between {0} and {1}",
            "integer"  => "{placeholder} must be a valid number",
            "inArray"  => "{placeholder} is not valid",
            "alphaNum" => "Only letters and numbers are allowed for {placeholder}",
            "alphaNumWithSpaces" => "Only letters, numbers and spaces are allowed for {placeholder}",
            "password"      => "Passwords must contain at least one lowercase, uppercase, number and special character",
            "equals"        => "{placeholder}s aren't match",
            "notEqual"      => "{placeholder} can't be equal to {0}",
            "email"         => "Invalid email, Please enter a valid email address",
            "unique"        => "{placeholder} already exists",
            "emailUnique"   => "Email already exists",
            "credentials"   => "We do not recognize the email or password",
            "attempts"      => "",
            "fileUnique"    => "File already exists",
            "fileUploaded"  => "Your uploaded file is invalid!",
            "fileErrors"    => "There was an error with the uploaded file",
            "fileSize"      => "",
            "imageSize"     => "",
            "mimeType"      => "Your file format is invalid",
            "fileExtension" => "Your file format is invalid"
        ];

        return isset($messages[$rule])? $messages[$rule]: null;
    }
}