<?php

$args = ["title", "text", "author", "name", "mail", "description", "phone", "employment"];
$set = [];
$one_set = false;
foreach($args as $value) {
    if (isset($_GET[$value])) {
        $set[$value] = "checked";
        $one_set = true;
    } else {
        $set[$value] = "";
    }
}

if ($one_set === false) {
    foreach($args as $value) {
        $set[$value] = "checked";
    }
}


if (isset($_GET["search"])) {
    $search = $_GET["search"];
} else {
    $search = $_GET["rsearch"];
}


echo '
    <link rel="stylesheet" href="/forum/assets/style/refined_search.css">

    <form action="/forum/?rsearch=true" method="get" class="refined-search">
        <input type="text" name="rsearch" autocomplete="off" placeholder="Refined Search..." value="' . $search . '"  class="refined-search-text theme-main-color-2">
        <input type="submit" class="refined-search-submit theme-main-color-2" value="->"><br>

        <table class="refined-search-table">
            <tr>
                <th><label for="title" class="refined-search-label">Title</label></th>
                <th><label for="text" class="refined-search-label">Text</label></th>
                <!--<th><label for="author" class="refined-search-label">Author</label></th>-->
                <th><label for="info class="refined-search-label">|</label></th>
                <th><label for="title" class="refined-search-label">Name</label></th>
                <th><label for="text" class="refined-search-label">Mail</label></th>
                <th><label for="title" class="refined-search-label">Description</label></th>
                <th><label for="text" class="refined-search-label">Phone</label></th>
                <th><label for="text" class="refined-search-label">Employment</label><br></th>
            </tr>
            <tr>
                <td><div class="checkbox-div theme-main-color-2" id="1"><input type="checkbox" name="title" ' . $set["title"] . ' autocomplete="off" class="refined-search-checkbox"></div></td>
                <td><div class="checkbox-div theme-main-color-2" id="2"><input type="checkbox" name="text" ' . $set["text"] . ' autocomplete="off" class="refined-search-checkbox"></div></td>
                <!--<td><div class="checkbox-div theme-main-color-2" id="3"><input type="checkbox" name="author" ' . $set["author"] . ' autocomplete="off" class="refined-search-checkbox"></div></td>-->
                <td><p></p></td>
                <td><div class="checkbox-div theme-main-color-2" id="4"><input type="checkbox" name="name" ' . $set["name"] . ' autocomplete="off" class="refined-search-checkbox"></div></td>
                <td><div class="checkbox-div theme-main-color-2" id="5"><input type="checkbox" name="mail" ' . $set["mail"] . ' autocomplete="off" class="refined-search-checkbox"></div></td>
                <td><div class="checkbox-div theme-main-color-2" id="6"><input type="checkbox" name="description" ' . $set["description"] . ' autocomplete="off" class="refined-search-checkbox"></div></td>
                <td><div class="checkbox-div theme-main-color-2" id="7"><input type="checkbox" name="phone" ' . $set["phone"] . ' autocomplete="off" class="refined-search-checkbox"></div></td>
                <td><div class="checkbox-div theme-main-color-2" id="8"><input type="checkbox" name="employment" ' . $set["employment"] . ' autocomplete="off" class="refined-search-checkbox"></div></td>
            </tr>
        </table>
    </form>   
';

for ($i = 1; $i < 9; $i++) {
    if ($i === 3) {continue;}
    echo '
    <script>
        document.getElementById("' . $i . '").addEventListener("click", event => {
            if (document.getElementById("' . $i . '").children[0].checked) {
                document.getElementById("' . $i . '").classList.add("refined-search-color");
            } else {
                document.getElementById("' . $i . '").classList.remove("refined-search-color");
            }
        });
        if (document.getElementById("' . $i . '").children[0].checked) {
            document.getElementById("' . $i . '").classList.add("refined-search-color");
        } else {
            document.getElementById("' . $i . '").classList.remove("refined-search-color");
        }
    </script>';
}