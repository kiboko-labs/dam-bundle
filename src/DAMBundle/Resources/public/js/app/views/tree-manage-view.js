define(function(require) {
    'use strict';

    var TreeManageView;

    var _ = require('underscore');
    var $ = require('jquery');
    var messenger = require('oroui/js/messenger');
    var mediator = require('oroui/js/mediator');
    var routing = require('routing');
    var tools = require('oroui/js/tools');

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

        treeEvents: {
            'create_node.jstree': 'onCreate',
            'rename_node.jstree': 'onNodeNameChange',
            'delete_node.jstree': 'onNodeDelete',
            'move_node.jstree': 'onNodeMove',
        },

        /**
         * Triggers after node deleted in tree
         *
         * @param {Event} e
         * @param {Object} data
         */
        onNodeDelete: function(e, data) {

            var uuid = data.node.original.uuid;
            var url = routing.generate('kiboko_dam_document_node_tree_ajax_delete', {uuid: uuid});

            $.ajax({
                async: false,
                type: 'DELETE',
                url: url
            });
        },

        /**
         * Triggers after node deleted in tree
         *
         * @param {Event} e
         * @param {Object} data
         */
        onNodeMove: function(e, data) {
            var newParent = data.parent;
            var oldParent = data.old_parent;
            var uuidParent = this.$tree.jstree(true).get_node(data.parent).original.uuid;

            var uuid = data.node.original.uuid;
                var url = routing.generate('kiboko_dam_document_node_tree_ajax_move', {uuid: uuid});
                $.ajax({
                    async: false,
                    type: 'POST',
                    data: {
                        'newParent': uuidParent
                    },
                    url: url
                });
        },
        /**
         * Triggers after node change name
         *
         * @param {Event} e
         * @param {Object} data
         */
        onNodeNameChange: function(e, data) {
        var uuid = data.node.original.uuid;
        var name = data.text;
        if (data.node.original.uuid === '') {
            var url = routing.generate('kiboko_dam_document_node_tree_ajax_rename', {uuid: uuid});
            $.ajax({
                async: false,
                type: 'POST',
                data: {
                    'newName': name
                },
                url: url
            });
        }
        },

        /**
         * Triggers after node selection in tree
         *
         * @param {Event} e
         * @param {Object} selected
         */
        onSelect: function(e, selected) {

            BaseTreeManageView.__super__.onSelect.apply(this, arguments);

            if (this.initialization || !this.updateAllowed) {
                return;
            }
            let url;
            if (this.onRootSelectRoute && selected.node.parent === '#') {
                url = routing.generate(this.onRootSelectRoute, {
                    storage: selected.node.original.storage,
                    node: selected.node.original.uuid
                });
            } else {
                url = routing.generate(this.onSelectRoute, {
                    storage: selected.node.original.storage,
                    node: selected.node.original.uuid
                });
            }
            mediator.execute('redirectTo', {url: url});
        },

        /**
         * Customize jstree config to add plugins, callbacks etc.
         *
         * @param {Object} options
         * @param {Object} config
         * @returns {Object}
         */
        customizeTreeConfig: function(options, config) {

            if (this.checkboxEnabled) {
                config.plugins.push('checkbox');
                config.plugins.push('contextmenu');
                config.plugins.push('dnd');
                config.checkbox = {
                    whole_node: false,
                    tie_selection: false,
                    three_state: false
                };

                this.$('[data-role="jstree-checkall"]').show();
            }

            if (this.$searchField.length) {
                config.plugins.push('search');
                config.search = {
                    close_opened_onclear: true,
                    show_only_matches: true,
                    show_only_matches_children: false,
                    case_sensitive: false,
                    search_callback: _.bind(this.searchCallback, this)
                };
            }

            if (_.isUndefined(options.autohideNeighbors)) {
                config.autohideNeighbors = tools.isMobile();
            } else {
                config.autohideNeighbors = options.autohideNeighbors;
            }

            return config;
        },
    });

    return TreeManageView;
});
