$(document).ready(function () {
  // input file type

  //   FORM CREATE
  /** when btn new meal is click, get form from ajax one time */
  $("#modal-btn-new").click(function () {
    var btn = this;
    $("#mealModal").on("show.bs.modal", function () {
      $(this).find("h5").text($(btn).data("label"));
      var bodyModal = $(this).find(".modal-body");
      if (bodyModal.children().length == 0) {
        getCreateContentForm(btn, bodyModal, bindMealCreateForm);
      }
    });
  });

  //   FORM EDIT
  $(".modal-btn-edit").each(function (index, element) {
    $(element).click(function () {
      var btn = this;
      $("#mealModal").on("show.bs.modal", function () {
        $(this).find("h5").text($(btn).data("label"));
        var bodyModal = $(this).find(".modal-body");
        getEditContentForm(btn, bodyModal, bindMealEditForm);
      });
    });
  });

  // FORM DELETE
  var formsDelete = [$("form[name=meal-delete]")];

  formsDelete.forEach((form) => {
    form.submit(function (e1) {
      e1.preventDefault();
      var confirmBtn = form.data("btnDelete");
      var modal = form.data("modal");

      $(modal).modal("show");
      $(confirmBtn).click(function (e2) {
        e2.preventDefault();
        $(modal).find(".modal-content").LoadingOverlay("show", {
          imageColor: appColor2,
          background: "rgba(255, 255, 255, 0.6)",
        });
        e1.currentTarget.submit();
      });
    });
  });
});

function sendData(element) {
  var spinner = $(element).parents(".modal-content");
  spinner.LoadingOverlay("show", {
    imageColor: appColor2,
    background: "rgba(255, 255, 255, 0.6)",
  });
  var form = new FormData(element);
  var xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 400) {
        $(element).html(xhr.responseText);
        bsCustomFileInput.init();
      }
      if (xhr.status === 201) {
        location.href = xhr.responseText;
      }
      spinner.LoadingOverlay("hide");
    }
  };

  xhr.open($(element).attr("method"), $(element).attr("action"));
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  xhr.setRequestHeader("Accept", "text/html");
  xhr.send(form);
}

function checkRecaptcha(element, fn) {
  var recaptcha = $(element).find(".recaptcha-check");
  grecaptcha.ready(function () {
    grecaptcha
      .execute(recaptcha.data("sitekey"), { action: "submit" })
      .then(function (token) {
        recaptcha.val(token);
        fn(element);
      });
  });
}

function getCreateContentForm(btn, modal, fn) {
  modal.LoadingOverlay("show", {
    imageColor: appColor2,
    background: "rgba(255, 255, 255, 0.9)",
  });

  var xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        $(modal).html(xhr.responseText);
        bsCustomFileInput.init();
        fn();
      }
      modal.LoadingOverlay("hide");
    }
  };

  xhr.open("GET", $(btn).data("urlAction"));
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  xhr.setRequestHeader("Accept", "text/html");
  xhr.send();
}

function bindMealCreateForm() {
  $("form[name=meal-create]").submit(function (e) {
    e.preventDefault();
    checkRecaptcha(this, sendData);
  });
}

function getEditContentForm(btn, modal, fn) {
  modal.LoadingOverlay("show", {
    imageColor: appColor2,
    background: "rgba(255, 255, 255, 0.9)",
  });

  var xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        $(modal).html(xhr.responseText);
        bsCustomFileInput.init();
        fn($(modal).children());
      }
      modal.LoadingOverlay("hide");
    }
  };

  xhr.open("GET", $(btn).data("urlAction"));
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  xhr.setRequestHeader("Accept", "text/html");
  xhr.send();
}

function bindMealEditForm(element) {
  $(element).submit(function (e) {
    e.preventDefault();
    checkRecaptcha(this, sendData);
  });
}
