(function ($) {

  /**
   * Initialise the first chart.
   */
  Drupal.behaviors.charts = {
    attach: function (context, settings) {
      var options = {
        // Ticks to be used to distribute across the axis length. As this axis type relies on the index of the value rather than the value, arbitrary data that can be converted to a string can be used as ticks.
        ticks: ['One', 'Two', 'Three'],
        // If set to true the full width will be used to distribute the values where the last value will be at the maximum of the axis length. If false the spaces between the ticks will be evenly distributed instead.
        stretch: true,
        low: 0,
        showArea: true,
        lineSmooth: Chartist.Interpolation.simple({
          divisor: 2
        }),
        axisY: {
          onlyInteger: true,
          offset: 20
        }
      };

      var charts = settings.chart;
      var days = charts.days;
      var hits = charts.hits;

      var data = {
        labels: days,
        series: hits
      };

      new Chartist.Line('.ct-chart', data, options);
    }
  };

  Drupal.behaviors.navigateBetweenCahrts = {
    attach: function(context, settings) {

      $('.ctools-dropdown-container a').click(function(event) {
        event.preventDefault();

        var elements = $(this).attr('href').split('/');
        var year = elements[elements.length - 1];
        var month = elements[elements.length - 2];

        $.ajax({
          type: 'GET',
          url: Drupal.settings.chart.basePath + '/' + month + '/' + year,
          dataType: 'json',
          success: function (matches) {
            console.log(matches);
          },
          error: function(data) {
            console.log(data);
          }
        });

        new Chartist.Line('.ct-chart', {
          labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
          series: [
            [12, 9, 7, 8, 5],
            [2, 1, 3.5, 7, 3],
            [1, 3, 4, 5, 6]
          ]
        }, {
          fullWidth: true,
          chartPadding: {
            right: 40
          }
        });

      });
    }
  };

})(jQuery);
