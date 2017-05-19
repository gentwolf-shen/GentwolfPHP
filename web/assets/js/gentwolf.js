var ValidForm = {
    targetForm: null,

    init: function(targetForm){
        if (!targetForm) {
            this.targetForm = $('form');
        } else {
            this.targetForm = targetForm;
        }

        var self = this;
        this.targetForm.find('input,textarea').each(function(i, src){
            var cur = $(src);
            if (cur.attr('name') && cur.attr('method')) {
                switch (src.type) {
                    case 'text':
                    case 'password':
                    case 'number':
                    case 'textarea':
                        self.checkFocusBlur(cur);
                        break;
                }
            }
        });
    },

    triggerValid: function(){
        //this.hideAllError();

        this.targetForm.find('input,textarea').blur();
    },

    getStatus: function(){
        this.triggerValid();

        return 0 == this.targetForm.find('.has-error').length;
    },

    checkFocusBlur: function(cur){
        var self = this;

        cur.focus(function(){
            if (!cur.is(':visible')) return false;

            self.showInfo(cur);
        }).blur(function(){
            if (!cur.is(':visible')) return false;

            var bl = false,
                val = $.trim(cur.val()),
                min = parseInt(cur.attr('min')),
                max = parseInt(cur.attr('max'));
            switch (cur.attr('method')) {
				case 'loginName':
					bl = self.validLoginName(val, min, max);
					break;
                case 'string':
                    bl = self.validString(val, min, max);
                    break;
                case 'integer':
                    val = val ? parseInt(val, 10) : 0;
                    bl = self.validInteger(val, min, max);
                    break;
                case 'compare':
                    bl = self.validCompare(val, cur);
                    break;
                case 'email':
                    bl = self.validEmail(val);
                    break;
                case 'number':
                    bl = self.validNumber(val, min, max);
                    break;
                case 'mobile':
                    bl = self.validMobile(val);
                    break;
            }

            if (bl) {
                var methodAjax = cur.attr('method-ajax');
                if (methodAjax) {
                    $.getJSON(methodAjax, {value: val}, function(data){
                        if ('0' != data.status) {
                            self.showError(cur, data.result);
                        } else {
                            self.showSuccess(cur);
                        }
                    });
                } else {
                    self.showSuccess(cur);
                }
            } else {
                self.showError(cur);
            }
            return bl;
        });
    },


    showError: function(src, msg) {
        var p = src.parent();
        p.removeClass('has-success').addClass('has-error has-feedback')
            .find('.glyphicon')
            .removeClass('glyphicon-ok')
            .addClass('glyphicon-remove');

        if (msg) {
            p.attr('data-msg', msg);
        } else {
            p.removeAttr('data-msg');
        }
        p.popover({
            trigger: 'manual',
            content: function(){
                var msg = p.attr('data-msg');
                if (!msg) msg = src.attr('msg-error');
                return msg;
            }
        }).popover('show');
    },
    showSuccess: function(src) {
        var p = src.parent();
        p.removeClass('has-error').addClass('has-success has-feedback')
            .find('.glyphicon')
            .removeClass('glyphicon-remove')
            .addClass('glyphicon-ok');
        if (src.attr('class').indexOf('checkboxHolder') == -1) {
            p.popover('hide');
        }
    },
    showInfo: function(src) {
        var p = src.parent();
        p.removeClass('has-error')
            .removeClass('has-success')
            .removeClass('has-feedback')
            .find('.glyphicon')
            .removeClass('glyphicon-remove')
            .removeClass('glyphicon-ok');
        p.popover('hide');
    },

    hideAllError: function(){
        $('.popover').remove();
        $('.has-feedback').removeClass('.has-error');
        $('.form-control-feedback').removeClass('glyphicon-remove');
    },

	validLoginName: function(str, minLength, maxLength) {
         var reg = new RegExp('^[0-9a-z\-]{'+ minLength +','+ maxLength +'}$', 'i');
         return reg.test(str);
    },

    validString: function(str, minLength, maxLength) {
        var len = str ? str.length : 0;
        return minLength <= len && len <= maxLength;
    },

    validInteger: function(val, minValue, maxValue) {
        return minValue <= val && val <= maxValue;
    },

    validEmail: function(val) {
        var pattern = /^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/;
        return pattern.test(val);
    },

    validMobile: function(val) {
        var ptns = ['^1[3|5|7|8][0-9]{9}$'],
            bl = false;
        for (var i = 0, len = ptns.length; i < len; i++) {
            var reg = new RegExp(ptns[i]);
            bl = reg.test(val);
            if (bl) break;
        }
        return bl;
    },

    validCompare: function(val, src) {
        var bl = false,
            target = $('#'+ src.attr('compare-target'));
        target.blur();
        if (target.parent().attr('class').indexOf('has-success') > -1) {
            switch (src.attr('compare-type')) {
                case '=':
                case '==':
                    bl = (val == target.val());
                    break;
            }
        }

        return bl;
    },

    validNumber: function(val, minLength, maxLength) {
        var reg = new RegExp('^[0-9]{'+ minLength +','+ maxLength +'}$');
        return reg.test(val);
    }
};

