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

        formatUuuid: function (chain) {
            return chain.substr(5).replace(/_/g, '-');
        },

        /**
         * @inheritDoc
         */
        constructor: function TreeManageView() {
            TreeManageView.__super__.constructor.apply(this, arguments);
        },

        treeEvents: {
            'create_node.jstree': 'onNodeCreate',
            'rename_node.jstree': 'onNodeNameChange',
            'delete_node.jstree': 'onNodeDelete',
            'move_node.jstree': 'onNodeMove',
            'select_node.jstree': 'onNodeOpen',
            'dnd_stop.vakata': 'onDragStop',
            'ready.jstree': 'onTreeLoaded',
        },

        onTreeLoaded: function (e,data) {
            var url = window.location.pathname;
            var regex = /(?<=browse\/).*$/g;
            var nodeUuid = url.match(regex).toString();
            console.log(dataUrl);
            var str = 'node_';

                if(nodeUuid) {

                    var dataUrl = $(".upload_button_widget").attr('data-url');
                    $(".upload_button_widget").attr('data-url',dataUrl.replace(/\b[0-9a-f]{8}\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\b[0-9a-f]{12}\b/
                        ,nodeUuid ));
                    nodeUuid = nodeUuid.replace(/-/g, '_');
                nodeUuid = str.concat('', nodeUuid);
                this.jsTreeInstance._open_to(nodeUuid);
                this.jsTreeInstance.select_node(nodeUuid);
            }
        },

        /**
         * Triggers after node deleted in tree
         *
         * @param {Event} e
         * @param {Object} data
         */
        onDragStop: function (e, data) {

            console.log('salut');

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
            mediator.trigger('datagrid:setParam:' + 'kiboko-dam-documents-grid', 'parent', this.formatUuuid(data.node.original.id));
            mediator.trigger('datagrid:doRefresh:' + 'kiboko-dam-documents-grid');
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
                    async: true,
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
        customizeTreeConfig: function (options, config) {

            config.root = {
                "valid_children": ["default"],
            }
            if (this.checkboxEnabled) {
                // config.plugins.push('checkbox');
                config.plugins.push('contextmenu');
                config.plugins.push('dnd');
                config.plugins.push('unique');

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
                                var inst = $.jstree.reference(data.reference),
                                    obj = inst.get_node(data.reference);
                                inst.create_node(obj, {}, "last", function (new_node) {
                                    try {
                                        inst.edit(new_node);
                                    } catch (ex) {
                                        setTimeout(function () {
                                            inst.edit(new_node);
                                        }, 0);
                                    }
                                });
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
