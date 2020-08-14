$(document).ready(function () {
  var oDocs = [];

  $.fn.richTextEditor = function () {
    oDocs.push(document.getElementById($(this).attr("id")));
    initRichTextEditor();
  };

  function initRichTextEditor() {
    oDocs.forEach((oDoc) => {
      oDoc.addEventListener("paste", function (event) {
        event.preventDefault();
        formatDoc(
          oDoc,
          "inserttext",
          event.clipboardData.getData("text/plain")
        );
      });
      formatDoc(oDoc, "defaultParagraphSeparator", "p");
      if ($(oDoc).next(".rte.textarea").length == 0) {
        $(oDoc).after(`<div class="rte textarea">
        <div class="toolBar">
          <button class="btn" type="button" title="{{'gras'|trans({}, 'rte')}}" onclick="formatDoc(this, 'bold');">
            <i class="fas fa-bold" aria-hidden="true"></i>
          </button>
          <button class="btn" type="button" title="{{'italique'|trans({}, 'rte')}}" onclick="formatDoc(this, 'italic');">
            <i class="fas fa-italic" aria-hidden="true"></i>
          </button>
          <button class="btn" type="button" title="{{'souligne'|trans({}, 'rte')}}" onclick="formatDoc(this, 'underline');">
            <i class="fas fa-underline" aria-hidden="true"></i>
          </button>
          <button class="btn" type="button" title="{{'list'|trans({}, 'rte')}}" onclick="formatDoc(this, 'insertunorderedlist');">
            <i class="fas fa-list-ul" aria-hidden="true"></i>
          </button>
          <button class="btn" type="button" title="{{'colle'|trans({}, 'rte')}}" onclick="formatDoc(this, 'paste');">
            <i class="fas fa-paste" aria-hidden="true"></i>
          </button>
        </div>
        <div class="textBox" contenteditable="true"></div>
      </div>`);
        if (
          $(oDoc).val() != "" &&
          $(oDoc).next(".rte.textarea").find(".textBox").children().length == 0
        ) {
          $(oDoc).next(".rte.textarea").find(".textBox").append($(oDoc).val());
        }
        $(oDoc)
          .next(".rte.textarea")
          .find(".textBox")
          .keyup(function (e) {
            setTimeout(() => {
              $(oDoc).val($(this).html());
            }, 700);
          });
      }
    });
  }
});

function formatDoc(sDoc, sCmd, sValue) {
  document.execCommand(sCmd, false, sValue);
  sDoc.focus();
}
