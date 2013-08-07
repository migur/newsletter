
window.addEvent('domready',
    function() {
        var
            sidebar = $('htmlconstructor-sidebar'),
            viewport = $(window),
            treshold = sidebar.getPosition().y + 100;


        var fixSidebar = function(){

            if (sidebar.hasClass('fixed')) return;

            sidebar.addClass('fixed');
//            var pos    = sidebar.getPosition(),
//                scroll = viewport.getScroll();
//
//            sidebar.setStyle('top', scroll.y - pos.y);
        }

        var releaseSidebar = function(){

            if (!sidebar.hasClass('fixed')) return;

            sidebar.removeClass('fixed');
        }

        $(viewport).addEvent('scroll', function(){

            var scroll = viewport.getScroll();

            if (scroll.y > treshold) {
                fixSidebar();
            } else {
                releaseSidebar();
            }
        })

    }
)
