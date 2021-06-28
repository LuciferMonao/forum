<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/forum/assets/class/class.data.php";


class DataV2 extends Data {
    public function get_categories () {
        return array("Home", "About", "Discussion", "Projects", "Help");
    }

    public function get_articleIds_by_category ($category, $orderby="articleCreated", $dir="DESC", $start=0, $limit=100000)
    {
        if (!in_array($dir, ["DESC", "ASC"])) {
            $dir = "ASC";
        }
        if (!in_array($orderby, ["articleCreated", "articleId", "userId", "articleTitle", "articleText"])) {
            $orderby = "articleCreated";
        }
        $query = '
        SELECT articleId, articleTitle, userId, articleCreated, articlePinned
        FROM articles
        WHERE articleCategory=?
        ORDER BY ' . $orderby . ' ' . $dir . '
        LIMIT ?, 
        ?;
        ';
        $stmt = $this->connId->prepare($query);
        $stmt->bind_param("sii", $category, $start, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $return = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (!in_array($row, $return)) {
                    array_push($return, array_merge($row, ["articleViews" => $this->get_article_views_by_article_id($row["articleId"]), "articleComments" => $this->get_article_comment_number_by_id($row["articleId"]), "articleLikes" => $this->get_article_likes_by_article_id($row["articleId"])]));
                }
            }
        }
        if (count($return) >= 1) {
            return $return;
        } else {
            return false;
        }
    }


    public function get_usernames () {
        $query = '
        SELECT userId, userName
        FROM users
        ORDER BY userId DESC;
        ';
        $stmt = $this->connId->prepare($query);
        $stmt->bind_param("");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $return = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (!in_array($row, $return)) {
                    array_push($return, $row);
                }
            }
        }
        if (count($return) >= 1) {
            return $return;
        } else {
            return false;
        }
    }


    public function toggle_article_pin ($articleId) {
        if ($this->check_article_pin($articleId)) {
            $this->change_article_column_by_id_and_name($articleId, "articlePinned", 0);
        } else {
            $this->change_article_column_by_id_and_name($articleId, "articlePinned", 1);
        }
    }


    public function check_article_pin ($articleId) {
        $query = '
        SELECT articlePinned
        FROM articles
        WHERE articleId=?;
        ';
        $stmt = $this->connId->prepare($query);
        $stmt->bind_param("i", $articleId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                return (strval($row["articlePinned"]) == "1");
            }
        }
    }


    public function add_log ($type, $text, $ip, $browser, $userId)
    {
        $time = time();
        $query = "INSERT INTO logs (logType, logContent, logDate, logIp, logBrowser, userId) VALUES (?, ?, ?, ?, ?, ?);";
        $stmt = $this->connId->prepare($query);
        $stmt->bind_param("ssisss", $type, $text, $time, $ip, $browser, $userId);
        $stmt->execute();
        $stmt->close();
        return true;
    }
}