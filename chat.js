document.addEventListener("DOMContentLoaded", function() {
const x = document.getElementById("chat");
var source = new EventSource("update.php");
source.onmessage = function(event) {
  x.innerHTML += event.data;
  scrollToBottom("chat");
};
window.addEventListener("beforeunload", function() {
  source.close();
});
});

function scrollToBottom(element) {
  var e = document.getElementById(element);
  e.scrollTop = e.scrollTopMax;
}

// We want to do this when the page gets loaded.
scrollToBottom("chat");
