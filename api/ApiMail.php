<?php
require_once('Api.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

class ApiMail extends Api {
    public function __construct ($email, $subject, $message) {
        mail($email, $subject, $message);
    }
}
