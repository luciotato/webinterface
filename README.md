Webinterface of the WPN-XM Server Stack. [![Build Status](https://travis-ci.org/WPN-XM/webinterface.png)](https://travis-ci.org/WPN-XM/webinterface)

Usage
-----

During the installation process of WPN-XM the webinterface is installed to "/server/www/webinterface".
By default the webinterface is served by Nginx, but it may also be served by the embedded PHP server.

a) With Nginx

You might reach the webinterface via the URL http://localhost/webinterface/index.php

Hint: The webinterface will automatically open up in your browser, when the stack is started with "start-wpnxm.bat".

b) With embedded PHP server

When using the embedded PHP server to serve the webinterface, it might be used as a control center application for the server stack.
To start the embedded PHP sever launch the following command on CLI: 
C:\server\bin\php\php -S localhost:90 -t C:\server\www
You might reach the webinterface via the URL http://localhost:90/webinterface/index.php
