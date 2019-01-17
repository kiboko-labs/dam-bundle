define(function (require) {
    'use strict';

    var TreeManageView;

    var _ = require('underscore');
    var $ = require('jquery');
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

        treeEvents: {
            'create_node.jstree': 'onNodeCreate',
            'rename_node.jstree': 'onNodeNameChange',
            'delete_node.jstree': 'onNodeDelete',
            'move_node.jstree': 'onNodeMove',
            'select_node.jstree': 'onNodeOpen',
            'dnd_stop.vakata': 'onDragStop',
            'ready.jstree': 'onTreeLoaded',
        },

        uploadWidget: null,
        createNodeWidget: null,

        rootUuid: null,

        /**
         * @inheritDoc
         */
        constructor: function TreeManageView() {
            TreeManageView.__super__.constructor.apply(this, arguments);
            this.rootUuid = arguments[0].menu;
            this.uploadWidget = $(".upload_button_widget");
            this.createNodeWidget = $('.pull-right a');
        },

        /**
         * Format uuid
         * @param chain
         * @returns {*}
         */
        formatUuuid: function (chain) {
            return chain.substr(5).replace(/_/g, '-');
        },

        /**
         * Refresh document datagrid at uuid node provided
         * @param nodeUuid
         */
        reloadDocumentGrid: function (nodeUuid) {

            $("div[class='grid-views']").ready(function(){
                mediator.trigger('datagrid:setParam:' + 'kiboko-dam-documents-grid', 'parent', nodeUuid);
                mediator.trigger('datagrid:doRefresh:' + 'kiboko-dam-documents-grid');
            });
        },

        /**
         * Refresh buttons route with uuid provided
         * @param nodeUuid
         */
        refreshButtonsRoute: function (nodeUuid) {
            this.createNodeWidget.attr('href', routing.generate('kiboko_dam_node_create', {
                'parent': nodeUuid,
                'root': this.rootUuid
            }));

        },

        /**
         * Update document grid, buttons route and tree position after page reload
         * @param event
         * @param data
         */
        onTreeLoaded: function (event, data) {

            var path = window.location.pathname;
            var regexChildUuid = /(?<=browse\/).*$/g;
            if(path.match(regexChildUuid)) {
                var nodeUuid = path.match(regexChildUuid).toString();
            }

            if (nodeUuid) {

                this.reloadDocumentGrid(nodeUuid);
                this.refreshButtonsRoute(nodeUuid);

            }
        },

        /**
         * Triggers after node deleted in tree
         *
         * @param {Event} e
         * @param {Object} data
         */
        onDragStop: function (e, data) {

        },

        /**
         * Triggers after node deleted in tree
         *
         * @param {Event} e
         * @param {Object} data
         */
        onNodeDelete: function (e, data) {

            var uuid = data.node.original.uuid;
            var url = routing.generate('kiboko_dam_document_node_tree_ajax_delete', {uuid: uuid});

            $.ajax({
                async: true,
                type: 'DELETE',
                url: url
            });
            // Todo: update url correclty
         //   window.history.pushState("object or string", "Title", "/new-url");

        },
        /**
         * Triggers after node is opened
         *
         * @param {Event} e
         * @param {Object} data
         */
        onNodeOpen: function (e, data) {

            var url = window.location.pathname;
            var regex = /browse(.*)/g;
            var newUrl = 'browse/';
            newUrl += this.formatUuuid(data.node.id);
            window.history.pushState("", "", url.replace(regex,newUrl));


            this.reloadDocumentGrid(this.formatUuuid(data.node.id));
            this.refreshButtonsRoute(this.formatUuuid(data.node.id));
        },
        /**
         * Triggers after node deleted in tree
         *
         * @param {Event} e
         * @param {Object} data
         */
        onNodeMove: function (e, data) {

            if (data.node.parent === "#") {
                e.stopImmediatePropagation();
                this.tree.jstree("refresh");
                return;
            }
            var uuid = this.formatUuuid(data.node.id);
            var uuidParent = this.formatUuuid(data.parent);

            if (uuid && uuidParent) {

                if (uuid !== uuidParent) {
                    var url = routing.generate('kiboko_dam_document_node_tree_ajax_move', {
                        uuid: uuid,
                        uuidParent: uuidParent
                    });
                    $.ajax({
                        async: true,
                        type: 'POST',
                        url: url
                    });
                }
            }

        },

        /**
         * Triggers after node change name
         *
         * @param {Event} e
         * @param {Object} data
         */
        onNodeNameChange: function (e, data) {
            var uuid = data.node.original.uuid;
            if (uuid) {
                var name = data.text;
                if (data.node.original.uuid !== '') {
                    var url = routing.generate('kiboko_dam_document_node_tree_ajax_rename', {uuid: uuid});
                    $.ajax({
                        async: true,
                        type: 'POST',
                        data: {
                            'newName': name
                        },
                        url: url
                    });
                }
            }
        },

        /**
         * Triggers after node change creation
         *
         * @param {Event} e
         * @param {Object} data
         */
        onNodeCreate: function (e, data) {
            var parent = data.parent;
            var name = data.node.original.text;

            if (data.node.original.uuid !== '') {
                var url = routing.generate('kiboko_dam_document_node_tree_ajax_create', {uuid: this.formatUuuid(parent)});
                $.ajax({
                    type: 'POST',
                    data: {
                        'name': name,
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
        onSelect: function (e, selected) {

            BaseTreeManageView.__super__.onSelect.apply(this, arguments);

            if (this.initialization || !this.updateAllowed) {
                return;
            }
            let url;
            if (this.onRootSelectRoute && selected.node.parent === '#') {
                url = routing.generate(this.onRootSelectRoute, {
                    root: selected.node.original.storage,
                    node: selected.node.original.uuid
                });
            } else {
                url = routing.generate(this.onSelectRoute, {
                    root: selected.node.original.storage,
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
        customizeTreeConfig: function (options, config) {

            config.core.check_callback =  function (operation, node, parent, position, more) {
                if(operation === "copy_node" || operation === "move_node") {
                    if(parent.id === "#") {
                        return false; // prevent moving a child above or below the root
                    }
                }
                return true; // allow everything else
            }
            config.root = {
                "valid_children": ["default"],
            }
            if (this.checkboxEnabled) {
                // config.plugins.push('checkbox');
                config.plugins.push('contextmenu');
                config.plugins.push('dnd');
                config.plugins.push('unique');
                config.plugins.push('sort');
                config.plugins.push('state');

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

            config.contextmenu = {
                "items": (function ($node) {
                    var tree = this.$tree.jstree(true);

                    return {
                        "Rename": {
                            "separator_before": false,
                            "separator_after": false,
                            "label": _.__('kiboko.dam.js.jstree.contextmenu.rename.label'),
                            "action": (function (obj) {
                                this.edit($node);
                            }).bind(tree)
                        },
                        "Create Folder": {
                            "seperator_before": false,
                            "seperator_after": false,
                            "label": _.__('kiboko.dam.js.jstree.contextmenu.newfolder.label'),
                            action: function (data) {
                                tree.create_node($node, { text: _.__('kiboko.dam.js.jstree.contextmenu.newfolder.label'), type: 'default' });
                                tree.deselect_all();
                                tree.select_node($node);
                            }
                        },
                        "Remove": {
                            "separator_before": false,
                            "separator_after": false,
                            "label": _.__('kiboko.dam.js.jstree.contextmenu.delete.label'),
                            "action": (function (obj) {
                                this.delete_node($node);
                            }).bind(tree)
                        }
                    };
                }).bind(this)
            };

            return config;
        },
    });

    return TreeManageView;
});
