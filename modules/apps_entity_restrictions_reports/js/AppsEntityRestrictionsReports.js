(function ($) {

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
      var months = charts.months;
      var days = charts.days;
      var hits = charts.hits;

      // keep the current month. Will be used for pagination.
      var month_position = 0;

      var data = {
        labels: days[months[month_position]],
        series: hits
      };

      new Chartist.Line('.ct-chart', data, options);
    }
  };

})(jQuery);
