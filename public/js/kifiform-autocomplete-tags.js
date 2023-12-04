(function($) {
  "use strict";

  var KEY_ENTER = 13;

  function append_value(input, value) {
    if (input.val().length > 0) {
      value = "," + value;
    }
    input.val(input.val() + value);
  }

  function append_tag(root, container, item) {
    var tag = $("<span>")
      .attr("class", "kifiform-tag")
      .attr("data-value", item.value)
      .text(item.label);

    if (!root.is(":disabled")) {
      $("<button>")
        .attr("type", "button")
        .attr("title", Drupal.t("Remove tag"))
        .text("X")
        .on("click", function(event) {
          tag.remove();
          container.trigger("kififormtagremove", {
            autocomplete: root,
            container: container[0],
            tag: tag[0],
            item: item
          });
        }).appendTo(tag);
    }

    container.append(tag);
    root.trigger("kififormtaginsert", {
      autocomplete: root,
      container: container[0],
      tag: tag,
      item: item
    });
  }

  function init_tags(root, proxy, container) {
    root.val().split(/,/).forEach(function(value) {
      if (value.length > 0) {
        var item = {
          value: value,
          label: value.substr(0, value.indexOf("(")).trim()
        };

        append_value(proxy, value);
        append_tag(root, container, item);
      }
    });

    root.val("");
  };

  Drupal.behaviors.kifiFormAutoCompleteTags = {
    attach: function(context, settings) {
      var elements = $(once("kifiform-autocomplete-tags", "input.form-autocomplete"))
        .each(function(i, _input) {
          var input = $(_input);

          var tags = $("<div/>")
            .attr("class", "kifiform-autocomplete-tags")
            .insertAfter(input);

          var proxy = $("<input/>")
            .attr("type", "hidden")
            .attr("name", _input.name)
            .insertAfter(input);

          init_tags(input, proxy, tags);

          input
            .on("autocompleteselect", function(event, ui) {
              ui.item.autocompleted = true;

              append_value(proxy, ui.item.value);
              append_tag(input, tags, ui.item)

              // FIXME: Hack to clear the text input... Could not do it any other way.
              setTimeout(function() {
                input.val("");
              });
            })
            .on("keypress", function(event) {
              if (event.keyCode == KEY_ENTER && this.value.length > 0) {
                event.preventDefault();

                append_value(proxy, this.value);
                append_tag(input, tags, {label: this.value, value: this.value});

                this.value = "";
              }
            });

          tags.on("kififormtagremove", function(event, ui) {
            var values = $.map(tags.children(), function(tag, i) {
              return tag.dataset.value;
            });

            proxy.val(values.join(","));
          });
        });

    }
  };
}(jQuery));
