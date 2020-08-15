$(document).ready(function () {
  // FALSH MESSAGE
  var flash = $("#flash");
  if (flash.children().length) {
    flash.show("fast");
    setTimeout(() => {
      flash.hide("fast");
    }, 11959);
  }

  // BTN COLLAPSE (PLUS OPTION - MOINS OPTION)
  $(".btn-form-collapse").click(function () {
    if ($(".btn-form-collapse.btn-show-toggle").length > 0) {
      $(this).html(
        "Plus d'option <i class='fas fa-plus-circle ml-1' aria-hidden='true'></i>"
      );
    } else {
      $(this).html(
        "Moins d'option <i class='fas fa-minus-circle ml-1' aria-hidden='true'></i>"
      );
    }
    $(this).toggleClass("btn-show-toggle");
  });
});
