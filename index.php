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

$errors = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["message"]) and isset($_POST["name"])) {
    if (strlen($_POST["message"]) < 1) {
      $errors .= "<div class='error'>Your message cannot be blank.</div>";
    }
    elseif (strlen($_POST["message"]) > 2048) {
      $errors .= "<div class='error'>Your message is too long.</div>";
    }
    if (strlen($_POST["name"]) < 1) {
      $errors .= "<div class='error'>Your name cannot be blank.</div>";
    }
    elseif (strlen($_POST["name"]) > 32) {
      $errors .= "<div class='error'>Your name is too long.</div>";
    }
    if ($errors == "") {
      // Set the user's name if it's different from their current one.
      if ($_SESSION["name"] != $_POST["name"]) $_SESSION["name"] = $_POST["name"];
      if ($db->query("INSERT INTO `messages` (content, mtime, name) VALUES ('" . $db->real_escape_string($_POST["message"]) . "', '" . time() . "', '" . $db->real_escape_string($_POST["name"]) . "')") === true) {
        // Delete old messages.
        $db->query("DELETE FROM `messages` WHERE `id`<=" . $db->insert_id . "-" . (int)$config["maxMessages"]);
      }
    }
  }
}

$content = "";

$result = $db->query("SELECT * FROM `messages` ORDER BY `id` DESC LIMIT " . (int)$config["maxMessages"]);

while ($r = $result->fetch_assoc()) {
  // Add the content backwards because we're going through the n newest messages in reverse order (DESC).
  $content = "<div class='msg'>" . htmlspecialchars($r["name"]) . ": " . htmlspecialchars($r["content"]) . "</div>" . $content;
}

// Serve the HTML document.
// The JavaScript is defered so that it is executed after the page has been parsed.
echo("<!DOCTYPE html>
<html lang='en-US'>
 <head>
  <meta charset='UTF-8'>
  <title>" . htmlspecialchars($config["chatName"]) . "</title>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <link rel='stylesheet' href='chat.css'>
  <script src='chat.js' defer></script>
 </head>
 <body>
  <div class='errors'>
  " . $errors . "
  </div>
  <h1>" . htmlspecialchars($config["chatName"]) . "</h1>
  <div id='chat'>
    " . $content . "
  </div>
  <div class='msgbox'>
  <form method='post' autocomplete='off'>
    <input type='username' class='namebox' name='name' value='" . htmlspecialchars($_SESSION["name"]) . "' maxlength='32' required>
    <input type='text' class='textbox' name='message' maxlength='2048' autofocus required>
    <input type='submit' value='Send'>
  </form>
  </div>
 </body>
</html>");

?>
