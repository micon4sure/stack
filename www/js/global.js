/**
 * highlightText jquery
 */
(function( $, undefined ) {

    $.effects.highlightText = function(o) {
        return this.queue(function() {
            var elem = $(this),
                props = ['opacity', 'color'],
                mode = $.effects.setMode(elem, o.options.mode || 'show'),
                animation = {
                    color: elem.css('color')
                };

            if (mode == 'hide') {
                animation.opacity = 0;
            }

            $.effects.save(elem, props);
            elem
                .show()
                .css({
                    color: o.options.color || '#ffff99'
                })
                .animate(animation, {
                    queue: false,
                    duration: o.duration,
                    easing: o.options.easing,
                    complete: function() {
                        (mode == 'hide' && elem.hide());
                        $.effects.restore(elem, props);
                        (mode == 'show' && !$.support.opacity && this.style.removeAttribute('filter'));
                        (o.callback && o.callback.apply(this, arguments));
                        elem.dequeue();
                    }
                });
        });
    };

})(jQuery);

// classes
var Application = new Class({
    initialize: function() {
        this.login = new Login();
        this.login.showUname();
    }
});
var Login = new Class({
    initialize: function() {
        this.uname = '';
        this.upass = '';
    },
    showUname: function() {
        // empty out terminal
        $terminal = $('#terminal');
        $terminal.empty();

        // append and focus uname, attach event listener
        var $uname = $('<input type="text" class="login" id="login_uname"/>');
        $terminal.append('<span>login: </span>');
        $terminal.append($uname);
        $uname.focus();

        $uname.keyup(function(event) {
            if(event.which == 13) {
                // enter has been pressed
                // put name in span and login object, remove uname input
                application.login.uname = $uname.val();
                $terminal.append('<span>' + $uname.val() + '</span><br/>');
                $uname.remove();
                application.login.showUpass()
            }
        });
    },
    showUpass: function() {
        // append and focus uname, attach event listener
        var $upass = $('<input type="password" class="login" id="login_password"/>');
        $terminal.append('<span>password: </span>');
        $terminal.append($upass);
        $upass.focus();

        $upass.keyup(function(event) {
            if(event.which == 13) {
                // enter has been pressed
                application.login.upass = $upass.val();
                application.login.login();
            }
        });
    },
    login: function() {
        $.post('/login');
    }
});

// initializer
jQuery(function() {
    var i = 0;
    $('#stack_ascii').animate({
            top: "10px",
            right: "50px",
            fontSize: "7px"
        },
        "slow",
        function() {
        $('#stack_ascii').children().each(function(index, row) {
            window.setTimeout(function() {
                $(row).effect('highlightText', {
                    color: '#ef2929'
                }, 2000);
            }, i * 30)
            i++;
        });
    });

    window.application = new Application();
});