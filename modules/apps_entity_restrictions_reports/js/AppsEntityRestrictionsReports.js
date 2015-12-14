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

      // keep the current month. Will be used for pagination.
      var month_position = 0;

      var data = {
        labels: days[months[month_position]],
        series: [
          [6, 9, 1,2,2,10,6,3,4,5,6,7,7,8,4,2,4],
          [15, 1, 1,12,3,4,5,6,17,7,8,14,2,4,12,01,6],
          [5, 9, 1,2,3,4,5,6,7,7,8,4,2,4,2,10,6]
        ]
      };

      new Chartist.Line('.ct-chart', data, options);
    }
  };

})(jQuery);
