var flash = $("#flash");

$(document).ready(function () {
  if (flash.children().length) {
    console.log("has children");

    anime({
      delay: 2000,
      duration: 10000,
      begin: function () {
        flash.show("fast");
      },
      complete: function () {
        flash.hide("fast");
      },
    });
  }
});
