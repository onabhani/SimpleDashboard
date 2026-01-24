/**
 * DOFS Dashboard Admin Scripts
 */
(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initSortable();
        initAddItem();
        initRemoveItem();
    });

    /**
     * Initialize sortable functionality
     */
    function initSortable() {
        if ($.fn.sortable) {
            $('.dofs-items-list').sortable({
                handle: '.dofs-item-handle',
                placeholder: 'dofs-item-row ui-sortable-placeholder',
                axis: 'y',
                update: function(event, ui) {
                    updateItemOrder($(this));
                }
            });
        }
    }

    /**
     * Update hidden order fields after sorting
     */
    function updateItemOrder($list) {
        $list.find('.dofs-item-row').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('input, select').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    var newName = name.replace(/\[\d+\]/, '[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
    }

    /**
     * Initialize add item functionality using WordPress templates
     */
    function initAddItem() {
        // Quick Access add button
        $('#add-quick-access-item').on('click', function(e) {
            e.preventDefault();
            addItemFromTemplate('quick-access-item', '#quick-access-items');
        });

        // Quick Actions add button
        $('#add-quick-actions-item').on('click', function(e) {
            e.preventDefault();
            addItemFromTemplate('quick-actions-item', '#quick-actions-items');
        });

        // Services add button
        $('#add-services-item').on('click', function(e) {
            e.preventDefault();
            addItemFromTemplate('services-item', '#services-items');
        });
    }

    /**
     * Add item from WordPress underscore template
     */
    function addItemFromTemplate(templateId, listSelector) {
        var $list = $(listSelector);
        var count = $list.find('.dofs-item-row').length;

        // Check if wp.template is available (WordPress underscore templates)
        if (typeof wp !== 'undefined' && typeof wp.template === 'function') {
            var template = wp.template(templateId);
            var html = template({ index: count });
            $list.append(html);
        } else {
            // Fallback: Clone the template script content and replace placeholders
            var $template = $('#tmpl-' + templateId);
            if ($template.length) {
                var html = $template.html()
                    .replace(/\{\{data\.index\}\}/g, count);
                $list.append(html);
            }
        }

        // Reinitialize sortable
        initSortable();

        // Focus on first input of new row
        $list.find('.dofs-item-row:last input:first').focus();
    }

    /**
     * Initialize remove item functionality
     */
    function initRemoveItem() {
        $(document).on('click', '.dofs-remove-item', function(e) {
            e.preventDefault();

            var $row = $(this).closest('.dofs-item-row');
            var $list = $row.closest('.dofs-items-list');

            if (confirm(dofsAdmin && dofsAdmin.strings ? dofsAdmin.strings.confirmDelete : 'Are you sure you want to remove this item?')) {
                $row.fadeOut(200, function() {
                    $(this).remove();
                    updateItemOrder($list);
                });
            }
        });
    }

})(jQuery);
