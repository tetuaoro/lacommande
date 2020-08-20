$(document).ready(function () {
    $(".category-input").select2({
        tags: true
    });
    $("#menu_meals").select2();
    $("form[name=menu]").submit(e => {
        e.preventDefault();
        console.log($(this).find("#menu_category").val());
    })
});