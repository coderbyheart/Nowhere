jQuery(function ($) {
    var positionStone = function () {
        var stoneImg = $('#stone img');
        if (stoneImg.length > 0) {
            if (stoneImg.height() == 0) {
                stoneImg.load(positionStone);
            } else {
                var diff = ($(window).height() - stoneImg.height()) / 2;
                stoneImg.attr('style', 'margin-top: ' + diff + 'px');
            }
        }
    };
    $(document).ready(positionStone);
    $(window).resize(positionStone);
});