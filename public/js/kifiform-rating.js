(function($) {
  "use strict";

  Drupal.behaviors.kifiFormContentRating = {
    attach: function(context, settings) {
      $(once("kifiform-rating", ".field--type-kifiform-rating"))
        .each(function(i, _element) {
          var element = $(_element);
          var form = $(_element).find('.rating-form');
          form.find("button").on("click", function(event) {
            event.preventDefault();
            var data = {vote: event.currentTarget.value};

            $.post(form[0].dataset.url, data, function(response) {
              var votes = response.up + response.down;

              element.find('[data-bind=votes]')
                .text(Drupal.t('@votes votes', {'@votes': votes}));

              element.find('[data-bind=rating]')
                .animate({"width": response.value + "%"});

              element.find('[data-bind=form]').fadeOut();
            });
          });
        });
    }
  };
}(jQuery));
