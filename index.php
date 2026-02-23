<?php
/*
 * Copyright © 2026 rainier39 <rainier39@proton.me>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

// index.php
// Main page for the application.

require("config.php");

$db = mysqli_connect($config["SQLHost"], $config["SQLUser"], $config["SQLPass"], $config["SQLDB"]);

$db->query("CREATE TABLE IF NOT EXISTS `messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(2048) DEFAULT NULL,
  `mtime` bigint NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

session_name("WebChat");
session_start([
  "cookie_httponly" => true,
  "cookie_samesite" => "strict",
]);

// Set a random name for the user if there isn't one (I.E. first page load).
$_SESSION["name"] = $_SESSION["name"] ?? "user" . rand(1000, 9999);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Note lack of error handling, lack of bounds checking. TODO
  if (isset($_POST["message"]) and isset($_POST["name"])) {
    if ($_SESSION["name"] != $_POST["name"]) $_SESSION["name"] = $_POST["name"];
    $db->query("INSERT INTO `messages` (content, mtime, name) VALUES ('" . $db->real_escape_string($_POST["message"]) . "', '" . time() . "', '" . $db->real_escape_string($_POST["name"]) . "')");
  }
}

$content = "";

$result = $db->query("SELECT * FROM `messages` ORDER BY `id` DESC LIMIT 50");

while ($r = $result->fetch_assoc()) {
  // Add the content backwards because we're going through the 50 newest messages in reverse order (DESC).
  $content = "<div class='msg'>" . htmlspecialchars($r["name"]) . ": " . htmlspecialchars($r["content"]) . "</div>" . $content;
}

// Serve the HTML document.
// The JavaScript is defered so that it is executed after the page has been parsed.
echo("<!DOCTYPE html>
<html lang='en-US'>
 <head>
  <meta charset='UTF-8'>
  <title>WebChat</title>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <link rel='stylesheet' href='chat.css'>
  <script src='chat.js' defer></script>
 </head>
 <body>
  <h1>WebChat</h1>
  <div id='chat'>
    " . $content . "
  </div>
  <div class='msgbox'>
  <form method='post'>
    <input type='username' name='name' value='" . htmlspecialchars($_SESSION["name"]) . "' maxlength='32' required>
    <input type='text' name='message' maxlength='2048' autofocus required>
    <input type='submit' value='Send'>
  </form>
  </div>
 </body>
</html>");

?>
