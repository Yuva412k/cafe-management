<?php

/**
 * This file contains configuration for the application
 */

 return array(

    "DB_HOST" =>getenv('DB_HOST') ?? "localhost",
    "DB_NAME" => getenv('DB_NAME') ??"cafemanagement",
    "DB_USER" => getenv('DB_USER') ?? "root",
    "DB_PASS" => getenv('DB_PASSWORD') ?? null,
    "DB_PORT" => getenv('DB_PORT') ?? 3306,

    "BASE_URL" => "/",
    "VIEWS_PATH" => APP."views/",
    "ERRORS_PATH" => APP."views/errors/",

    "DEFAULT_CONTROLLER" => "Login",
    "DEFAULT_METHOD" => "index",
    "CONTROLLER_NAMESPACE" => "app\\application\\controllers\\",

    "COOKIE_EXPIRY" => 7200,
    "SESSION_COOKIE_EXPIRY" => 7200,
    "COOKIE_DOMAIN" => '',
    "COOKIE_PATH" => '/',
    "COOKIE_SECURE" => false,
    "COOKIE_HTTP" => true,
    "COOKIE_SECRET_KEY" => 'c&m1-0s!^a{fr3823@kjlfas]#jE9+%32',
    

    "ENCRYPTION_KEY" => "3¥‹a0cd@!$251Êìcef08%&",

    "EMAIL_SMTP_DEBUG" => 2,
    "EMAIL_SMTP_AUTH" => true,
    "EMAIL_SMTP_SECURE" => "ssl",
    "EMAIL_SMTP_HOST" => "YOURSMTPHOST",
    "EMAIL_SMTP_PORT" => 465,
    "EMAIL_SMTP_USERNAME" => "YOURUSERNAME",
    "EMAIL_SMTP_PASSWORD" => "YOURPASSWORD",
    "EMAIL_FROM" => "info@YOURDOMAIN.com",
    "EMAIL_FROM_NAME" => "mini PHP",
    "EMAIL_REPLY_TO" => "no-reply@YOURDOMAIN.com",
    "ADMIN_EMAIL" => "YOUREMAIL",


    // "EMAIL_EMAIL_VERIFICATION" => "1",
    // "EMAIL_EMAIL_VERIFICATION_URL" => PUBLIC_ROOT . "Login/verifyUser",
    // "EMAIL_EMAIL_VERIFICATION_SUBJECT" => "[IMP] Please verify your account",


    // "EMAIL_REVOKE_EMAIL" => "2",
    // "EMAIL_REVOKE_EMAIL_URL" => PUBLIC_ROOT . "User/revokeEmail",
    // "EMAIL_REVOKE_EMAIL_SUBJECT" => "[IMP] Your email has been changed",


    // "EMAIL_UPDATE_EMAIL" => "3",
    // "EMAIL_UPDATE_EMAIL_URL" => PUBLIC_ROOT . "User/updateEmail",
    // "EMAIL_UPDATE_EMAIL_SUBJECT" => "[IMP] Please confirm your new email",


    // "EMAIL_PASSWORD_RESET" => "4",
    // "EMAIL_PASSWORD_RESET_URL" => PUBLIC_ROOT . "Login/resetPassword",
    // "EMAIL_PASSWORD_RESET_SUBJECT" => "[IMP] Reset your password",



    "EMAIL_REPORT_BUG" => "5",
    "EMAIL_REPORT_BUG_SUBJECT" => "Request",


);
