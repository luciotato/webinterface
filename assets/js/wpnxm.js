<!-- Browser Update Notifier - async -->
var $buoop = {};
$buoop.ol = window.onload;
window.onload=function()
{
    try {if ($buoop.ol) $buoop.ol();}catch (e) {}
    var e = document.createElement("script");
    e.setAttribute("type", "text/javascript");
    <!--e.setAttribute("src", "http://browser-update.org/update.js");-->
    e.setAttribute("src", "http://localhost/webinterface/assets/js/browser-update.js");
    document.body.appendChild(e);
}