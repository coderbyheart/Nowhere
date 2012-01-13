// Text-Images
function addText(tagname, elText, el, hover) {
    if (hover) tagname += "-over";
    var img = $(document.createElement('img'));
    img.prop('src', '/image.php?class=' + encodeURIComponent(tagname) + '&text=' + encodeURIComponent(elText));
    img.prop('alt', elText);
    img.addClass('imagetext');
    if (hover) {
        img.addClass("over");
    } else {
        img.addClass("normal");
    }
    el.append(img);
}
;

function createTextImage(el) {
    el = $(el);
    var elText = el.text();
    el.empty();
    var tagname = el.prop('tagName').toLowerCase();
    var classRegex = /textimage-([a-z0-9-]+)/;
    var classMatch = classRegex.exec(el.prop('class'));
    if (classMatch) tagname = classMatch[1];
    addText(tagname, elText, el, false);
    if (el.hasClass("hover")) {
        addText(tagname, elText, el, true);
    }
    if (el.hasClass("hover-parent")) {
        addText(tagname, elText, el, true);
    }
}
;

jQuery(function ($) {
    $(document).ready(function () {
        $('.textimage').each(function (idx, el) {
            createTextImage(el);
        });
    });
});

