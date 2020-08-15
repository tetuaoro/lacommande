$(document).ready(function () {
  // MEAL COMMAND
  var form = $("form[name=command]");

  form.submit(function (e) {
    e.preventDefault();
    sendForm(this);
  });

  function sendForm(element) {
    var form = new FormData(element);
    var xhr = new XMLHttpRequest();

    $(".command-submit").LoadingOverlay("show", {
      imageColor: appColor1,
    });

    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4) {
        $(".command-submit").LoadingOverlay("hide");
        if (xhr.status === 201) {
          $("button .show-form-command").css("color", "inherit");
          $("#collapseCommand").collapse("hide");
          setTimeout(() => {
            $(".command-submit").LoadingOverlay("show", {
              image: "",
              text: "La commande a été envoyé",
              textClass: "tit2 t-center",
              textResizeFactor: 0.3,
            });
            $(".tcommand .tcommand-count").text(
              parseInt($(".tcommand .tcommand-count").text()) + 1
            );
          }, 1450);
        }
        if (xhr.status === 400) {
          form.html(xhr.responseText);
        }
        setTimeout(() => {
          $(".command-submit").LoadingOverlay("hide");
        }, 4570);
      }
    };

    xhr.open($(element).attr("method"), $(element).attr("action"));
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(form);
  }
});
