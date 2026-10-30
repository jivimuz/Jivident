!function( $ ) {
    var WinResizer = (function() {
        var registered = [];
        var inited = false;
        var timer;
        var resize = function() {
            clearTimeout(timer);
            timer = setTimeout(notify, 100);
        };
        var notify = function() {
            for (var i = 0; i < registered.length; i++) {
                registered[i].apply();
            }
        };
        return {
            register: function(fn) {
                registered.push(fn);
                if (!inited) {
                    $(window).on('resize', resize);
                    inited = true;
                }
            },
            unregister: function(fn) {
                registered = registered.filter(function(item) {
                    return item !== fn;
                });
            }
        };
    }());

    var TabDrop = function(element, options) {
        this.element = $(element);
        this.dropdown = $(
            '<li class="dropdown d-none pull-right tabdrop">' +
            '<a class="dropdown-toggle" data-toggle="dropdown" href="#">' +
            options.text +
            '</a><ul class="dropdown-menu"></ul></li>'
        ).prependTo(this.element);

        if (this.element.parent().hasClass('tabs-below')) {
            this.dropdown.addClass('dropup');
        }
        WinResizer.register($.proxy(this.layout, this));
        this.layout();
    };

    TabDrop.prototype = {
        constructor: TabDrop,
        layout: function() {
            var collection = [];
            this.dropdown.removeClass('d-none');
            this.element
                .append(this.dropdown.find('li'))
                .find('>li')
                .not('.tabdrop')
                .each(function() {
                    if (this.offsetTop > 0) {
                        collection.push(this);
                    }
                });

            if (collection.length > 0) {
                this.dropdown.find('ul').empty().append(collection);
                this.dropdown.toggleClass('active', !!this.dropdown.find('.active').length);
            } else {
                this.dropdown.addClass('d-none');
            }

            // Pastikan tabdrop ada di posisi terakhir
            this.element.append(this.dropdown);
        },

        bindDropdownEvent: function() {
            // Re-bind dropdown toggle to ensure functionality
            this.dropdown.find('.dropdown-toggle').dropdown();
        }
    };

    $.fn.tabdrop = function(option) {
        return this.each(function() {
            var $this = $(this);
            var data = $this.data('tabdrop');
            var options = typeof option === 'object' && option;
            if (!data) {
                $this.data('tabdrop', new TabDrop(this, $.extend({}, $.fn.tabdrop.defaults, options)));
            }
            if (typeof option === 'string') {
                data[option]();
            }
        });
    };

    $.fn.tabdrop.defaults = {
        text: '<i class="icon-bars" style="color: orange; font-size:10px;margin-top:10px"></i>'
    };
}(jQuery);

// Inisialisasi
$('#responsive-tabs .nav-tabs').tabdrop();

// Tambahkan event handler eksplisit untuk dropdown
$('main').on('click', '.dropdown-toggle', function(e) {
    e.preventDefault();
    $(this).parent().find('.dropdown-menu').toggle();
});
