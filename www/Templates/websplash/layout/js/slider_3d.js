var flashvars = {};
flashvars.cssSource = "layout/plugins_styles/piecemaker.css";
flashvars.xmlSource = "layout/plugins_styles/piecemaker.xml";

var params = {};
params.play = "true";
params.menu = "false";
params.scale = "showall";
params.wmode = "transparent";
params.allowfullscreen = "true";
params.allowscriptaccess = "always";
params.allownetworking = "all";

swfobject.embedSWF('layout/plugins_styles/piecemaker.swf', 'piecemaker', '1000', '322', '10', null, flashvars,    
params, null);