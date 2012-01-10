var loadHighresThumbnail = function (img) {
    img = $(img);
    if (img.width() > 250) {
        var src = img.prop('src');
        if (src.match(/-150\./)) {
            img.prop('src', src.replace(/-150\./, '-1024.'));
        }
    }
};
var loadHighresThumbnailListener = function (ev) {
    loadHighresThumbnail(ev.target);
};

jQuery(function ($) {
    yepnope({
        test: !Modernizr.touch,
        complete: function () {
            var grid = $(".draggrid");
            var elements = grid.find('li');
            var images = grid.find('img');
            images.mouseenter(loadHighresThumbnailListener);
            images.mousewheel(loadHighresThumbnailListener);
            var numVer = Math.round(grid.width() / elements.first().width());
            var numHor = Math.round(elements.length / numVer);
            var gridbox = $(document.createElement('div'));
            gridbox.css({'width':'100%', 'height':'100%', 'overflow':'hidden', 'position':'absolute', 'z-index':10});
            gridbox.addClass('gridbox');
            grid.wrap(gridbox);
            var initMargin = (($(window).width() - grid.width()) / 2);
            grid.css({'margin':0, 'position':'relative', 'left':initMargin + 'px', 'top':initMargin + 'px'});
            grid.draggable();
            images.each(function (idx, img) {
                loadHighresThumbnail(img);
            });

            if (!grid.hasClass('nozoom')) grid.mousewheel(function (event, delta, deltaX, deltaY) {
                var theWidth = grid.width();
                var theHeight = elements.first().height() * numHor;
                var newWidth = theWidth * (deltaY > 0 ? 1.05 : 0.95 );

                var newHeight = theHeight * (deltaY > 0 ? 1.05 : 0.95 );
                var offSetLeft = newWidth - theWidth;
                var offSetTop = newHeight - theHeight;
                var pos = grid.position();

                var hr = (event.pageX - pos.left) / theWidth;
                var vr = (event.pageY - pos.top) / theHeight;

                grid.css({
                    'width':newWidth, 'left':pos.left - offSetLeft * hr, 'top':pos.top - offSetTop * vr
                });
            });
        }
    });
});
