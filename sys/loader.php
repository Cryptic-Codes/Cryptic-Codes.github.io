<?php
if($page_path === "/" || empty($page_path)) {
    require("pages/index.php");
} elseif(is_file("pages/" . $page_path . ".php")) {
    require("pages/" . $page_path . ".php");
} elseif(is_file("pages/" . $page_path . "/index.php")) {
    require("pages/" . $page_path . ".php");
} else {
    echo "404";
}