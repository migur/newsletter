
window.addEvent('domready', 
    function() {
        var 
            sidebar = $('htmlconstructor-sidebar'),
            viewport = window,
            treshold = sidebar.getPosition().y - 50;

        var fixSidebar = function(){
            sidebar.addClass('fixed');
        }

        var releaseSidebar = function(){
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