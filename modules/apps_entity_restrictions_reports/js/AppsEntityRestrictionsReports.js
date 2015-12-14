(function ($) {

  Drupal.behaviors.charts = {
    attach: function (context) {
      var options = {
        low: 0,
        showArea: true
      };

      // Keep this as reference.
      var data = {
        // A labels array that can contain any sort of values
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
        // Our series array that contains series objects or in this case series data arrays
        series: [
          [5, 2, 4, 2, 0, 5],
          [10, 2, 4, 2, 20]
        ]
      };

      new Chartist.Line('.ct-chart', data, options);
    }
  };

})(jQuery);
