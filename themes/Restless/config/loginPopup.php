<?php
require_once('Includes/nkCaptcha.php');

$this->captcha = false;

if(isset($_SESSION['captcha']) && $_SESSION['captcha'] === true){
    $this->captcha = true;
}