<?php

if (isset($_GET["articleId"])) {
    $articleId = $_GET["articleId"];
    if (isset($_GET["articleTitle"]) && intval($data->get_id_by_articletitle($_GET["articleTitle"])) !== intval($_GET["articleId"])) {
        header("LOCATION:/forum/?error=requesterror");
        die("Requesterror");
    }
} else {
    $articleId = intval($data->get_id_by_articletitle($_GET["articleTitle"]));
}

$article_data = $data->get_article_by_id($articleId);
if ($article_data === false) {
    header("LOCATION:/forum/?error=notexistentarticle");
    die("This article does not exist");
}

if (isset($_SESSION["userId"])) {
    $data->create_article_view($_SESSION["userId"], $articleId);
    if ($data->check_if_article_liked_by_user($_SESSION["userId"], $articleId)) {
        $liked = " liked";
    } else {
        $liked = "";
    }
}


if (isset($_SESSION["userId"]) && (($data->is_admin_by_id($_SESSION["userId"]) && !$data->is_admin_by_id($article_data["userId"])) || $_SESSION["userId"] === $article_data["userId"])) {
    $delete_button = '<div class="delete-article">Delete</div>
    <script>document.querySelector(".delete-article").addEventListener("click", (e) => {window.location = "/forum/assets/site/delete.php?articleId=' . $articleId . '&refer=/forum/";})</script>';
} else {
    $delete_button = "";
}

// USE VARIABLE TO REDUCE QUERY AMOUNT
$try_author_name = $data->get_username_by_id($article_data["userId"]);

if ($try_author_name && $try_author_name !== "") {
    $author = "Author: " . $try_author_name;
} else {
    $author = "The author of this article was deleted...";
}


if ($data->get_user_by_id($article_data["userId"])["userVerified"] == "1") {
    $verified = '<p class="verified">&#10003</p>';
} else {
    $verified = "";
}

echo '
<link rel="stylesheet" href="/forum/assets/style/pc.article.css">



<div class="article-block">
    <div class="like-article ' . $liked . '">Like</div>
    ' . $delete_button . '
    <textarea disabled class="article-block-entry article-block-title">' . $article_data["articleTitle"] . '</textarea>' . $verified . '
    <textarea disabled class="article-block-entry article-block-author">' . $author . '</textarea>
    <textarea disabled class="article-block-entry article-block-tags">Tags: ' . implode("; ", json_decode($article_data["articleTags"])) . '</textarea>
    <textarea disabled class="article-block-entry article-block-created">Created: ' . $article_data["articleCreated"] . '</textarea>

    <textarea disabled class="article-block-content">' . $article_data["articleText"] . '</textarea>';


$comments = $data->get_article_comments_by_id($articleId);

echo '<div class="comment-section">';

if (isset($_SESSION["userId"])) {
    echo '<form class="comment-form comment" method="post" action="/forum/assets/site/comment.php?articleId=' . $articleId . '">';
    echo '<input class="comment-title" name="title" placeholder="Title">';
    echo '<h3 class="comment-author">' . $data->get_username_by_id($_SESSION["userId"]) . '</h3>';
    echo '<input class="comment-text" name="text" placeholder="Your comment..."></input>';
    echo '<input type="submit" name="submit" class="comment-form-submit">';
    echo '</form>';
}

foreach($comments as $row) {
    echo '<div class="comment">';
    echo '<h3 class="comment-title">' . $row["commentTitle"] . '</h3>';
    echo '<h3 class="comment-author">' . $data->get_username_by_id($row["userId"]) . '</h3>';
    echo '<textarea class="comment-text" disabled>' . $row["commentText"] . '</textarea>';
    if (isset($_SESSION["userId"]) && ($data->is_admin_by_id($_SESSION["userId"]) || intval($row["userId"]) === intval($_SESSION["userId"]))) {
        echo '<button class="comment-delete" id="comment-element-' . intval($row["commentId"] * 1342 + 234) . '">Delete</button>';
        echo '
        <script>
        document.getElementById("comment-element-' . intval($row["commentId"] * 1342 + 234) . '").addEventListener("click", (e) => {
            window.location = "/forum/assets/site/delete_comment.php?type=article&articleId=' . $articleId . '&commentId=' . $row["commentId"] . '";
        });
        </script>';
    }
    echo '</div>';
}


echo '
</div></div>



<script>document.querySelector(".like-article").addEventListener("click", (e) => {window.location = "/forum/assets/site/like.php?articleId=' . $articleId . '&refer=/forum/?articleId=' . $articleId . '";})</script>
';


