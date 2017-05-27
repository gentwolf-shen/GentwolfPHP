var Gentwolf = {};

Gentwolf.Http = {
    send: function(url, data, returnDataType, httpMethod, successCallback, errorCallback, isPostFile, postFileCallback){
        var contentType = null,
            processData = true;

        if ('POST' == httpMethod) {
            if (isPostFile) {
                contentType = false;
                processData = false;
            } else {
                contentType = 'application/x-www-form-urlencoded';
            }
        }

        $.ajax({
            async: true,
            data: data,
            dataType: returnDataType,
            url: url,
            type: httpMethod,
            contentType: contentType,
            processData: processData,
            error: function(e){
                if (errorCallback) {
                    errorCallback(e.responseText);
                } else {
                    console.info('网络错误: '+ url);
                }                
            },
            success: function(data){
                successCallback(data);
            },
            xhr: function(){
                var xhr = $.ajaxSettings.xhr();
                if (postFileCallback) {
                    xhr.upload.addEventListener('progress', function(e){                        
                        postFileCallback({
                            loaded: e.loaded,
                            total: e.total
                        });                   
                    }, false);
                }
                return xhr;
            }
        });
    },
    postFile: function(url, data, successCallback, errorCallback, progressCallback){
        this.send(url, data, 'JSON', 'POST', successCallback, errorCallback, true, progressCallback);
    },
    jsonp: function(url, data, successCallback, errorCallback){
        this.send(url, data, 'JSONP',  'GET', successCallback, errorCallback);
    },
    get: function(url, data, successCallback, errorCallback){
        this.send(url, data, 'JSON', 'GET', successCallback, errorCallback);
    },
    post: function(url, data, successCallback, errorCallback){
        this.send(url, data, 'JSON', 'POST', successCallback, errorCallback);
    }
};

Gentwolf.Loading = {
    holder: null,
    show: function(){
        if (null == this.holder) {
            this.holder = $('<img id="loadingImage" src="/assets/images/loading.gif" />');
            this.holder.css({
                position: 'absolute',
                zIndex: 99999,
                top: '40%',
                left: '40%'
            });
            top.$('body').append(this.holder);
            this.holder.css({
                top: (window.innerHeight - parseInt(this.holder.height())) / 2 +'px',
                left: (window.innerWidth - parseInt(this.holder.width())) / 2 +'px'
            });
        }
        this.holder.show();
    },
    hide: function(){
        if (this.holder) this.holder.hide();
    }
};

Gentwolf.Mask = {
    holder: null,
    show: function(target){
        if (null == this.holder) {
            this.holder = $('<div id="mask"></div>');
            $('body').append(this.holder);
        }
        this.holder.fadeIn();
        if (target) {
            var h = target.height();
            target.css({
                top: (window.innerHeight - h) / 2 - (h / 2) +'px',
                left: (window.innerWidth - target.width()) / 2 +'px'
            }).fadeIn();
        }
    },
    hide: function(target){
        this.holder.fadeOut();
        if (target) target.fadeOut();
    }
};

Gentwolf.Tool = {
    formatDate: function(d){
        var date = new Date(parseInt(d) * 1000);
        return [date.getFullYear(), date.getMonth() + 1, date.getDate()].join('-');
    },
    getNow: function(){
        return Math.floor((new Date()).getTime() / 1000);
    },
    toInt: function(str) {
        str = $.trim(str);
        return '' == str ? 0 : parseInt('0'+ str, 10);
    },
    getUrlParam: function(name) {
        var rs = window.location.hash.match(new RegExp(name +'=([^&]*)(&|$)'));
        return rs ? rs[1] : null;
    },
    isMobile: function(str) {
        return /^1[3-8][0-9]{9}$/.test(str);
    }
};

Gentwolf.Dialog = {
    holder: null,
    init: function(){
        if (null == this.holder) {
            var str = '<div id="messageBox">'+
                '     <span class="msgBtnClose">×</span>'+
                '     <div class="msgTitle"></div>'+
                '     <div class="btns text-center">'+
                '     <input type="button" role="button" class="btn btn-primary msgBtnOK" value="确认" />'+
                '     <input type="button" role="button" class="btn btn-warning msgBtnCancel" value="取消" />'+
                '     </div>'+
                '</div>';
            this.holder = $(str);
            $('body').append(this.holder);

            this.holder.find('.msgBtnCancel').click(function () {
                Gentwolf.Dialog.hide();
            });
            this.holder.find('.msgBtnClose').click(function () {
                if ('none' == Gentwolf.Dialog.holder.find('.msgBtnCancel').css('display')) {
                    Gentwolf.Dialog.holder.find('.msgBtnOK').click();
                } else {
                    Gentwolf.Dialog.hide();
                }
            });
        }
    },
    show: function(opts){
        this.init();

        if (!opts) opts = {};
        if (!opts.title) opts.title = ' ';
        if (!opts.confirmCallback) opts.confirmCallback = function () { };
        if (!opts.cancelCallback) opts.cancelCallback = function () { };
        if (!opts.btns) opts.btns = ['OK', 'Cancel'];

        if (1 == opts.btns.length) {
            this.holder.find('.msgBtnCancel').hide();
        } else {
            this.holder.find('.btn').show();
        }

        this.holder.find('.msgTitle').html(opts.title);
        this.holder.find('.msgBtnOK').unbind('click').click(function(){
            opts.confirmCallback();
            Gentwolf.Dialog.hide();
        });
        this.holder.find('.msgBtnCancel').unbind('click').click(function(){
            opts.cancelCallback();
            Gentwolf.Dialog.hide();
        });

        Gentwolf.Mask.show(this.holder);

        var self = this;
        window.setTimeout(function(){
            self.holder.find('.msgBtnOK').focus();
        }, 100);
    },
    showAlert: function(msg, okCallback) {
         this.show({
            title: msg,
            btns: ['OK'],
            confirmCallback: function(){
                if (okCallback) okCallback();
            }
        });
    },
    showConfirm: function(msg, okCallback, cancelCallback) {
        this.show({
            title: msg,
            //btns: ['OK', 'Cancel'],
            confirmCallback: function(){
                if (okCallback) okCallback();
            },
            cancelCallback: function(){
                if (cancelCallback) cancelCallback();
            }
        });
    },
    hide: function() {
        Gentwolf.Mask.hide(this.holder);
    }
};

Gentwolf.LocalCache = {
    isSupported: !!window.localStorage,
    setItem: function(key, value){
        if (this.isSupported) window.localStorage.setItem(key, value);
    },
    getItem: function(key){
        if (this.isSupported) return window.localStorage.getItem(key);
    },
    removeItem: function(key){
        if (this.isSupported) window.localStorage.removeItem(key);
    },
    removeAll: function(){
        if (this.isSupported) window.localStorage.clear();
    }
};

Gentwolf.Http = {
    request: function(method, data, type, successCallback) {
        $.ajax({
            async: true,
            data: data,
            dataType: 'JSON',
            url: '/'+ method,
            type: type,
            error: function(e){
                alert('网络错误，请稍后重试。');
                console.info(e);
            },
            success: function(data){
                successCallback(data);
            }
        });
    },

    get: function(method, data, successCallback){
        this.request(method, data, 'GET', successCallback);
    },

    post: function(method, data, successCallback){
        this.request(method, data, 'POST', successCallback);
    }
};

