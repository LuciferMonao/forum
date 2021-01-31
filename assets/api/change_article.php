<?php 
session_start();
$hide_frame = true;
$require_purifier = true;
require_once $_SERVER["DOCUMENT_ROOT"] . "/forum/assets/class/class.main.php";

if (!$data->is_logged_in()) {
    exit("Formerror");
}


if (!isset($rargs["articleText"]) || !isset($rargs["articleId"])) {
    exit("Formerror");
}


$data->change_article_column_by_id_and_name($rargs["articleId"], "articleText", $filter->purify($rargs["articleText"], 25));

exit();