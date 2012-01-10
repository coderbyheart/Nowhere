var positionStone = function () {
    var stoneImg = $('#stone img');
    if (stoneImg.length > 0) {
        if (!stoneImg.data('width')) {
            stoneImg.data('width', parseInt(stoneImg.prop('width'), 10));
            stoneImg.data('height', parseInt(stoneImg.prop('height'), 10));
            stoneImg.prop("width", null);
            stoneImg.prop("height", null);
        }
        var wwidth = $(window).width();
        var wheight = $(window).height();
        var iwidth = stoneImg.data('width');
        var iheight = stoneImg.data('height');
        var wr = wheight / wwidth;
        var ir = iheight / iwidth;
        var wr2 = wwidth / wheight;
        var ir2 = iwidth / iheight;

        if (wr <= ir) {
            var theHeight = (wwidth / iwidth) * iheight;
            stoneImg.css({
                'width':wwidth,
                'height':theHeight,
                'margin-left':'0',
                'margin-top':((wheight - theHeight) / 2) + 'px'
            });
        } else if (wr2 <= ir2) {
            var theWidth = (wheight / iheight) * iwidth;
            stoneImg.css({
                'width':theWidth,
                'height':wheight,
                'margin-top':'0',
                'margin-left':((wwidth - theWidth) / 2) + 'px'
            });
        }
    }
};

var loadHighresThumbnail = function(ev) {
    var img = $(ev.target);
    if (img.width() > 250) {
        var src = img.prop('src');
        if (src.match(/stone-150\.png/)) {
            img.prop('src', src.replace(/stone-150\.png/, 'stone-1024.png'));
        }
    }
};

jQuery(function ($) {
    $(document).ready(positionStone);
    $(window).resize(positionStone);

    var grid = $(".draggrid");
    var elements = grid.find('li');
    var images = grid.find('img');
    images.mouseenter(loadHighresThumbnail);
    images.mousewheel(loadHighresThumbnail);
    var numVer = Math.round(grid.width() / elements.first().width());
    var numHor = Math.round(elements.length / numVer);
    var gridbox = $(document.createElement('div'));
    gridbox.css({'width': '100%', 'height': '100%', 'overflow': 'hidden', 'position': 'absolute', 'z-index': 10});
    grid.wrap(gridbox);
    var initMargin = (($(window).width() - grid.width()) / 2);
    grid.css({'margin': 0, 'position': 'relative', 'left': initMargin + 'px', 'top': initMargin + 'px'});
    grid.draggable();
    grid.mousewheel(function (event, delta, deltaX, deltaY) {
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
            'width':newWidth
            ,'left': pos.left - offSetLeft * hr
            ,'top': pos.top - offSetTop * vr
        });
    });

});
