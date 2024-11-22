import jQuery from 'jquery/dist/jquery.js';
/*! DataTables Tailwind CSS integration
 */

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'datatables.net'], function ($) {
            return factory($, window, document);
        });
    } else if (typeof exports === 'object') {
        // CommonJS
        var jq = jQuery;
        var cjsRequires = function (root, $) {
            if (!$.fn.dataTable) {
                require('datatables.net')(root, $);
            }
        };

        if (typeof window === 'undefined') {
            module.exports = function (root, $) {
                if (!root) {
                    // CommonJS environments without a window global must pass a
                    // root. This will give an error otherwise
                    root = window;
                }

                if (!$) {
                    $ = jq(root);
                }

                cjsRequires(root, $);
                return factory($, root, root.document);
            };
        } else {
            cjsRequires(window, jq);
            module.exports = factory(jq, window, window.document);
        }
    } else {
        // Browser
        factory(jQuery, window, document);
    }
}(function ($, window, document) {
    'use strict';
    var DataTable = $.fn.dataTable;


    /*
     * This is a tech preview of Tailwind CSS integration with DataTables.
     */

    // Set the defaults for DataTables initialisation
    $.extend(true, DataTable.defaults, {
        renderer: 'tailwindcss'
    });

    // Default class modification
    $.extend(true, DataTable.ext.classes, {
        container: "dt-container dt-tailwindcss",
        search: {
            input: "border outline-0 placeholder-gray-500 ml-2 px-3 py-2 !rounded-lg border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-600 dark:focus:border-blue-500 dark:placeholder-gray-400"
        },
        length: {
            select: "border px-3 py-2 rounded-lg border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-600 dark:focus:border-blue-500"
        },
        processing: {
            container: "dt-processing"
        },
        paging: {
            active: '',
            notActive: 'btn-color7',
            button: 'btn btn-ripple text-nowrap btn-border-0',
            first: '!rounded-l',
            last: '!rounded-r',
            enabled: 'btn-color2',
            notEnabled: '!bg-slate-300 btn-dead'
        },
        table: 'dataTable min-w-full text-sm align-middle whitespace-nowrap',
        thead: {
            row: 'border-b border-gray-100 dark:border-gray-700/50',
            cell: 'px-3 py-4 text-gray-900 bg-gray-100/75 font-semibold text-left dark:text-gray-50 dark:bg-gray-700/25'
        },
        tbody: {
            row: 'even:bg-gray-50 dark:even:bg-gray-900/50',
            cell: 'p-3'
        },
        tfoot: {
            row: 'even:bg-gray-50 dark:even:bg-gray-900/50',
            cell: 'p-3 text-left'
        },
    });

    DataTable.ext.renderer.pagingButton.tailwindcss = function (settings, buttonType, content, active, disabled) {
        var classes = settings.oClasses.paging;
        var btnClasses = [classes.button];

        btnClasses.push(active ? classes.active : classes.notActive);
        btnClasses.push(disabled ? classes.notEnabled : classes.enabled);

        var a = $('<a>', {
            'href': disabled ? null : '#',
            'class': btnClasses.join(' ')
        })
            .html(content);

        return {
            display: a,
            clicker: a
        };
    };

    DataTable.ext.renderer.pagingContainer.tailwindcss = function (settings, buttonEls) {
        var classes = settings.oClasses.paging;

        delete buttonEls[0];
        delete buttonEls[buttonEls.length - 1];

        return $('<ul/>').addClass('btn-group btn-group-border-2-slate px-5').append(buttonEls);
    };

    DataTable.ext.renderer.layout.tailwindcss = function (settings, container, items) {
        var row = $('<div/>', {
            "class": items.full ?
                'grid grid-cols-1 gap-4 mb-4' :
                'grid grid-cols-2 gap-4 mb-4 dt-tool-header'
        }).appendTo(container);

        $.each(items, function (key, val) {
            var klass;

            // Apply start / end (left / right when ltr) margins
            if (val.table) {
                klass = '';
            } else if (key === 'start') {
                klass = 'justify-self-start';
            } else if (key === 'end') {
                klass = 'dt-layout-end';
            } else {
                klass = 'justify-center';
            }

            $('<div/>', {
                id: val.id || null,
                "class": klass + ' ' + (val.className || '')
            })
                .append(val.contents)
                .appendTo(row);
        });
    };


    return DataTable;
}));
