define(function(require) {
    'use strict';

    var DragAndDropComponent;

    var BaseComponent = require('oroui/js/app/components/base/component');
    var $ = require('jquery');
    var _ = require('underscore');
    var tools = require('oroui/js/tools');


    function allowDrop(event) {
        event.preventDefault();
        document.getElementsByClassName("drop-target").innerHTML = "The p element is OVER the droptarget.";
        event.target.style.border = "10px dotted green";
    }

    function drop(event) {
        event.preventDefault();

    }
    /**
     * @export kibokodam/js/app/views/drag-and-drop
     * @extends oroui.app.components.base.Component
     * @class kibokodam.app.components.DragAndDrop
     */
    DragAndDropComponent = BaseComponent.extend({
        /**
         * @inheritDoc
         */
        constructor: function PasswordGenerateComponent() {
            DragAndDropComponent.__super__.constructor.apply(this, arguments);
        },

        initialize: function(options) {


            // window.addEventListener("dragover",function(e){
            //     e = e || event;
            //     e.preventDefault();
            // },false);
            // window.addEventListener("drop",function(e){
            //     e = e || event;
            //     e.preventDefault();
            // },false);

            $(".drop-target").find('*').addBack().on(
                'dragover',
                function(e) {
                }
            );
            $('.drop-target').find('*').on(
                'dragenter',
                function(e) {
                    var $this = $(this);
                    $this.css("opacity",0.2);
                    e.preventDefault();
                    e.stopPropagation();

                }
            );
            $('.drop-target').find('*').on(
                'dragleave',
                function(event) {
                    var $this = $(this);
                    $this.css("opacity",0.2);
                    e.preventDefault();
                    e.stopPropagation();
                    // var dataTransfer =  e.originalEvent.dataTransfer;
                    // if( dataTransfer && dataTransfer.files.length) {
                    //     e.preventDefault();
                    //     e.stopPropagation();
                    //     //more code here...
                    // }
                }
            );


        },
    });

    return DragAndDropComponent;
});
