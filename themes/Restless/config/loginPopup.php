<?php
$this->assign('captcha', false);

$captcha = initCaptcha();

if ((isset($_SESSION['captcha']) && $_SESSION['captcha'] === true) || !empty($catpcha)) {
    $this->assign('captcha', true);
}
?>