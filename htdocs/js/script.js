var positionStone = function () {
    var stoneImg = $('#stone img');
    if (stoneImg.length > 0) {
        var wwidth = $(window).width();
        var wheight = $(window).height();
        var iwidth = stoneImg.width();
        var iheight = stoneImg.height();
        var wr = wheight / wwidth;
        var ir = iheight / iwidth;
        var wr2 = wwidth / wheight;
        var ir2 = iwidth / iheight;

        if (wr <= ir) {
            stoneImg.css('width', '100%');
            stoneImg.css('height', 'auto');
            stoneImg.css('margin-left', '0');
            stoneImg.css('margin-top', ((wheight - iheight) / 2) + 'px');
        } else if (wr2 <= ir2) {
            stoneImg.css('width', 'auto');
            stoneImg.css('height', '100%');
            stoneImg.css('margin-top', '0');
            stoneImg.css('margin-left', ((wwidth - iwidth) / 2) + 'px');
        }
    }
};
var positionStone2 = function () {
    positionStone();
    positionStone();
}
jQuery(function ($) {
    $(window).load(positionStone2);
    $(window).resize(positionStone);
});
