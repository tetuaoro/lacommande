$(document).ready(function () {
  // MEAL COMMAND
  var form = $("form[name=command]");

  form.submit(function (e) {
    e.preventDefault();
    checkRecaptcha(this, sendForm)
  });

  $("#command_items").change(function (e) { 
    e.preventDefault();
    var base = +$("input[name=lacommand_price]").val();
    var meal = +$("input[name=meal_price]").val();
    var items = +$(this).val();
    var price = +meal * +items;
    var pricettc = +price + +base;
    $("span.prices small").text("total : (" + meal + "*" + items + ") + " + base + " = " + pricettc + " XPF");
  });

/**
 * Check before recaptcha test.
 * @param {Elemet} element
 * @param {CallableFunction} fn
 */
function checkRecaptcha(element, fn) {
  var recaptcha = $(element).find(".recaptcha-check");
  grecaptcha.ready(function () {
    grecaptcha
      .execute(recaptcha.data("sitekey"), { action: "submit" })
      .then(function (token) {
        recaptcha.val(token);
        fn(element);
      })
      .catch(function () {
        spinner("hide");
      });
  });
}

  function sendForm(element) {
    var form = new FormData(element);
    var xhr = new XMLHttpRequest();

    $(".command-submit").LoadingOverlay("show", {
      imageColor: appColor1,
    });

    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4) {
        $(".command-submit").LoadingOverlay("hide");
        if (xhr.status == 201) {
          $("button .show-form-command").css("color", "inherit");
          $("#collapseCommand").collapse("hide");
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
        if (xhr.status == 400) {
          form.html(xhr.responseText);
        }
      }
    };

    xhr.open($(element).attr("method"), $(element).attr("action"));
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(form);
  }
});
