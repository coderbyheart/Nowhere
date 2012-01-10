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

jQuery(function ($) {
    $(document).ready(positionStone);
    $(window).resize(positionStone);
});
