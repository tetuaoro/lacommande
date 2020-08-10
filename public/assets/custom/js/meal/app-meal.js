$(document).ready(function () {

  // MEAL COMMAND
  var rel = $("form[name=command]");

  rel.submit(function (e) {
    e.preventDefault();
    sendForm();
  });

  function sendForm() {
    var form = new FormData(rel.get(0));
    var xhr = new XMLHttpRequest();

    $(".command-submit").LoadingOverlay("show", {
      imageColor: appColor1,
    });

    xhr.onreadystatechange = function () {
      if ((xhr.readyState === 4) & (xhr.status === 201)) {
        $(".command-submit").LoadingOverlay("hide");
        $("button .show-form-command").css("color", "inherit");
        $(".command-form").hide();
        setTimeout(() => {
          $(".command-submit").LoadingOverlay("show", {
            image: "",
            text: "La commande a été envoyé",
            textClass: "tit2 t-center",
            textResizeFactor: 0.3,
          });
        }, 1450);
        setTimeout(() => {
          $(".command-submit").LoadingOverlay("hide");
        }, 4570);
      }
    };

    xhr.open("POST", rel.attr("action"));
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(form);
  }
});
