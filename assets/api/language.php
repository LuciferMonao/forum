<?php
session_start();
$hide_frame = true;
require_once $_SERVER["DOCUMENT_ROOT"] . "/forum/assets/class/class.main.php";

if (isset($rargs["language"])) {
    if (in_array($rargs["language"], $info->get_languages())) {
        $_SESSION["language"] = $rargs["language"];
        exit($rargs["language"]);
    } else {
        exit("Languagenotfounderror");
    } 
} else {
    exit("Requesterror");
} 