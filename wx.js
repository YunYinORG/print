(function(window) {
    "use strict";
    /**
     * 定义WeixinApi
     */
    var WeixinApi = {
        version: 2.9
    };
    // 将WeixinApi暴露到window下：全局可使用，对旧版本向下兼容
    window.WeixinApi = WeixinApi;
    /////////////////////////// CommonJS /////////////////////////////////
    if (typeof define === 'function' && (define.amd || define.cmd)) {
        if (define.amd) {
            // AMD 规范，for：requirejs
            define(function() {
                return WeixinApi;
            });
        } else if (define.cmd) {
            // CMD 规范，for：seajs
            define(function(require, exports, module) {
                module.exports = WeixinApi;
            });
        }
    }
    /**
     * 内部私有方法，分享用
     * @private
     */
    var _share = function(cmd, data, callbacks) {
        callbacks = callbacks || {};
        // 分享过程中的一些回调
        var progress = function(resp) {
            switch (true) {
                // 用户取消
                case /\:cancel$/i.test(resp.err_msg):
                    callbacks.cancel && callbacks.cancel(resp);
                    break;
                    // 发送成功
                case /\:(confirm|ok)$/i.test(resp.err_msg):
                    callbacks.confirm && callbacks.confirm(resp);
                    break;
                    // fail　发送失败
                case /\:fail$/i.test(resp.err_msg):
                default:
                    callbacks.fail && callbacks.fail(resp);
                    break;
            }
            // 无论成功失败都会执行的回调
            callbacks.all && callbacks.all(resp);
        };
        // 执行分享，并处理结果
        var handler = function(theData, argv) {
            // 新的分享接口，单独处理
            if (cmd.menu === 'menu:general:share') {
                // 如果是收藏操作，并且在wxCallbacks中配置了favorite为false，则不执行回调
                if (argv.shareTo == 'favorite') {
                    if (callbacks.favorite === false) {
                        return argv.generalShare(theData, function() {});
                    }
                }
                argv.generalShare(theData, progress);
            } else {
                WeixinJSBridge.invoke(cmd.action, theData, progress);
            }
        };
        // 监听分享操作
        WeixinJSBridge.on(cmd.menu, function(argv) {
            if (callbacks.async && callbacks.ready) {
                var _callbackKey = "_wx_loadedCb_";
                WeixinApi[_callbackKey] = callbacks.dataLoaded || new Function();
                if (WeixinApi[_callbackKey].toString().indexOf(_callbackKey) > 0) {
                    WeixinApi[_callbackKey] = new Function();
                }
                callbacks.dataLoaded = function(newData) {
                    WeixinApi[_callbackKey](newData);
                    handler(newData, argv);
                };
                // 然后就绪
                if (!(argv && argv.shareTo == 'favorite' && callbacks.favorite === false)) {
                    callbacks.ready && callbacks.ready(argv);
                }
            } else {
                // 就绪状态
                if (!(argv && argv.shareTo == 'favorite' && callbacks.favorite === false)) {
                    callbacks.ready && callbacks.ready(argv);
                }
                handler(data, argv);
            }
        });
    };
    /**
     * 分享到微信朋友圈
     * @param       {Object}    data       待分享的信息
     * @p-config    {String}    appId      公众平台的appId（服务号可用）
     * @p-config    {String}    imgUrl     图片地址
     * @p-config    {String}    link       链接地址
     * @p-config    {String}    desc       描述
     * @p-config    {String}    title      分享的标题
     *
     * @param       {Object}    callbacks  相关回调方法
     * @p-config    {Boolean}   async                   ready方法是否需要异步执行，默认false
     * @p-config    {Function}  ready(argv)             就绪状态
     * @p-config    {Function}  dataLoaded(data)        数据加载完成后调用，async为true时有用，也可以为空
     * @p-config    {Function}  cancel(resp)    取消
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  confirm(resp)   成功
     * @p-config    {Function}  all(resp)       无论成功失败都会执行的回调
     */
    WeixinApi.shareToTimeline = function(data, callbacks) {
        _share({
            menu: 'menu:share:timeline',
            action: 'shareTimeline'
        }, {
            "appid": data.appId ? data.appId : '',
            "img_url": data.imgUrl,
            "link": data.link,
            "desc": data.title,
            "title": data.desc,
            "img_width": "640",
            "img_height": "640"
        }, callbacks);
    };
    /**
     * 发送给微信上的好友
     * @param       {Object}    data       待分享的信息
     * @p-config    {String}    appId      公众平台的appId（服务号可用）
     * @p-config    {String}    imgUrl     图片地址
     * @p-config    {String}    link       链接地址
     * @p-config    {String}    desc       描述
     * @p-config    {String}    title      分享的标题
     *
     * @param       {Object}    callbacks  相关回调方法
     * @p-config    {Boolean}   async                   ready方法是否需要异步执行，默认false
     * @p-config    {Function}  ready(argv)             就绪状态
     * @p-config    {Function}  dataLoaded(data)        数据加载完成后调用，async为true时有用，也可以为空
     * @p-config    {Function}  cancel(resp)    取消
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  confirm(resp)   成功
     * @p-config    {Function}  all(resp)       无论成功失败都会执行的回调
     */
    WeixinApi.shareToFriend = function(data, callbacks) {
        _share({
            menu: 'menu:share:appmessage',
            action: 'sendAppMessage'
        }, {
            "appid": data.appId ? data.appId : '',
            "img_url": data.imgUrl,
            "link": data.link,
            "desc": data.desc,
            "title": data.title,
            "img_width": "640",
            "img_height": "640"
        }, callbacks);
    };
    /**
     * 分享到腾讯微博
     * @param       {Object}    data       待分享的信息
     * @p-config    {String}    link       链接地址
     * @p-config    {String}    desc       描述
     *
     * @param       {Object}    callbacks  相关回调方法
     * @p-config    {Boolean}   async                   ready方法是否需要异步执行，默认false
     * @p-config    {Function}  ready(argv)             就绪状态
     * @p-config    {Function}  dataLoaded(data)        数据加载完成后调用，async为true时有用，也可以为空
     * @p-config    {Function}  cancel(resp)    取消
     * @p-config    {Function}  fail(resp)      失败
     * @p-config    {Function}  confirm(resp)   成功
     * @p-config    {Function}  all(resp)       无论成功失败都会执行的回调
     */
    WeixinApi.shareToWeibo = function(data, callbacks) {
        _share({
            menu: 'menu:share:weibo',
            action: 'shareWeibo'
        }, {
            "content": data.desc,
            "url": data.link
        }, callbacks);
    };
    /**
     * 当页面加载完毕后执行，使用方法：
     * WeixinApi.ready(function(Api){
     *     // 从这里只用Api即是WeixinApi
     * });
     * @param readyCallback
     */
    WeixinApi.ready = function(readyCallback) {
        if (readyCallback && typeof readyCallback == 'function') {
            var Api = this;
            var wxReadyFunc = function() {
                readyCallback(Api);
            };
            if (typeof window.WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', wxReadyFunc, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', wxReadyFunc);
                    document.attachEvent('onWeixinJSBridgeReady', wxReadyFunc);
                }
            } else {
                wxReadyFunc();
            }
        }
    };
    /**
     * 开启Api的debug模式，比如出了个什么错误，能alert告诉你，而不是一直很苦逼的在想哪儿出问题了
     * @param    {Function}  callback(error) 出错后的回调，默认是alert
     */
    WeixinApi.enableDebugMode = function(callback) {
        /**
         * @param {String}  errorMessage   错误信息
         * @param {String}  scriptURI      出错的文件
         * @param {Long}    lineNumber     出错代码的行号
         * @param {Long}    columnNumber   出错代码的列号
         */
        window.onerror = function(errorMessage, scriptURI, lineNumber, columnNumber) {
            // 有callback的情况下，将错误信息传递到options.callback中
            if (typeof callback === 'function') {
                callback({
                    message: errorMessage,
                    script: scriptURI,
                    line: lineNumber,
                    column: columnNumber
                });
            } else {
                // 其他情况，都以alert方式直接提示错误信息
                var msgs = [];
                msgs.push("额，代码有错。。。");
                msgs.push("\n错误信息：", errorMessage);
                msgs.push("\n出错文件：", scriptURI);
                msgs.push("\n出错位置：", lineNumber + '行，' + columnNumber + '列');
                alert(msgs.join(''));
            }
        }
    };
})(window);
// WeixinApi.enableDebugMode();
WeixinApi.ready(function(Api) {
    var wxData = {
        "appId": "",
        "imgUrl": "http://print.nkumstc.cn/image/shareicon.jpg",
        "link": "http://print.nkumstc.cn",
        "desc": "更方便的校园打印，南开等你!",
        "title": "云印南开"
    };

     // 分享的回调
                var wxCallbacks = {
                    // 收藏操作是否触发回调，默认是开启的
                    favorite : false,

                    // 分享操作开始之前
                    ready : function() {
                        // 你可以在这里对分享的数据进行重组
                       // alert("准备分享");
                    },
                    // 分享被用户自动取消
                    cancel : function(resp) {
                        // 你可以在你的页面上给用户一个小Tip，为什么要取消呢？
                       // alert("分享被取消，msg=" + resp.err_msg);
                    },
                    // 分享失败了
                    fail : function(resp) {
                        // 分享失败了，是不是可以告诉用户：不要紧，可能是网络问题，一会儿再试试？
                      //  alert("分享失败，msg=" + resp.err_msg);
                    },
                    // 分享成功
                    confirm : function(resp) {
                        // 分享成功了，我们是不是可以做一些分享统计呢？
                       // alert("分享成功，msg=" + resp.err_msg);
                    },
                    // 整个分享过程结束
                    all : function(resp,shareTo) {
                        // 如果你做的是一个鼓励用户进行分享的产品，在这里是不是可以给用户一些反馈了？
                        //alert("分享" + (shareTo ? "到" + shareTo : "") + "结束，msg=" + resp.err_msg);
                    }
                };
                // 用户点开右上角popup菜单后，点击分享给好友，会执行下面这个代码
                Api.shareToFriend(wxData, wxCallbacks);

                // 点击分享到朋友圈，会执行下面这个代码
            Api.shareToTimeline(wxData, wxCallbacks);

                // 点击分享到腾讯微博，会执行下面这个代码
                Api.shareToWeibo(wxData, wxCallbacks);
});