var Dialog = {
    holder: null,
    init: function (title) {
        var self = this;
        if (null == self.holder) {
            var str = '<div id="dialog">'+   
					  '	<span class="msgBtnClose">×</span>'+
					  ' <div class="msgTitle">'+ title +'</div>'+
					  '	<div class="btns">'+
					  '		<input type="button" class="btn btn-default btn-warning msgBtnOK" value="确定" />'+
					  '		<input type="button" class="btn btn-default btn-primary msgBtnCancel" value="取消" />'+
					  '	</div>'+
					  '</div>';
            self.holder = $(str);
            $('body').append(self.holder);
            self.holder.find('.msgBtnCancel,.msgBtnClose').click(function () {
                self.close()
            });
        }
    },

    show: function (opts) {
		this.init(opts.title);
		if (opts.buttons) {
			this.holder.find('.btn').hide();
			if (opts.buttons.ok) {
				this.holder.find('.msgBtnOK').show();
			}
			if (opts.buttons.cancel) {
				this.holder.find('.msgBtnCancel').show();
			}
		}

		if (opts.confirm) {
			var self = this;
			this.holder.find('.msgBtnOK').unbind('click').click(function () {
				opts.confirm();
				self.close();
			});
		}
		Mask.show(this.holder);
    },
    showConfirm: function (title, callback) {
        this.show({
            title: title,
            btns: [
                'OK'
            ],
            confirm: function () {
                if (callback) callback();
            }
        })
    },
    close: function () {
        Mask.hide();
		this.holder.remove();
		this.holder = null;
    }
};

var Mask = {
    holder: null,
    show: function(target){
        if (null == this.holder) {
            this.holder = $('<div id="mask"></div>');
            this.holder.css({
                position: 'absolute',
                zIndex: 5000,
                left: 0,
                top: 0,
                width: '100%',
                height: $(document).height() +'px',
                backgroundColor: '#000000',
                opacity: 0.5
            });
            $('body').append(this.holder);
        }
        this.holder.show();
        if (target) {
            target.show().css({
                position: 'fixed',
                zIndex: 5001,
                top: (window.innerHeight - parseInt(target.css('height'))) / 2 +'px',
                left: (window.innerWidth - parseInt(target.css('width'))) / 2 +'px'
            });
        }
    },
    hide: function(){
        this.holder.hide();
    }
};

var Loading = {
	holder: null,
	show: function(){
		if (!this.holder) {
			this.holder = $('<img id="loadingImage" src="/assets/images/loading.gif" />');
			$('body').append(this.holder);
			this.holder.css({
				top: '40%',
				left: '45%',
				position: 'fixed'
			});
		}
		this.holder.show();
	},
	hide: function(){
		this.holder.hide();
	}
};

