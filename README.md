# WebChat
This is a simple PHP instant messaging chatroom application. The frontend consists of HTML, CSS, and JavaScript. It uses SSE (Server Sent Events) to allow clients to receive new messages in nearly real-time. SSE is a neat feature as it allows this to happen without the client having to constantly poll for new messages. Instead, the client may maintain a connection to the server and listen for new messages.

As of current, this is not production software. There are certainly a few bugs to be worked out, along with security issues. Since it is such a small application, it should be finished somewhat soon.

Has only been tested with Apache2 on Debian 13. More detailed version information to be posted.
