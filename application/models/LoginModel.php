<?php

namespace app\application\models;

use app\core\components\Validation;
use app\core\Cookie;
use app\core\Logger;
use app\core\Model;
use app\core\Session;

class LoginModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * login
     * 
     * @param string $email|username
     * @param string $password
     * @param bool $rememberMe
     * @param string $userIp
     * @param string $userAgent
     * @return bool
     */
    public function doLogin($email, $password, $rememberMe, $userIp, $userAgent)
    {
        // validate only presence
        $validation = new Validation();
        if (!$validation->validate([
            'Username' => [$email, 'required'],
            'Password' => [$password, 'required']
        ])) {
            $this->errors = $validation->errors();
            return false;
        }
      

        //check if user has previous failed login attempts
        $this->db->prepare("SELECT * FROM failed_logins WHERE email = :email");
        $this->db->bindValue(":email", $email); 
        $this->db->execute();
        $failedLogin = $this->db->fetchAssociative();
    

        $last_time = isset($failedLogin['last_failed_login']) ? $failedLogin['last_failed_login'] : null;
        $count = isset($failedLogin['failed_login_attempts']) ? $failedLogin['failed_login_attempts'] : null;

        // check if the f   ailed login attemps exceeded limits
        
        if (!$validation->validate([
            'Falied Login' => [["last_time" => $last_time, 'count' => $count], 'attempts']
        ])) {

            $this->errors = $validation->errors();
            return false;
        }

        //get user from database
        $this->db->prepare("SELECT * FROM users WHERE email=:email LIMIT 1");
        $this->db->bindValue(':email', $email);
        $this->db->execute();
        $user = $this->db->fetchAssociative();

        $userId = isset($user['id']) ? $user['id'] : null;
        $hashedPassword = isset($user['password']) ? $user['password'] : null;


        //validate data returned from users table
        if (!$validation->validate([
            'Login' => [['user_id' => $userId, 'hashed_password' => $hashedPassword, 'password' => $password,], 'credentials']
        ])) {
            $this->incrementFailedLogins($email, $failedLogin);

            $this->errors = $validation->errors();
            return false;
        }

        //reset session
        Session::reset(['user_id' => $userId, 'role_id' => $user['role_id'], 'ip' => $userIp, "user_agent" => $userAgent,'name'=>$user['name']]);

        //if remember me checkbox is checked, the save data to cookies as well
        if (!empty($rememberMe) && $rememberMe == 'remember_me') { 

            //reset cookie, Cookie token usable only once
            Cookie::reset($userId);
        } else {
            Cookie::remove($userId);
        }

        //if user credentials are valid then, 
        //reset failed logins & forgotten password tokens
        $this->resetFailedLogins($email);
        $this->resetPasswordToken($userId);

        return true;
    }

    /**
     * Increment number of failed logins.
     *
     * @access private
     * @param  string   $email
     * @param  array    $failedLogin It determines if there was a previous record in the database or not
     * @throws Exception If couldn't increment failed logins
     *
     */ 
    private function incrementFailedLogins($email, $failedLogin)
    {
        if (!empty($failedLogin)) {
            $query = "UPDATE failed_logins SET last_failed_login = :last_failed_login, failed_login_attempts =failed_login_attempts+1 WHERE email =:email";
        } else {
            $query = "INSERT INTO failed_logins (email, last_failed_login, failed_login_attempts) VALUES(:email, :last_failed_login, 1)";
        }
        $this->db->prepare($query);
        $this->db->bindValue(":last_failed_login", time());
        $this->db->bindValue(":email", $email);
        $result = $this->db->execute();

        if (!$result) {
            Logger::log("FAILED LOGIN", "Couldn't increment failed logins of user email: " . $email, __FILE__, __LINE__);
            throw new \Exception("FAILED LOGIN : Couldn't increment failed logins of user email: " . $email);
        }
    }

    /**
     * Reset failed logins
     */
    private function resetFailedLogins($email)
    {
        $query = "UPDATE failed_logins SET last_failed_login = NULL, failed_login_attempts = 0 WHERE email=:email";
        $this->db->prepare($query);
        $this->db->bindValue(":email", $email);
        $result = $this->db->execute();

        if (!$result) {
            throw new \Exception("Couldn't Reset the failed logins for user email: " . $email);
        }
    }

    /**
     * Forgot Password
     */
    public function forgotPassword($email)
    {
        $validation = new Validation();
        if (!$validation->validate(['Email' => [$email, 'required|email']])) {
            $this->errors = $validation->errors();
        }

        if ($this->isEmailExists($email)) {

            //depends on the last query made by isEmailExists()
            $user = $this->db->fetchAssociative();

            //TODO check if forgot password last rest attempt and if it is recent show limit exceed

            //You need to get the new password token from the database after updating/inserting it
            $newPasswordToken = $this->generateForgotPasswordToken($user['id']);

            // Email::sendEmail(Config::get('EMAIL_PASSWORD_RESET'), $user['email'], ["id"=>$user["id"], "name"=>$user["name"]], $newPasswordToken);

        }
        //this will return true even if the email doesn't exists
        //because you don't want to give any clue
        //to unauthencated user if email is actually exists or not
        return true;
    }

    /**
     * Generate forgot password token
     */
    private function generateForgotPasswordToken($userId)
    {
        //generate random string 40 char len
        $passwordToken = sha1(uniqid(mt_rand(), true));

        Session::set('forgotpasswordtoken', ["userId" => $userId, "token" => $passwordToken]);

        return $passwordToken;
    }

    /**
     * Check if Password token is valid or not
     */
  /*  public function isForgotPasswordTokenValid($userId, $passwordToken)
    {
        if (empty($userId) || empty($passwordToken)) {
            return false;
        }

        if (isset(Session::get('forgotpasswordtoken'))) {
            $token = Session::get('forgotpasswordtoken');
            if ($userId == $token['userId'] && $passwordToken == $token['token']) {
                return true;
            } else {
                $this->resetPasswordToken($userId);
            }
        }
        Logger::log("PASSWORD TOKEN", "User ID " . $userId . " is trying to reset password using invalid token: " . $passwordToken, __FILE__, __LINE__);
        return false;
    }

    /**
     * Update password after validating the password token
     * 
     */
    public function updatePassword($userId, $password, $confirmPassword)
    {
        $validation = new Validation();
        if (!$validation->validate([
            'Password' => [$password, "required|equals(" . $confirmPassword . ")|minLen(6)|password"],
            'Password Confirmation' => [$confirmPassword, 'required']
        ])) {
            $this->errors = $validation->errors();
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "UPDATE users SET password=:hashed_password WHERE id=:id LIMIT 1";
        $this->db->prepare($query);
        $this->db->bindValue(':hashed_password', $hashedPassword);
        $this->db->bindValue(':id', $userId);
        $result = $this->db->execute();

        if (!$result) {
            throw new \Exception("Coundn't update password");
        }

        //resetting the password token comes ONLY after successful updating password
        $this->resetPasswordToken($userId);
        return true;
    }

    /**
     * Reset the password Token
     * 
     */
    private function resetPasswordToken($userId)
    {
        Session::unsetKey('passwordresettoken');
    }

    /**
     * Checks if email exists and activated in the database or not
     *
     * @access private
     * @param  string  $email
     * @return bool
     *
     */
    private function isEmailExists($email)
    {

        // email is already unique in the database,
        // So, we can't have more than 2 users with the same emails
        $this->db->prepare("SELECT * FROM users WHERE email = :email AND is_email_activated = 1 LIMIT 1");
        $this->db->bindValue(':email', $email);
        $this->db->execute();

        return $this->db->countRows() == 1;
    }

    /**
     * Logout by removing the session and Cookies.
     * 
     */
    public function logout($userId)
    {
        Session::remove();
        Cookie::remove();
    }
}
