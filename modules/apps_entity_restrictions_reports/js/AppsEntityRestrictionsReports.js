(function ($) {

  /**
   * Initialise the first chart.
   */
  Drupal.behaviors.charts = {
    attach: function (context, settings) {
      Drupal.behaviors.graph.generate(settings.chart.days, settings.chart.hits);
    }
  };

  Drupal.behaviors.navigateBetweenCahrts = {
    attach: function(context, settings) {

      $('.ctools-dropdown-container a').click(function(event) {
        event.preventDefault();
        $('.ctools-dropdown-container').hide();

        // Adding indication for loading results.
        $('.ctools-dropdown-link-wrapper a').after('<div class="ajax-progress ajax-progress-throbber"><div class="throbber"></div></div>');

        var elements = $(this).attr('href').split('/');
        var year = elements[elements.length - 1];
        var month = elements[elements.length - 2];

        $.ajax({
          type: 'GET',
          url: Drupal.settings.chart.basePath + '/' + month + '/' + year,
          dataType: 'json',
          success: function (matches) {
            Drupal.behaviors.graph.generate(matches.days, matches.hits);

            // Hide the pager.
            $('.ctools-dropdown-link-wrapper .ajax-progress').remove();
          },
          error: function(data) {
            console.log(data);
          }
        });
      });
    }
  };

  /**
   * Generating graph easily.
   */
  Drupal.behaviors.graph = {
    generate: function(days, hits) {
      var data = {
        labels: days,
        series: hits
      };

      var options = {
        fullWidth: true,
        chartPadding: {
          right: 50
        },
        stretch: true,
        showArea: true,
        lineSmooth: Chartist.Interpolation.simple({
          divisor: 2
        }),
        axisY: {
          onlyInteger: true
        }
      };

      new Chartist.Line('.ct-chart', data, options);
    }
  };

})(jQuery);
