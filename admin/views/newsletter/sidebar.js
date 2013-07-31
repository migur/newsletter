
window.addEvent('domready', 
    function() {
        var 
            sidebar = $('htmlconstructor-sidebar'),
            viewport = $(window),
<<<<<<< HEAD
            treshold = sidebar.getPosition().y + 100;

	
=======
            treshold = sidebar.getPosition().y - 20;

>>>>>>> development
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

<<<<<<< HEAD
        	console.log(sidebar, viewport, treshold);
=======
>>>>>>> development
            var scroll = viewport.getScroll();

            if (scroll.y > treshold) {
                fixSidebar();
            } else {
                releaseSidebar();
            }
        })

    }
)