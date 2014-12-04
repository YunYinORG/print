(function(window){"use strict";var WeixinApi={version:2.9};window.WeixinApi=WeixinApi;if(typeof define==='function'&&(define.amd||define.cmd)){if(define.amd){define(function(){return WeixinApi;});}else if(define.cmd){define(function(require,exports,module){module.exports=WeixinApi;});}}
var _share=function(cmd,data,callbacks){callbacks=callbacks||{};var progress=function(resp){switch(true){case/\:cancel$/i.test(resp.err_msg):callbacks.cancel&&callbacks.cancel(resp);break;case/\:(confirm|ok)$/i.test(resp.err_msg):callbacks.confirm&&callbacks.confirm(resp);break;case/\:fail$/i.test(resp.err_msg):default:callbacks.fail&&callbacks.fail(resp);break;}
callbacks.all&&callbacks.all(resp);};var handler=function(theData,argv){if(cmd.menu==='menu:general:share'){if(argv.shareTo=='favorite'){if(callbacks.favorite===false){return argv.generalShare(theData,function(){});}}
argv.generalShare(theData,progress);}else{WeixinJSBridge.invoke(cmd.action,theData,progress);}};WeixinJSBridge.on(cmd.menu,function(argv){if(callbacks.async&&callbacks.ready){var _callbackKey="_wx_loadedCb_";WeixinApi[_callbackKey]=callbacks.dataLoaded||new Function();if(WeixinApi[_callbackKey].toString().indexOf(_callbackKey)>0){WeixinApi[_callbackKey]=new Function();}
callbacks.dataLoaded=function(newData){WeixinApi[_callbackKey](newData);handler(newData,argv);};if(!(argv&&argv.shareTo=='favorite'&&callbacks.favorite===false)){callbacks.ready&&callbacks.ready(argv);}}else{if(!(argv&&argv.shareTo=='favorite'&&callbacks.favorite===false)){callbacks.ready&&callbacks.ready(argv);}
handler(data,argv);}});};WeixinApi.shareToTimeline=function(data,callbacks){_share({menu:'menu:share:timeline',action:'shareTimeline'},{"appid":data.appId?data.appId:'',"img_url":data.imgUrl,"link":data.link,"desc":data.title,"title":data.desc,"img_width":"640","img_height":"640"},callbacks);};WeixinApi.shareToFriend=function(data,callbacks){_share({menu:'menu:share:appmessage',action:'sendAppMessage'},{"appid":data.appId?data.appId:'',"img_url":data.imgUrl,"link":data.link,"desc":data.desc,"title":data.title,"img_width":"640","img_height":"640"},callbacks);};WeixinApi.shareToWeibo=function(data,callbacks){_share({menu:'menu:share:weibo',action:'shareWeibo'},{"content":data.desc,"url":data.link},callbacks);};WeixinApi.ready=function(readyCallback){if(readyCallback&&typeof readyCallback=='function'){var Api=this;var wxReadyFunc=function(){readyCallback(Api);};if(typeof window.WeixinJSBridge=="undefined"){if(document.addEventListener){document.addEventListener('WeixinJSBridgeReady',wxReadyFunc,false);}else if(document.attachEvent){document.attachEvent('WeixinJSBridgeReady',wxReadyFunc);document.attachEvent('onWeixinJSBridgeReady',wxReadyFunc);}}else{wxReadyFunc();}}};WeixinApi.enableDebugMode=function(callback){window.onerror=function(errorMessage,scriptURI,lineNumber,columnNumber){if(typeof callback==='function'){callback({message:errorMessage,script:scriptURI,line:lineNumber,column:columnNumber});}else{var msgs=[];msgs.push("额，代码有错。。。");msgs.push("\n错误信息：",errorMessage);msgs.push("\n出错文件：",scriptURI);msgs.push("\n出错位置：",lineNumber+'行，'+columnNumber+'列');alert(msgs.join(''));}}};})(window);WeixinApi.enableDebugMode();WeixinApi.ready(function(Api){var wxData={"appId":"","imgUrl":"http://print.nkumstc.cn/image/shareicon.jpg","link":"http://print.nkumstc.cn","desc":"云印南开——更方便的校园打印，南开等你!","title":"云印南开"};var wxCallbacks={favorite:false,ready:function(){},cancel:function(resp){},fail:function(resp){},confirm:function(resp){},all:function(resp,shareTo){}};Api.shareToFriend(wxData,wxCallbacks);Api.shareToTimeline(wxData,wxCallbacks);Api.shareToWeibo(wxData,wxCallbacks);});