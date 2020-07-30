$("button .show-form-command").click(function (e) {
  e.preventDefault();
  $(this).css("color", "#dcdc0a");
  $(".command-form").show();
});

var rel = $("form[name=command]");

rel.submit(function (e) {
  e.preventDefault();
  sendForm();
});

function sendForm() {
  var form = new FormData(rel.get(0));
  var xhr = new XMLHttpRequest();

  $(".command-submit").LoadingOverlay("show", {
    imageColor: "#dcdc0a",
  });

  xhr.onreadystatechange = function () {
    if ((xhr.readyState === 4) & (xhr.status === 201)) {
      $(".command-submit").LoadingOverlay("hide");
      $("button .show-form-command").css("color", "inherit");
      $(".command-form").hide();
      $(".command-submit").LoadingOverlay("show", {
        image: "",
        text: "La commande a été envoyé",
        textClass: "tit2",
        textResizeFactor: 0.3,
      });
    }
  };

  xhr.open("POST", rel.attr("action"));
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  xhr.send(form);
}