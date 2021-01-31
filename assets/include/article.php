<?php

if (isset($_GET["articleId"])) {
    $articleId = $_GET["articleId"];
    if (isset($_GET["articleTitle"]) && intval($data->get_article_id_by_title($_GET["articleTitle"])) !== intval($_GET["articleId"])) {
        header("LOCATION:/forum/?error=requesterror");
        die("<script>window.location='/forum/?error=requesterror';</script>");
    }
} else {
    $articleId = intval($data->get_article_id_by_title($_GET["articleTitle"]));
}

$article_data = $data->get_article_by_id($articleId);
if ($article_data === false) {
    header("LOCATION:/forum/?error=notexistentarticle");
    die("<script>window.location='/forum/?error=notexistentarticle';</script>");
}

if ($data->is_logged_in()) {
    $data->create_article_view($_SESSION["userId"], $articleId);
    if ($data->check_if_article_liked_by_user($_SESSION["userId"], $articleId)) {
        $liked = " liked";
    } else {
        $liked = "";
    }
}

if ($data->is_logged_in()) {
    $settings = '<img class="user-settings" src="https://img.icons8.com/material-rounded/1024/000000/settings.png"/><script>document.querySelector(".user-settings").addEventListener("click", (e) => {document.querySelector(".main-menu").style.display = "none"; if (document.querySelector(".user-settings-menu").style.display === "") {document.querySelector(".user-settings-menu").style.display = "none"} else {document.querySelector(".user-settings-menu").style.display = "";}})</script>';
    $settings_menu = '<div class="user-settings-menu theme-main-color-2" style="display: none;">';
    if ((($data->is_admin_by_id($_SESSION["userId"]) && !$data->is_admin_by_id($userId)) || intval($article_data["userId"]) === intval($_SESSION["userId"]))) {
        $settings_menu .= '<div class="edit-btn hover-theme-main-color-1">' . $text->get("user-view-edit") . '</div><script src="/forum/assets/script/edit.js" defer></script>';
    }
    if ((($data->is_admin_by_id($_SESSION["userId"]) && !$data->is_admin_by_id($userId)) || intval($article_data["userId"]) === intval($_SESSION["userId"]))) {
        $settings_menu .= '<div class="delete-btn hover-theme-main-color-1">' . $text->get("user-view-delete") . '</div><script src="/forum/assets/script/delete.js"></script>';
    }
    $settings_menu .= '</div>';
} 


if ($data->is_logged_in() && (($data->is_admin_by_id($_SESSION["userId"]) && !$data->is_admin_by_id($article_data["userId"])) || $_SESSION["userId"] === $article_data["userId"])) {
    $delete_button = '<div class="delete-btn">' . $text->get("article-view-delete") . '</div>
    <script src="/forum/assets/script/delete.js"></script>';
} else {
    $delete_button = "";
}

// USE VARIABLE TO REDUCE QUERY AMOUNT
$try_author_name = $data->get_username_by_id($article_data["userId"]);

if ($try_author_name && $try_author_name !== "") {
    $author = $text->get("article-view-author") . $try_author_name;
} else {
    $author = $text->get("article-view-author-deleted");
}


if ($data->get_user_by_id($article_data["userId"])["userVerified"] == "1") {
    $verified = '<p class="verified">&#10003</p>';
} else {
    $verified = "";
}

if ($info->mobile === true) {
    echo '<link rel="stylesheet" href="/forum/assets/style/mobile.article.css">';
    $l1 = '<div class="like-btn ' . $liked . '">' . $text->get("article-view-like") . '</div>';
    $l2 = '';
} else {
    echo '<link rel="stylesheet" href="/forum/assets/style/pc.article.css">';
    $l1 = '';
    $l2 = '<div class="like-btn ' . $liked . '">' . $text->get("article-view-like") . '</div>';
}


echo '
    ' . $l1 . '
    <div class="article-block theme-main-color-1 article-background-element">
    ' . $l2 . '
    <script src="/forum/assets/script/like.js" defer></script>
    ' . $settings . '
    ' . $settings_menu . '
    <div class=" article-block-entry article-block-title">' . htmlspecialchars($article_data["articleTitle"]) . '</div>' . $verified . '
    <button id="userSubmit" class="theme-main-color-2 submitButton" style="display: none;">Save</button>
    <a class="author-href" href="/forum/?userId=' . $article_data["userId"] . '"><div class="article-block-entry article-block-author">' . htmlspecialchars($author) . '</div></a>
    <div class=" article-block-entry article-block-tags">' . $text->get("article-view-tags") . htmlspecialchars(implode("; ", json_decode($article_data["articleTags"]))) . '</div>
    <div class=" article-block-entry article-block-created">' . $text->get("article-view-created") . $article_data["articleCreated"] . '</div>

    <textarea disabled id="article-content" class="article-block-content">' . htmlspecialchars($article_data["articleText"]) . '</textarea>
    <script>document.getElementById("article-content").style.height = (document.getElementById("article-content").scrollHeight + 10) + \'px\';</script>';

$comments = $data->get_article_comments_by_id($articleId);

echo '<div class="comment-section theme-main-color-1">';

echo '<h3 id="loading-comments-info">' . $text->get("comments-loading") . '</h3>';

// cur_ID and cur_username used in like, delete and verify
if ($data->is_logged_in()) {
    echo '<form class="comment-form comment theme-main-color-1">';
    echo '<input class="comment-title theme-main-color-1" name="title" placeholder="' . $text->get("comments-title") . '">';
    echo '<h3 class="comment-author theme-main-color-1">' . $data->get_username_by_id($_SESSION["userId"]) . '</h3>';
    echo '<input class="comment-text theme-main-color-1" name="text" placeholder="' . $text->get("comments-comment") . '"></input>';
    echo '<input type="submit" name="submit" class="comment-form-submit theme-main-color-1" id="submit-comment" value="' . $text->get("comments-submit") . '">';
    echo '</form>
    <script>var cur_Id = "articleId=" + "' . $articleId . '";</script>
    <script>var cur_username = "' . $_SESSION["user"] . '";</script>
    <script async defer src="/forum/assets/script/comment.js"></script>
    <div id="js_comments"></div>
    ';
} else {
    echo '<script>var cur_Id = "articleId=" + "' . $articleId . '";</script>
    <script>var cur_username = false;</script>
    <script async defer src="/forum/assets/script/comment.js"></script>
    <div id="js_comments"></div>';
}



echo '
</div></div>
';


