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
    var grid = $(".draggrid");
    var elements = grid.find('li');
    if (!istouch) {
        var images = grid.find('img');
        images.mouseenter(loadHighresThumbnailListener);
        images.mousewheel(loadHighresThumbnailListener);
        // Make the grid square
        var numEls = elements.length;
        var numHor = Math.ceil(Math.sqrt(numEls));
        var numVer = Math.ceil(numEls / numHor);
        elements.css('width', (100 / numHor) + '%');
        var gridbox = $(document.createElement('div'));
        gridbox.css({'width':'100%', 'height':'100%', 'overflow':'hidden', 'position':'absolute', 'z-index':10});
        gridbox.addClass('gridbox');
        grid.wrap(gridbox);
        var gridWidth = grid.width() * 2.25; // Initialer Zoom = 225%
        var initMargin = (($(window).width() - gridWidth) / 2);
        grid.css({'margin':0, 'position':'relative', 'left':initMargin + 'px', 'top':initMargin + 'px', 'width':gridWidth + 'px'});
        elements.css('height', (gridWidth / numHor) + "px");
        grid.draggable();

        var isPlaces = grid.prop('id') == 'placesgrid';
        var portrait = grid.find('img.portrait');
        var landscape = grid.find('img.landscape');
        if (isPlaces) {
            portrait.css('height', ((gridWidth / numHor) * 0.5) + "px");
            landscape.css('width', ((gridWidth / numHor) * 0.5) + "px");
        }

        if (!grid.hasClass('nozoom')) grid.mousewheel(function (event, delta, deltaX, deltaY) {
            var theWidth = grid.width();
            var theHeight = elements.first().width() * numHor;
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
            elements.css('height', (newWidth / numHor) + "px");
            // Scale places
            if (isPlaces) {
                portrait.css('height', ((newWidth / numHor) * 0.5) + "px");
                landscape.css('width', ((newWidth / numHor) * 0.5) + "px");
            }
        });
    } else {
        var fixCoords = function () {
            elements.css('height', elements.first().css('width'));
        }
        $(window).resize(fixCoords);
        fixCoords();
    }
});
