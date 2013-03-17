(function (window, document, $) {

    var Compare = {

        init: function() {
            $('.compare-filter').attr('checked', 'checked');
            $('.compare-all-filter').attr('checked', 'checked');
        },

        toggleFilterContent: function(el) {
            el = $(el);
            var container = el.closest('.filter-container');
            if (container.length) {
                container.toggleClass('open');
            }
        },

        checkAllFilters: function(type) {
            var el = $('.compare-all-filter-' + type);
            if (el.length) {
                var container = el.closest('.filter-container');
                if (container.length) {
                    var filters = container.find('.compare-filter:not(:checked)');
                    if (filters.length) {
                        el.removeAttr('checked');
                    } else {
                        el.attr('checked', 'checked');
                    }
                }
            }
        },

        toggleAllFilters: function(el) {
            el = $(el);
            var container = el.closest('.filter-container');
            if (container.length) {
                var filters = container.find('.compare-filter');
                if (filters.length) {
                    if (el.is(':checked')) {
                        filters.attr('checked', 'checked');
                    } else {
                        filters.removeAttr('checked');
                    }
                    filters.each(function(i, el) {
                        Compare.toggleFilter(el);
                    });
                }
            }
        },

        toggleFilter: function(el) {
            el = $(el);
            var type = el.data('type');
            var containers;
            if (type == 'nation') {
                containers = '.container-nation-';
            } else if (type == 'lvl') {
                containers = '.row-lvl-';
            } else if (type == 'tank-type') {
                containers = '.col-tank-type-';
            }
            if (containers) {
                containers = $(containers + el.data('value'));
                if (containers.length) {
                    if (el.is(':checked')) {
                        containers.show();
                    } else {
                        containers.hide();
                    }
                }
            }
            Compare.checkAllFilters(type);
        },

        compareSelectedVehicles: function() {
            var selected = [];
            $('.vehicle-to-compare:checked').each(function(i, el) {
                el = $(el);
                selected.push(el.attr('name'));
            });
            if (selected.length) {
                var params = $.param({'tanks[]': selected}, true);
                window.location = compareUrl + '?' + params;
            } else {
                alert('Nothing to compare. Please select some vehicles.');
            }
        }

    };

    $(document).on('click', '#compare-button', function(e) {
        Compare.compareSelectedVehicles();
    });

    $(document).on('click', '.explain', function(e) {
        Compare.toggleFilterContent(e.currentTarget);
    });

    $(document).on('click', '.compare-all-filter', function(e) {
        Compare.toggleAllFilters(e.currentTarget);
    });
    $(document).on('click', '.compare-filter', function(e) {
        Compare.toggleFilter(e.currentTarget);
    });

    $(function() {
        Compare.init();
    });

})(this, this.document, this.jQuery);