var Http = {
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

var Gentwolf = {};

Gentwolf.initCaptcha = function() {
	$('#imageCaptcha').click(function(){
		var cur = $(this),
            url = cur.attr('src');
		if (url.indexOf('t=') >= 1) {
			url = url.split('t=')[0];
		} else {
			url += '?';
		}
        cur.attr('src', url +'t='+ (new Date()).getTime());
	});
};

Gentwolf.initTalksList = function(){
    $('.dropdown-menu > li').click(function(){
		var cur = $(this);
		cur.parent().attr('data-value', cur.attr('data-value'));

		Gentwolf.searchTalks();
	});

	$('#btnSearch').click(function(){
		Gentwolf.searchTalks();
	});

	$('.currentSearch').find('.btnRemoveFilter').click(function(){
		var cur = $(this),
			k = cur.attr('data-key');
		cur.parent().remove();
		$('#'+ k).val('');
		
		Gentwolf.searchTalks();
	});

	$('#title').keyup(function(e){
		if (13 == e.keyCode) {
			Gentwolf.searchTalks();
		}
	});

	Gentwolf.initAddCatalog();
};

Gentwolf.searchTalks = function(){
	var param = [];
	param.push('title='+ $.trim($('#title').val()));
	param.push('tag='+ $.trim($('#tag').val()));

	$('.dropdown-menu').each(function(){
		var cur = $(this);
		param.push(cur.attr('data-key') +'='+ cur.attr('data-value'));
	});

	window.location.href = '?'+ param.join('&');
};

Gentwolf.initRegister = function(loginUrl){
	$('#username').focus();
	ValidForm.init($('#register'));
	Gentwolf.initCaptcha();

	$('#btnRegister').click(function(){
		var bl = ValidForm.getStatus();
		if (!bl) return false;

		Loading.show();
		$.post(document.location.href, $('#register').serialize(), function(data){
			Loading.hide();
			if ('0' == data.status) {
				window.location.href = loginUrl;
			} else {
				//console.info(data.result);
			}
		}, 'JSON');
	});
};

Gentwolf.initLogin = function(userCenterUrl){
	$('#username').focus();
	ValidForm.init($('#login'));

	$('#btnLogin').click(function(){
		var bl = ValidForm.getStatus();
		if (!bl) return false;

		Loading.show();
		$.post(document.location.href, $('#login').serialize(), function(data){			
			if ('0' == data.status) {
				var ref = document.referrer;
				if (!ref) ref = userCenterUrl;
				window.location.href = ref;
			} else {
				window.setTimeout(function(){
					Loading.hide();
					ValidForm.showError($('#username'), data.result);
				}, 200);
			}
		}, 'JSON');
	});

	$('#username, #password').keyup(function(e){
		if (13 == e.keyCode) {
			$('#btnLogin').click();
		}
	});
};

Gentwolf.initAddCatalog = function(){
	$('.btnAddCatalog').click(function(){
		Gentwolf.addToCart($(this));
	});
};

Gentwolf.addToCart = function(target) {
	if (!$('#loginStatus1').is(':visible')) {
		window.location.href = $('#loginStatus0').find('a').eq(0).attr('href');
		return;
	}
	
	var tedId = target.attr('data-id');
	Loading.show();
	Api.get('cart/add', {tedId: tedId}, function(data){
		if (0 == data.status) {
            Gentwolf.showMoveToCart(tedId);
        }
        Loading.hide();
	});
};

Gentwolf.showMoveToCart = function(tedId) {
    var talkHolder = $('#img-'+ tedId),
        talkOffset = talkHolder.offset(),
        tmp = talkHolder.clone();
    tmp.css({
        position: 'absolute',
        zIndex: 100,
        top: talkOffset.top,
        left: talkOffset.left,
        width: talkHolder.width(),
        height: talkHolder.height()
    });
    $('body').append(tmp);
    var cart = $('#dvdCatalog'),
        cartOffset = cart.offset();
    tmp.animate({
        top: cartOffset.top + 10,
        left: cartOffset.left + 10,
        width: 0,
        height: 0
    }, function(){
        var badge = $('#reminderBadge');
        badge.text(parseInt(badge.text()) + 1);
        tmp.remove();
    });
};

Gentwolf.initUser = function(segment) {
	switch (segment) {
		case 'catalog':
			Gentwolf.initCatalog();
			break;
	}
};

Gentwolf.initCatalog = function() {
    $('.catalogDelete').click(function() {
		var tedId = $(this).attr('data-id');
		Dialog.showConfirm('确定要删除？', function(){
			Loading.show();
			Api.get('cart/del', {tedId: tedId}, function(data){
				if (0 == data.status) {
					$('#list-'+ tedId).remove();
				} else {
					Dialog.showConfirm(data.result);
				}
				Loading.hide();
			});
		});
    });
};