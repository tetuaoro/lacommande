$(document).ready(function () {
  //   FORM MEAL CREATE/EDIT
  $("[id^='mealModal']").on("show.bs.modal", function () {
    bindMealForm($(this).find("form"));
    bsCustomFileInput.init();
    $(this).find("[id$='description']").richTextEditor();
    $(this).find("[id$='recipe']").richTextEditor();
    $($(this).find(".multiCollapseMeal")).collapse("hide");
    $(this).find(".tags-input").select2({
      tags: true,
    });
  });

  //   FORM MENU CREATE/EDIT
  $("[id^='menuModal']").on("show.bs.modal", function () {
    var form = $(this).find("form");
    bindMenuForm(form);

    if (form.next().length > 0) {
      form.html(form.nextAll());
      form.nextAll().remove();
    }

    $(this).find("#menu_meals").select2();
    $(this).find(".category-input").select2({
      tags: true,
    });
  });

  // FORM DELETE
  $("form[name=meal-delete], form[name=menu-delete]").submit(function (e1) {
    e1.preventDefault();
    $("#deleteModal").on("show.bs.modal", function () {
      $(this).find("h5").text($(e1.currentTarget).data("textTitleModal"));
    });
    $("#deleteModal").modal("show");
    $("#meal-btn-delete-confirm").click(function (e2) {
      e2.preventDefault();
      e1.currentTarget.submit();
    });
  });
});

/**
 * Send data form via ajax.
 * @param {Element} element
 */
function sendMealData(element) {
  var form = new FormData(element);
  var xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 400) {
        $(element).html(xhr.responseText);
        bsCustomFileInput.init();
        $(element).find("[id$='description']").richTextEditor();
        $(element).find("[id$='recipe']").richTextEditor();
        $(element).find(".tags-input").select2({
          tags: true,
        });
      }
      if (xhr.status === 201) {
        location.href = xhr.responseText;
      }
      spinner("hide");
    }
  };

  xhr.open($(element).attr("method"), $(element).attr("action"));
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  xhr.setRequestHeader("Accept", "text/html");
  xhr.send(form);
}

/**
 * Send data form via ajax.
 * @param {Element} element
 */
function sendMenuData(element) {
  var form = new FormData(element);
  var xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 400) {
        $(element).html(xhr.responseText);
        $(element).find("#menu_meals").select2();
        $(element).find(".category-input").select2({
          tags: true,
        });
      }
      if (xhr.status === 201) {
        location.href = xhr.responseText;
      }
      spinner("hide");
    }
  };

  xhr.open($(element).attr("method"), $(element).attr("action"));
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  xhr.setRequestHeader("Accept", "text/html");
  xhr.send(form);
}

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

/**
 * Bind all form when the button clicked.
 * @param {Element|null} element
 */
function bindMenuForm(element) {
  $(element).submit(function (e) {
    e.preventDefault();
    spinner();
    checkRecaptcha(this, sendMenuData);
  });
}

/**
 * Bind all form when the button clicked.
 * @param {Element|null} element
 */
function bindMealForm(element) {
  $(element).submit(function (e) {
    e.preventDefault();
    spinner();
    checkRecaptcha(this, sendMealData);
  });
}

/**
 * Load spinner
 *
 * @param {null|string} mode
 * @param {number} [alpha=0.6]
 */
function spinner(mode, alpha = 0.6) {
  var spinner = $(".modal-content");
  if (mode == null || mode == "show") {
    spinner.LoadingOverlay("show", {
      imageColor: appColor2,
      background: "rgba(255, 255, 255, " + alpha + ")",
    });
  } else if (mode == "hide") {
    spinner.LoadingOverlay("hide");
  }
}

/**
 * Bootstrap tags input
 *
 * @param {string} element
 */
/* function tagInput(element, url, namejs) {
  if ($(element).next(".bootstrap-tagsinput").length == 0) {
    var tagnames = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.obj.whitespace("name"),
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      prefetch: url,
    });
    tagnames.initialize();

    $(element).tagsinput({
      typeaheadjs: {
        name: namejs,
        displayKey: "name",
        valueKey: "name",
        source: tagnames.ttAdapter(),
      },
    });
    $(element).on("itemAddedOnInit", function (event) {
      // event.item: contains the item
      setTimeout(() => {
        $(".bootstrap-tagsinput :input").val("");
      }, 1);
    });
  }
} */
