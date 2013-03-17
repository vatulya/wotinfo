(function (window, document, $) {

    var Compare = {

        allFilters: $('.filter-parameter'),
        allFiltersCheckbox: $('#filter-parameter-all'),

        init: function() {
            Compare.initAutoScroll();
            Compare.redraw();
            Compare.initFilterParameters();
            Compare.initCompareHighlight();
        },

        initAutoScroll: function() {
            var container = $('.all-vehicle-parameters');
            if (container.length) {
                var scroll = container.data('scroll');
                if (scroll) {
                    var scroll = scroll.split(',');
                    $('.all-parameters').scrollLeft(scroll[0]);
                    $('.all-vehicles').scrollTop(scroll[1]);
                }
            }
        },

        initFilterParameters: function() {
            var filter = window.location.hash.substring(1);
            if (filter) {
                filter = filter.split(',');
            } else {
                filter = $.cookie('filter-parameters');
                filter = JSON.parse(filter);
            }
            if (typeof filter != 'undefined' && filter != null && filter.length > 0) {
                Compare.allFilters.removeAttr('checked');
                $.each(filter, function(i, val) {
                    var el = $('#filter-parameter-' + val);
                    if (el.length) {
                        el.attr('checked', 'checked');
                    }
                });
                Compare.allFilters.each(function(i, el){
                    Compare.toggleFilterParameter(el);
                });
            } else {
                Compare.allFilters.attr('checked', 'checked');
            }
            Compare.checkAllFilterParameters();
            Compare.redraw();
        },

        initCompareHighlight: function() {
            var compare = null;
            if (typeof window.compare != 'undefined' && (compare = window.compare) != null && typeof compare == 'object') {
                for (var param in compare) {
                    if (compare.hasOwnProperty(param)) {
                        $.each(compare[param], function(i, val) {
                            var cell = $('.row-cell-' + val + '.col-cell-' + param);
                            if (cell.length) {
                                cell.addClass('compare-winner');
                            }
                        });
                        // prop is not inherited
                    }
                }
            }
        },

        toggleFilterParameter: function(el) {
            el = $(el);
            var param = el.data('parameter');
            var colCells = $('.col-cell-' + param);
            if (el.attr('checked')) {
                colCells.show();
            } else {
                colCells.hide();
            }
        },

        refreshFilterParametersCookie: function() {
            var filter = [];
            if (Compare.allFiltersCheckbox.not(':checked')) {
                $('.filter-parameter:checked').each(function(i, el) {
                    el = $(el);
                    filter.push(el.data('parameter'));
                });
            }
            window.location.hash = filter.join(',');
            filter = JSON.stringify(filter);
            $.cookie('filter-parameters', filter);
        },

        checkAllFilterParameters: function() {
            if ($('.filter-parameter:not(:checked)').length > 0) {
                Compare.allFiltersCheckbox.removeAttr('checked');
            } else {
                Compare.allFiltersCheckbox.attr('checked', 'checked');
            }
        },

        toggleAllFilterParameters: function() {
            var allEl = $('#filter-parameter-all');
            if (allEl.length) {
                if (allEl.attr('checked')) {
                    $('.filter-parameter:not(:checked)').each(function(i, el) {
                        el = $(el);
                        el.attr('checked', 'checked');
                        Compare.toggleFilterParameter(el);
                    });
                } else {
                    $('.filter-parameter:checked').each(function(i, el) {
                        el = $(el);
                        el.removeAttr('checked');
                        Compare.toggleFilterParameter(el);
                    });
                }
            }
        },

        redraw: function() {
            Compare.setHeaderSize();
        },

        setHeaderSize: function() {
            var maxWidth = $('.all-parameters .col-param:visible').length * 130;
            $('.all-parameters .params-container').css('width', maxWidth + 'px');
            $('.all-vehicle-parameters .params-container').css('width', maxWidth + 'px');
        },

        scrollLeft: function(el) {
            el = $(el);
            var left = el[0].scrollLeft;
            $('.all-vehicle-parameters').scrollLeft(left);
        },

        scrollTop: function(el) {
            el = $(el);
            var top = el[0].scrollTop;
            $('.all-vehicle-parameters').scrollTop(top);
        },

        hover: function(el) {
            el = $(el);
            var tank = el.data('tank');
            var param = el.data('param');
            $('.row-cell-' + tank).addClass('hover');
            $('.col-cell-' + param).addClass('hover');
        },

        unhover: function(el) {
            el = $(el);
            var tank = el.data('tank');
            var param = el.data('param');
            $('.row-cell-' + tank).removeClass('hover');
            $('.col-cell-' + param).removeClass('hover');
        },

        addScrollPosition: function(el) {
            el = $(el);
            var container = $('.all-vehicle-parameters');
            if (container.length) {
                var scroll = container[0].scrollLeft + ',' + container[0].scrollTop;
                if (scroll != '0,0') {
                    el.attr('href', el.attr('href') + '&scroll=' + scroll + window.location.hash);
                }
            }
        }

    };

    $(document).on('click', '.filter-parameter', function(e) {
        Compare.toggleFilterParameter(e.currentTarget);
        Compare.checkAllFilterParameters();
        Compare.refreshFilterParametersCookie();
        Compare.redraw();
    });
    $(document).on('click', '#filter-parameter-all', function(e) {
        Compare.toggleAllFilterParameters();
        Compare.refreshFilterParametersCookie();
        Compare.redraw();
    });
    $(document).on('click', '.sortable-col a', function(e) {
        Compare.addScrollPosition(e.currentTarget);
    });
    $(document).on('mouseover', '.tank-parameter-cell', function(e) {
        Compare.hover(e.currentTarget);
    });
    $(document).on('mouseout', '.tank-parameter-cell', function(e) {
        Compare.unhover(e.currentTarget);
    });

    $('.all-parameters').scroll(function(e) {
        Compare.scrollLeft(e.currentTarget);
    });
    $('.all-vehicles').scroll(function(e) {
        Compare.scrollTop(e.currentTarget);
    });

    $(function() {
        Compare.init();
    });

})(this, this.document, this.jQuery);
