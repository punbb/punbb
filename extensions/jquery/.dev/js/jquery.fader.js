jQuery.fn.extend({
	fader: function(interval, callback) {
		return this.each(function() {
			$(this).fadeTo(interval, 0.01, function() {
				if (jQuery.isFunction(callback)) {
					callback();
				}
				$(this).fadeTo(interval, 1.0);
			});
		});
	}
});
