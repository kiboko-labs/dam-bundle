define(function(require) {
    'use strict';

    var TreeManageView;

    var _ = require('underscore');
    var messenger = require('oroui/js/messenger');
    var BaseTreeManageView = require('oroui/js/app/views/jstree/base-tree-manage-view');

    /**
     * @export kibokodam/js/app/views/tree-manage-view
     * @extends oroui.app.components.BaseTreeManageView
     * @class kibokodam.app.components.TreeManageView
     */
    TreeManageView = BaseTreeManageView.extend({
        /**
         * @inheritDoc
         */
        constructor: function TreeManageView() {
            TreeManageView.__super__.constructor.apply(this, arguments);
        },

        /**
         * Triggers after page move
         *
         * @param {Object} e
         * @param {Object} data
         */
        onMove: function(e, data) {
            if (this.moveTriggered) {
                return;
            }

            if (data.parent === '#') {
                this.rollback(data);
                messenger.notificationFlashMessage('warning', _.__('oro.catalog.jstree.add_new_root_warning'));
                return;
            }

            TreeManageView.__super__.onMove.call(this, e, data);
        }
    });

    return TreeManageView;
});
