if(typeof infosoftglobal=="undefined")var infosoftglobal={};if(typeof infosoftglobal.FusionChartsUtil=="undefined")infosoftglobal.FusionChartsUtil={};
infosoftglobal.FusionCharts=function(a,b,c,d,e,f,i,g,h,j,k){if(document.getElementById){this.initialDataSet=false;this.params={};this.variables={};this.attributes=[];a&&this.setAttribute("swf",a);b&&this.setAttribute("id",b);e=e?e:0;this.addVariable("debugMode",e);(c=c.toString().replace(/\%$/,"%25"))&&this.setAttribute("width",c);(d=d.toString().replace(/\%$/,"%25"))&&this.setAttribute("height",d);i&&this.addParam("bgcolor",i);this.addParam("quality","high");this.addParam("allowScriptAccess","always");
this.addVariable("chartWidth",c);this.addVariable("chartHeight",d);this.addVariable("DOMId",b);f=f?f:0;this.addVariable("registerWithJS",f);g=g?g:"noScale";this.addVariable("scaleMode",g);h=h?h:"EN";this.addVariable("lang",h);this.detectFlashVersion=j?j:1;this.autoInstallRedirect=k?k:1;this.installedVer=infosoftglobal.FusionChartsUtil.getPlayerVersion();if(!window.opera&&document.all&&this.installedVer.major>7)infosoftglobal.FusionCharts.doPrepUnload=true}};
infosoftglobal.FusionCharts.prototype={setAttribute:function(a,b){this.attributes[a]=b},getAttribute:function(a){return this.attributes[a]},addParam:function(a,b){this.params[a]=b},getParams:function(){return this.params},addVariable:function(a,b){this.variables[a]=b},getVariable:function(a){return this.variables[a]},getVariables:function(){return this.variables},getVariablePairs:function(){var a=[],b,c=this.getVariables();for(b in c)a.push(b+"="+c[b]);return a},getSWFHTML:function(){var a="";if(navigator.plugins&&
navigator.mimeTypes&&navigator.mimeTypes.length){a='<embed type="application/x-shockwave-flash" src="'+this.getAttribute("swf")+'" width="'+this.getAttribute("width")+'" height="'+this.getAttribute("height")+'"  ';a+=' id="'+this.getAttribute("id")+'" name="'+this.getAttribute("id")+'" ';var b=this.getParams();for(var c in b)a+=[c]+'="'+b[c]+'" ';b=this.getVariablePairs().join("&");if(b.length>0)a+='flashvars="'+b+'"';a+="/>"}else{a='<object id="'+this.getAttribute("id")+'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+
this.getAttribute("width")+'" height="'+this.getAttribute("height")+'">';a+='<param name="movie" value="'+this.getAttribute("swf")+'" />';b=this.getParams();for(c in b)a+='<param name="'+c+'" value="'+b[c]+'" />';b=this.getVariablePairs().join("&");if(b.length>0)a+='<param name="flashvars" value="'+b+'" />';a+="</object>"}return a},setDataURL:function(a){if(this.initialDataSet==false){this.addVariable("dataURL",a);this.initialDataSet=true}else{var b=infosoftglobal.FusionChartsUtil.getChartObject(this.getAttribute("id"));
b.setDataURL||__flash__addCallback(b,"setDataURL");b.setDataURL(a)}},encodeDataXML:function(a){var b=a.match(/=\s*\".*?\"/g);if(b)for(var c=0;c<b.length;c++){var d=b[c].replace(/^=\s*\"|\"$/g,"");d=d.replace(/\'/g,"%26apos;");var e=a.indexOf(b[c]);d="='"+d+"'";var f=a.substring(0,e);a=a.substring(e+b[c].length);a=f+d+a}a=a.replace(/\"/g,"%26quot;");a=a.replace(/%(?![\da-f]{2}|[\da-f]{4})/ig,"%25");return a=a.replace(/\&/g,"%26")},setDataXML:function(a){if(this.initialDataSet==false){this.addVariable("dataXML",
this.encodeDataXML(a));this.initialDataSet=true}else infosoftglobal.FusionChartsUtil.getChartObject(this.getAttribute("id")).setDataXML(a)},setTransparent:function(a){if(typeof a=="undefined")a=true;a?this.addParam("WMode","transparent"):this.addParam("WMode","Opaque")},render:function(a){if(this.detectFlashVersion==1&&this.installedVer.major<8)if(this.autoInstallRedirect==1)if(window.confirm("You need Adobe Flash Player 8 (or above) to view the charts. It is a free and lightweight installation from Adobe.com. Please click on Ok to install the same."))window.location=
"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash";else return false;else return false;else{a=typeof a=="string"?document.getElementById(a):a;this.getVariable("scaleMode").search(/noscale/i)>=0&&(this.getAttribute("width").search("%")>0||this.getAttribute("height").search("%"));a.innerHTML=this.getSWFHTML();if(!document.embeds[this.getAttribute("id")]&&!window[this.getAttribute("id")])window[this.getAttribute("id")]=document.getElementById(this.getAttribute("id"));
return true}}};
infosoftglobal.FusionChartsUtil.getPlayerVersion=function(){var a=new infosoftglobal.PlayerVersion([0,0,0]);if(navigator.plugins&&navigator.mimeTypes.length){var b=navigator.plugins["Shockwave Flash"];if(b&&b.description)a=new infosoftglobal.PlayerVersion(b.description.replace(/([a-zA-Z]|\s)+/,"").replace(/(\s+r|\s+b[0-9]+)/,".").split("."))}else if(navigator.userAgent&&navigator.userAgent.indexOf("Windows CE")>=0){b=1;for(var c=3;b;)try{c++;b=new ActiveXObject("ShockwaveFlash.ShockwaveFlash."+c);
a=new infosoftglobal.PlayerVersion([c,0,0])}catch(d){b=null}}else{try{b=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7")}catch(e){try{b=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");a=new infosoftglobal.PlayerVersion([6,0,21]);b.AllowScriptAccess="always"}catch(f){if(a.major==6)return a}try{b=new ActiveXObject("ShockwaveFlash.ShockwaveFlash")}catch(i){}}if(b!=null)a=new infosoftglobal.PlayerVersion(b.GetVariable("$version").split(" ")[1].split(","))}return a};
infosoftglobal.PlayerVersion=function(a){this.major=a[0]!=null?parseInt(a[0]):0;this.minor=a[1]!=null?parseInt(a[1]):0;this.rev=a[2]!=null?parseInt(a[2]):0};infosoftglobal.FusionChartsUtil.cleanupSWFs=function(){for(var a=document.getElementsByTagName("OBJECT"),b=a.length-1;b>=0;b--){a[b].style.display="none";for(var c in a[b])if(typeof a[b][c]=="function")a[b][c]=function(){}}};
if(infosoftglobal.FusionCharts.doPrepUnload)if(!infosoftglobal.unloadSet){infosoftglobal.FusionChartsUtil.prepUnload=function(){__flash_unloadHandler=function(){};__flash_savedUnloadHandler=function(){};window.attachEvent("onunload",infosoftglobal.FusionChartsUtil.cleanupSWFs)};window.attachEvent("onbeforeunload",infosoftglobal.FusionChartsUtil.prepUnload);infosoftglobal.unloadSet=true}if(!document.getElementById&&document.all)document.getElementById=function(a){return document.all[a]};
if(Array.prototype.push==null)Array.prototype.push=function(a){this[this.length]=a;return this.length};infosoftglobal.FusionChartsUtil.getChartObject=function(a){var b=null;(b=navigator.appName.indexOf("Microsoft Internet")==-1?document.embeds&&document.embeds[a]?document.embeds[a]:window.document[a]:window[a])||(b=document.getElementById(a));return b};var getChartFromId=infosoftglobal.FusionChartsUtil.getChartObject,FusionCharts=infosoftglobal.FusionCharts;
