(function($) {
  "use strict";

  Drupal.behaviors.kifiFormViewCounter = {
    attach: function(context, settings) {
      $('.field--type-kifiform-view-counter', context).each(function(i, elem) {
        var url = elem.dataset.viewCounterPath;
        $.post(url).then(function(result) {
          if (result.status == "ok") {
            $(".field__item > span", elem).text(result.views);
          }
        });
      });
    }
  };
}(jQuery));
