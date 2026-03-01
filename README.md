# WebChat
This is a simple PHP instant messaging chatroom application. The frontend consists of HTML, CSS, and JavaScript. It uses SSE (Server Sent Events) to allow clients to receive new messages in nearly real-time. SSE is a neat feature as it allows this to happen without the client having to constantly poll for new messages. Instead, the client may maintain a connection to the server and listen for new messages.

As of current, this software may be used in production. It lacks rate limiting, so message spamming is possible. However, a limited amount of messages are stored by the software (configurable). Also note that there is nothing stopping users from sharing the same name, thereby making "impersonation" possible. The software is incredibly simple and not meant to be taken too seriously.

This is free software, I will develop it at my leisure. This is not one of my big projects so it won't likely be updated often. Contributions are welcome, however, and bug reports/feature suggestions will be considered.

Has only been tested with Apache2 on Debian 13. More detailed version information to be posted. Should work on various HTTP server software, on at least most Linux distributions if not most OSes.
