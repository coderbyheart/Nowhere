// Text-Images
function createTextImage(el) {
    el = $(el);
    var elText = el.text();
    el.empty();
    var tagname = el.prop('tagName').toLowerCase();
    var classRegex = /textimage-([a-z0-9-]+)/;
    var classMatch = classRegex.exec(el.prop('class'));
    if (classMatch) tagname = classMatch[1];
    var img = $(document.createElement('img'));
    img.prop('src', '/image.php?class=' + encodeURIComponent(tagname) + '&text=' + encodeURIComponent(elText));
    img.prop('alt', elText);
    img.addClass('imagetext');
    el.append(img);
}

jQuery(function ($) {
    $(document).ready(function () {
        $('.textimage').each(function (idx, el) {
            createTextImage(el);
        });
    });
});

