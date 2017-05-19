var Category = {};

Category.settings = {
    edit: {
        enable: true,
        showRemoveBtn: false,
        showRenameBtn: false
    },
    data: {
        simpleData: {
            enable: true
        }
    },
    callback: {
        beforeDrop: function(treeId, srcNodes, targetNode, moveType){
            Category.beforeDrop(treeId, srcNodes, targetNode, moveType);
        },
        onRightClick: function(event, treeId, treeNode){
            Category.showContextMenu(event, treeId, treeNode);
        }
    }
};

Category.show = function(targetId, menuId, nodes){
    this.target = $('#'+ targetId);
    this.menu = $('#'+ menuId);
    $.fn.zTree.init(this.target, this.settings, nodes);
    this.zTree = $.fn.zTree.getZTreeObj(targetId);

    this.bindActionClick();
};

Category.showContextMenu = function(event, treeId, treeNode){
    if (!treeNode && event.target.tagName.toLowerCase() != 'button' && $(event.target).parents('a').length == 0) {
        this.zTree.cancelSelectedNode();
        this.showMenu('root', event.clientX, event.clientY);
    } else if (treeNode && !treeNode.noR) {
        this.zTree.selectNode(treeNode);
        this.showMenu('node', event.clientX, event.clientY);
    }
};

Category.showMenu = function(type, x, y) {
    this.menu.css({
        top: y +'px',
        left: x +'px'
    }).show();

    $('body').bind('click', this.bodyClick);
};

Category.hideMenu = function() {
    this.menu.hide();
    $('body').unbind('click', this.bodyClick);
};

Category.bodyClick = function(){
    Category.menu.hide();
};

Category.bindActionClick = function(){
    this.menu.delegate('a', 'click', function(){
        Category.actionHandle($(this).attr('data-item'));
    })
};

Category.actionHandle = function(type) {
    switch (type) {
        case 'addCat':
            this.addCategory();
            break;
        case 'delCat':
            this.delCategory();
            break;
        case 'editCat':
            this.editCategory();
            break;
    }
};

Category.addCategory = function(){
    this.menu.hide();

    var selectedNode = this.zTree.getSelectedNodes()[0];
    var param = {
        id: 0,
        pId: selectedNode ? selectedNode.id : 0
    };

    var self = this;
    Manage.Category.show(param, function(data){
        var newNode = { id: data.id, name: data.name, pId: param.pId},
            selectedNode = self.zTree.getSelectedNodes()[0];
        if (selectedNode) {
            self.zTree.addNodes(selectedNode, newNode);
        } else {
            self.zTree.addNodes(null, newNode);
        }
    });
};

Category.delCategory = function(){
    this.menu.hide();

    var nodes = this.zTree.getSelectedNodes();
    if (nodes && nodes.length>0) {
        if (nodes[0].children && nodes[0].children.length > 0) {            
            window.top.Gentwolf.Dialog.showAlert('该分类下有子分类，不允许删除！');
        } else {
            var self = this,
                node = nodes[0];
            window.top.Gentwolf.Dialog.showConfirm('确定要删除“'+ node.name +'”?', function(){
                Manage.Category.del(node.id, function(data){
                    if (data.code != 0) {
                        Gentwolf.Dialog.showConfirm(data.message);
                    } else {
                        self.zTree.removeNode(node);
                    }
                });
            });
        }
    }
};

Category.editCategory = function(){
    this.menu.hide();

    var node = this.zTree.getSelectedNodes()[0];
    if (!node) return;

    var param = {
        id: node.id
    };

    Manage.Category.edit(param, function(data){
        $('#'+ node.tId +'_span').text(data.name);
    });
};

Category.beforeDrop = function(treeId, srcNodes, targetNode, moveType) {
    var param = {
        id: srcNodes[0].id,
        pId: targetNode.pId,
        targetId: targetNode.id,
        type: moveType
    };

    if ('inner' == moveType) param.pId = targetNode.id;

    Manage.Category.editOrder(param);
    return true;
};