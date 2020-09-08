$(document).ready(function () {
  // MEAL COMMAND
  var form = $("form[name=cart]");

  form.submit(function (e) {
    e.preventDefault();
    checkRecaptcha(this, sendForm)
  });

  $("#cart_quantity").change(function (e) { 
    e.preventDefault();
    var meal = +$("input[name=meal_price]").val();
    var items = +$(this).val();
    var price = +meal * +items;
    var pricettc = +price;
    $("span.prices small").text("total : " + meal + " * " + items + " = " + pricettc + " XPF");
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
              text: "Ajouter au panier",
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
