var istouch = navigator.userAgent.toLowerCase().match(/(ipad|iphone|mobile)/) != null;

var positionStone = function () {
    var stoneImg = $('#stone img.resize');
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

var tooltip;
var lasta;
var tooltipTimer;
var tooltipX;
var tooltipY;
var tooltipShowing;

var startTooltipTimer = function (ev) {
    var a = $(ev.target).closest('a');
    tooltipTimer = window.setTimeout(showTooltip, 500, a);
};

var setXY = function(ev)
{
    tooltipX = ev.pageX;
    tooltipY = ev.pageY;
    if (tooltipShowing) {
        hideTooltip(ev);
        startTooltipTimer(ev);
    }
}

var showTooltip = function (a) {
    tooltipShowing = true;
    if (lasta != a) {
        tooltip.empty();
        tooltip.append(a.data("title"));
        tooltip.css({
            'display':'block',
            'left':tooltipX + 20,
            'top':tooltipY + 20
        });
    } else {
        tooltip.css({
            'left':tooltipX + 20,
            'top':tooltipY + 20
        });
    }
};

var hideTooltip = function (ev) {
    tooltipShowing = false;
    if (tooltipTimer) window.clearInterval(tooltipTimer);
    tooltip.css('display', 'none');
};

jQuery(function ($) {
    $(document).ready(positionStone);
    $(window).resize(positionStone);
    $('.link-home').click(function (ev) {
        window.location.pathname = $('a[rel=bookmark]').prop('href');
    });
    $('.link-stones-or-places').click(function (ev) {
        if ($.cookie('lastloc') != null) {
            window.location.pathname = $.cookie('lastloc');
        }
    });
    $('a.external').click(function (ev) {
        ev.preventDefault();
        window.open($(ev.target).closest('a').prop('href'));
        return false;
    });
    // Tooltips
    tooltip = $('#tooltip');
    $('a').each(function (idx, el) {
        var a = $(el);
        var title = a.prop("title");
        if (!title.length) return;
        a.data("title", title);
        a.prop("title", "");
        a.addClass("tooltip");
    });

    $('a.tooltip').mousemove(setXY);
    $('a.tooltip').mouseenter(startTooltipTimer);
    $('a.tooltip').mouseleave(hideTooltip);
});
