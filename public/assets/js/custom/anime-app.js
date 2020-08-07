var flash = $("#flash");

$(document).ready(function () {
  // FALSH MESSAGE
  if (flash.children().length) {
    anime({
      targets: "#flash",
      translateX: 515,
      easing: "easeOutElastic(2, 1)",
      duration: 900,
      delay: 7500,
      begin: function () {
        flash.show("fast");
      },
      complete: function () {
        flash.hide("fast");
      },
    });
  }

  // BTN COLLAPSE
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
