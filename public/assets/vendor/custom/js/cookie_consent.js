document.addEventListener("DOMContentLoaded", function () {
  var cookieConsent = document.querySelector(".ch-cookie-consent");
  var cookieConsentForm = document.querySelector(".ch-cookie-consent__form");
  var cookieConsentFormBtn = document.querySelectorAll(
    ".ch-cookie-consent__btn"
  );
  var cookieConsentCategoryDetails = document.querySelector(
    ".ch-cookie-consent__category-group"
  );
  var cookieConsentCategoryDetailsToggle = document.querySelector(
    ".ch-cookie-consent__toggle-details"
  );

  // If cookie consent is direct child of body, assume it should be placed on top of the site pushing down the rest of the website
  if (cookieConsent && cookieConsent.parentNode.nodeName === "BODY") {
    if (cookieConsent.classList.contains("ch-cookie-consent--top")) {
      document.body.style.marginTop = cookieConsent.offsetHeight + "px";

      cookieConsent.style.position = "absolute";
      cookieConsent.style.top = "0";
      cookieConsent.style.left = "0";
    } else {
      document.body.style.marginBottom = cookieConsent.offsetHeight + "px";

      cookieConsent.style.position = "fixed";
      cookieConsent.style.bottom = "0";
      cookieConsent.style.left = "0";
    }
  }

  if (cookieConsentForm) {
    // Submit form via ajax
    cookieConsentFormBtn.forEach(function (btn) {
      btn.addEventListener(
        "click",
        function (event) {
          event.preventDefault();

          var xhr = new XMLHttpRequest();
          xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
              cookieConsent.style.display = "none";
            }
          };
          xhr.open("POST", cookieConsentForm.action);
          xhr.setRequestHeader(
            "Content-Type",
            "application/x-www-form-urlencoded"
          );
          xhr.send(serializeForm(cookieConsentForm, event.target));
        },
        false
      );
    });
  }

  if (cookieConsentCategoryDetails && cookieConsentCategoryDetailsToggle) {
    cookieConsentCategoryDetailsToggle.addEventListener("click", function () {
      var detailsIsHidden =
        cookieConsentCategoryDetails.style.display !== "block";
      cookieConsentCategoryDetails.style.display = detailsIsHidden
        ? "block"
        : "none";
      cookieConsentCategoryDetailsToggle.querySelector(
        ".ch-cookie-consent__toggle-details-hide"
      ).style.display = detailsIsHidden ? "block" : "none";
      cookieConsentCategoryDetailsToggle.querySelector(
        ".ch-cookie-consent__toggle-details-show"
      ).style.display = detailsIsHidden ? "none" : "block";
    });
  }
});

function serializeForm(form, clickedButton) {
  var serialized = [];

  for (var i = 0; i < form.elements.length; i++) {
    var field = form.elements[i];

    if (
      (field.type !== "checkbox" &&
        field.type !== "radio" &&
        field.type !== "button") ||
      field.checked
    ) {
      serialized.push(
        encodeURIComponent(field.name) + "=" + encodeURIComponent(field.value)
      );
    }
  }

  serialized.push(encodeURIComponent(clickedButton.getAttribute("name")) + "=");

  return serialized.join("&");
}

window.addEventListener("load", function (event) {
  setTimeout(() => {
    $(".ch-cookie-consent").show("slow");
  }, 2000);

  $(".btn-accept").click(function () {
    $(".ch-cookie-consent").fadeOut(
      "slow",
      "swing",
      $(".ch-cookie-consent-body").css("margin-top", 0)
    );
  });
});
