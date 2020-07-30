$(document).ready(function () {
    $('.pagination .page-item a').each(function (index, element) {
        // element == this
        $(this).attr('href', $(this).attr('href') + "#meal-section");
    });
});