var Manage = {};

Manage.backgroundColor = function(colorName){
    $('body').addClass(colorName);
};

/* iCheck START */

Manage.checkUI = function(){
    $('input[type="checkbox"], input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        radioClass: 'iradio_flat-blue'
    });
};

Manage.setRadioChecked = function(name, val) {
    $('input[type=radio][name="'+ name +'"][value="'+ val +'"]').iCheck('check');
};

/* iCheck END */

/* login START */

Manage.login = function(){
	$('#loginForm').keyup(function(e){
		if (e.keyCode == 13) {
			$('#btnLogin').click();
		}
	});
    $('#btnLogin').click(function(){
        Gentwolf.Loading.show();
        Gentwolf.Http.post(SITEURL +'passport/login', $('#loginForm').serialize(), function(rs){
            if (rs.code != 0) {
                Gentwolf.Loading.hide();           
                Gentwolf.Dialog.showAlert(rs.message);
            } else {
                window.setTimeout(function(){
                    Gentwolf.Loading.hide();
                    window.location.href = SITEURL +'default';
                }, 500);
            }
        }, function(){
            Gentwolf.Loading.hide();
            Gentwolf.Dialog.showAlert('登录失败，用户名或密码错误！');
        });
    });
};

/* login END */

/* panel START */

Manage.panel = function(){
    $('#rightNav').delegate('a', 'click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    navHolder = $('.mainLeft');
    navHolder.delegate('.itemTitle', 'click', function(e){
        e.preventDefault();

        var cur = $(this),
            p = cur.parent().find('.subNav');
        if (p.is(':hidden')) {
            p.show(400);
            cur.addClass('itemTitleSelected');
        } else {
            p.hide(400);
            window.setTimeout(function(){
                cur.removeClass('itemTitleSelected');
            }, 400);
        }       
    }).delegate('.subNavItem', 'click', function(){
        navHolder.find('.subNavItem').removeClass('subNavItemselected');
        $(this).addClass('subNavItemselected');

        var cur = $(this),
            target = cur.attr('data-target');
        if (0 == $('#'+ target).length) {
            //添加panel
            addPanel(target, cur);
        } else {
            //切换tab
            $('#tab'+ target).find('a').click();
        }
    });

    function addPanel(target, cur){
        var src = SITEURL + cur.attr('data-href');
        var panel = '<div role="tabpanel" class="tab-pane fade" id="'+ target +'">'+
            '<iframe class="iframe" src="'+ src +'" />'+
            '</div>';
        $('.tab-content').append(panel);
        changeIframeHeight();

        //添加tab
        var tab = $('<li role="presentation" id="tab'+ target +'">'+
            '<a href="#'+ target +'" role="tab">'+ cur.text() +'</a>'+
            '<span class="tabClose glyphicon glyphicon-remove"></span>'+
            '</li>');
        $('#rightNav').append(tab);
        tab.find('a').click();

        tab.find('.tabClose').click(function(){
            tab.remove();
            $('#'+ target).remove();

            var rightNavItem = $('#rightNav li'),
                len = rightNavItem.length;
            $(rightNavItem[len - 1]).find('a').click();
        });
    }

    document.body.onresize = function(){
        changeIframeHeight();
    };

    function changeIframeHeight() {
        var topHeight = $('.topNav').height()
            tabHeight = $('.rightNav').height(),
            iframeHeight = window.innerHeight - topHeight - tabHeight - 20;
        $('iframe').css({
            height: iframeHeight +'px'
        });
    }
};

/* panel END */

/* category START */

Manage.Category = {
    callback: null,
    id: 0,
    init: function(){
        $('#btnSave').click(function(){
			var bl = Manage.ValidForm.getStatus();
			if (!bl) return false;

            Gentwolf.Loading.show();

            Gentwolf.Http.post(SITEURL +'category/update&id='+ Manage.Category.id, $('form').serialize(), function(data){
                Gentwolf.Loading.hide();
                if (Manage.Category.callback) {
                    var param = {
                        id: data.message,
                        name: $('#name').val()
                    };
                    Manage.Category.callback(param);
                }

                Gentwolf.Loading.hide();
                Manage.Category.hide();
            }, function(){
                Gentwolf.Loading.hide();
                Gentwolf.Dialog.showConfirm('网络错误，请与管理联系！');
            });
        });

        Manage.checkUI();
		Manage.ValidForm.init($('form'));
    },
    show: function(param, callback){
        $('form')[0].reset();

        Manage.Category.id = param.id;
        $('#pId').val(param.pId);
        Manage.Category.callback = callback;
        $('#pId').val(param.pId);

        $('#panelEdit').modal('show').on('shown.bs.modal', function(){
            //$('#name').focus();
        });
    },
    edit: function(param, callback) {
        Manage.Category.id = param.id;
        Manage.Category.callback = callback;

        Gentwolf.Loading.show();
        Gentwolf.Http.get(SITEURL +'category/info', {id: param.id}, function(data){
            $('#pId').val(data.pId);
            $('#name').val(data.name);
            $('#keywords').val(data.keywords);
            $('#description').val(data.description);
            $('#showOrder').val(data.showOrder);

            Manage.setRadioChecked('data[is_nav]', data.isNav);
            Manage.setRadioChecked('data[is_show]', data.isShow);

            Gentwolf.Loading.hide();

            $('#panelEdit').modal('show').on('shown.bs.modal', function(){
                $('#name').focus();
            });
        }, function(){
            Gentwolf.Loading.hide();
            Gentwolf.Dialog.showAlert('网络错误，请与管理联系！');
        });
    },
    hide: function(){
        $('#panelEdit').modal('hide');
    },
    del: function(id, callback){
        Gentwolf.Loading.show();

        Gentwolf.Http.post(SITEURL +'category/del', {id: id}, function(data){
            Gentwolf.Loading.hide();

            callback(data);
        }, function(){
            Gentwolf.Loading.hide();
            Gentwolf.Dialog.showAlert('网络错误，请与管理联系！');
        });
    },
    editOrder: function(param) {
        Gentwolf.Http.post(SITEURL +'category/editOrder', param, function(data){
            if (data.code != 0) {
                Gentwolf.Dialog.showAlert(data.message);
            }
        }, function(){
            Gentwolf.Dialog.showAlert('网络错误，请与管理联系！');
        });
    }
};

/* category END */

/* table START */

Manage.Table = {
    list: function(){
        $('.confirm').click(function(e){
            e.preventDefault();

            var cur = $(this);
            window.top.Gentwolf.Dialog.showConfirm(cur.attr('data-msg'), function(){
               if ('1' == cur.attr('data-ajax')) {
                        Gentwolf.Http.get(cur.attr('href'), null, function(rs){
                        if (rs.code != 0) {
                            window.top.Gentwolf.Dialog.showConfirm(rs.msg)
                        } else {
                            window.top.window.location.reload();
                        }
                    }, function(){
                        window.top.Gentwolf.Dialog.showAlert('网络错误，请与管理员联系！');
                    });
                } else {
                    window.location.href = cur.attr('href');
                }
            });
        });
    },

    edit: function(data){
        if (data) Manage.InputFill.init(data);

        //处理富文本编辑器
        $('.editor').each(function(){
            var cur = $(this);
            CKEDITOR.replace(cur.attr('id'), {
                height: cur.attr('data-height')
            });
        });

        $('#btnBack').click(function(){
            window.history.go(-1);
        });

        $('#btnSave').click(function(){
            var f = $('form');
			var bl = Manage.ValidForm.getStatus(f);
			if (!bl) return false;

            Gentwolf.Http.post(f.attr('action'), f.serialize(), function(rs){
                if (rs.status != 0) {
                    window.top.Gentwolf.Dialog.showAlert(rs.result);
                } else {
                    var url = f.attr('data-index'),
                        timer = window.setTimeout(function(){
                            window.top.Gentwolf.Dialog.holder.find('.msgBtnOK').click();
                        }, 3000);
                    window.top.Gentwolf.Dialog.showAlert('保存成功', function(){
                        window.clearTimeout(timer);
                        window.location.href = url;
                    });

                }
            }, function(){
                window.top.Gentwolf.Dialog.showAlert('网络错误，请与管理员联系！');
            });
        });

        Manage.checkUI();
		Manage.ValidForm.init();
    }
};

/* table END */

Manage.InputFill = {
    init: function(data) {
        for (var item in data) {
            var objs = document.getElementsByClassName('input-'+ item);
            if (objs.length > 0) {
                switch (objs[0].type) {
                    case 'text':
                    case 'number':
                    case 'textarea':
                        this.fillText(objs[0], data[item]);
                        break;
                    case 'select':
                    case 'select-one':
                        this.fillSelect(objs[0], data[item]);
                        break;
                    case 'radio':
                        this.fillRadio(objs, data[item]);
                        break;
                    case 'checkbox':
                        this.fillCheckbox(objs, data[item]);
                        break;
                }
            }
        }
    },    
    fillText: function(target, value) {
        window.setTimeout(function(){
            target.value = value;
        }, 1);
    },
    fillSelect: function(target, value) {
        var len = target.options.length;
        if (len > 1) {
            for (var i = 0; i < len; i++) {
                if (value == target.options[i].value) {
                    target.options[i].selected = true;
                    break;
                }
            }
        }
    },
    fillRadio: function(targets, value) {
        for (var i = 0, len = targets.length; i < len; i++) {
            if (value == targets[i].value) {
                targets[i].checked = true;
                break;
            }
        }
    },
    fillCheckbox: function(targets, value) {
        if ('string' == typeof value) value = value.split(',');
        for (var i = 0, len = targets.length; i < len; i++) {
            if ($.inArray(targets[i].value, value) > -1) {
                targets[i].checked = true;
            }
        }
    }
};

Manage.ValidForm = {
	targetForm: null,

	init: function(targetForm){
		if (!targetForm) {
			this.targetForm = $('form');
		} else {
			this.targetForm = targetForm;
		}

		var self = this,
			lastName = '';
		this.targetForm.find('input,select,textarea').each(function(i, src){
			if (src.name && src.name != lastName) {
				lastName = src.name;

				switch (src.type) {
					case 'text':
					case 'number':
					case 'date':
					case 'select':
					case 'select-one':
					case 'textarea':
						if ($(src).attr('class').indexOf('editor') > -1) {
							self.checkEditorFocusBlur(src);
						}
						self.checkFocusBlur(src);
						break;
				}
			}
		});

		this.targetForm.find('.checkboxHolder').each(function(){
			var holder = $(this);
			holder.find('label,ins').click(function(){
				self.checkbox(holder);
			});
		});

		/*this.targetForm.find('.radioHolder').each(function(){
			self.checkbox($(this));
		});*/
	},

	triggerValid: function(){		
		this.targetForm.find('input,select,textarea').blur();

		var self = this;
		this.targetForm.find('.checkboxHolder').each(function(){
			self.checkbox($(this));
		});
	},

	getStatus: function(){
		this.triggerValid();

		return 0 == this.targetForm.find('.has-error').length;
	},

	checkFocusBlur: function(src){
		var self = this,
			cur = $(src);

		cur.focus(function(){
			self.showInfo(cur);
		}).blur(function(){
			var bl = false,
				val = $.trim(cur.val()),
				min = parseInt(cur.attr('min')), 
				max = parseInt(cur.attr('max'));
			switch (cur.attr('method')) {
				case 'string':
					bl = self.validString(val, min, max);
					break;
				case 'integer':
					bl = self.validInteger(val, min, max);
					break;
			}

			if (bl) {
				self.showSuccess(cur);
			} else {
				self.showError(cur);
			}
			return bl;
		});
	},

	checkEditorFocusBlur: function(src){
		var self = this,
			cur = $(src),
			name = cur.attr('id');

		CKEDITOR.instances[name].on('instanceReady', function(){
			this.document.on('focus', function(){
				self.showInfo(cur);
			});
			this.document.on('blur', function(){
				CKEDITOR.instances[name].updateElement();

				var val = $.trim(cur.val()),
					min = parseInt(cur.attr('min')), 
					max = parseInt(cur.attr('max'));

				var bl = self.validString(val, min, max);
				if (bl) {
					self.showSuccess(cur);
				} else {
					self.showError(cur);
				}
				return bl;
			});
		});
	},

	checkbox: function(src) {
		var len = src.find('input[type="checkbox"]:checked').length;
		var bl = this.validInteger(len, parseInt(src.attr('min')), parseInt(src.attr('max')));
		if (!bl) {
			this.showError(src);
		} else {
			this.showSuccess(src);
		}
	},

	showError: function(src) {        
		src.parent().parent().removeClass('has-success').addClass('has-error has-feedback')
			.find('.glyphicon')
			.removeClass('glyphicon-ok')
			.addClass('glyphicon-remove');
	},
	showSuccess: function(src) {
		src.parent().parent().removeClass('has-error').addClass('has-success has-feedback')
			.find('.glyphicon')
			.removeClass('glyphicon-remove')
			.addClass('glyphicon-ok');
	},
	showInfo: function(src) {
		src.parent().parent().removeClass('has-error')
			.removeClass('has-success')
			.removeClass('has-feedback')
			.find('.glyphicon')
			.removeClass('glyphicon-remove')
			.removeClass('glyphicon-ok');
	},
	
	validString: function(str, minLength, maxLength) {
		var len = str ? str.length : 0;
		return minLength <= len && len <= maxLength;
	},

	validInteger: function(val, minValue, maxValue) {
        var bl = /^\d+$/.test(val);
        if (!bl) return false;

        val = parseInt(val, 10);
		return minValue <= val && val <= maxValue;
	}
};
