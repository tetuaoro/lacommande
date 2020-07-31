$(document).ready(function () {
    $('.pagination .page-item a').each(function (index, element) {
        // element == this
        $(this).attr('href', $(this).attr('href') + "#meal-section");
    });
});

$(".custom-file-label").change(function (e) { 
    e.preventDefault();
    $(selector).css("border-bottom", "3px solid #dcdc0a");    
});