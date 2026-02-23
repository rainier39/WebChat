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

// update.php
// Sends new messages to any connected clients.

require("config.php");

$db = mysqli_connect($config["SQLHost"], $config["SQLUser"], $config["SQLPass"], $config["SQLDB"]);

// Must set these headers for the SSE (Server Sent Events) to work properly.
header("X-Accel-Buffering: no");
header("Content-Type: text/event-stream; charset=utf-8");
header("Cache-Control: no-cache");

// Keep track of time so we know if a message is new or not.
$lastTime = time();

while (true) {
  $result = $db->query("SELECT * FROM `messages` WHERE `mtime`>" . $lastTime . " ORDER BY `id` ASC");

  if ($result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
      // Update the time so we only get messages newer than this.
      $lastTime = $r["mtime"];
      // Send the message to the client.
      echo("data: <div class='msg'>" . $r["name"] . ": " . $r["content"] . "</div>\n\n");
      if (ob_get_contents()) {
        ob_end_flush();
      }
      flush();
    }
  }

  // Stop if the client has disconnected.
  if (connection_aborted()) {
    break;
  }

  // Don't busy-wait (and probably slam the database).
  sleep(0.5);
}

?>
