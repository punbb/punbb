if (typeof FORUM === "undefined" || !FORUM) {
	var FORUM = {};
}

FORUM.message = function () {
	// Remove message if mouse is moved or key is pressed
	function bind_events() {
		jQuery(window)
			.mousemove(FORUM.message.hide)
			.click(FORUM.message.hide)
			.keypress(FORUM.message.hide);
	}

	// CREATE DOM
	function create_message_dom() {
		if (!$('#fancy_forum_message').length) {
			var _message_span = $(document.createElement('span')).attr('id', 'fancy_forum_message_content'),
				_wrap_bar = $(document.createElement('div')).attr('id', 'fancy_forum_message');

			_wrap_bar.css({"cursor": "pointer"});
			_wrap_bar.click(function (e) {
				FORUM.message.hide();
			});

			_wrap_bar.append(_message_span).hide().insertBefore($('#brd-wrap'));
		}
	}

	// CLEAR ALL MESSAGE TIMERS
	function clear_timeouts() {
		jQuery(document).stopTime('message_timer_1').stopTime('message_timer_2');
	}

	// UNBIND ALL MESSAGE EVENTS
	function unbind_events() {
		jQuery(window)
			.unbind('mousemove', FORUM.message.hide)
			.unbind('click', FORUM.message.hide)
			.unbind('keypress', FORUM.message.hide);
	}


	return {
		// HIDE MESSAGE
		hide: function () {
			// clear timers
			clear_timeouts();

			// unbind events
		 	unbind_events();

			// HIDE MESSAGE
			$('#fancy_forum_message').stop().hide();
		},

		// SHOW MESSAGE
		show: function (msg) {
			if (!msg || msg.length < 1) {
				return;
			}

			// clear timers
			clear_timeouts();

			unbind_events();

			msgClass = 'fancy_forum_message_info';

			// NEED CREATE
			create_message_dom();

			// stop animation and show our msg
			$("#fancy_forum_message_content")
				.html(msg)
				.removeClass('fancy_forum_message_error fancy_forum_message_warning fancy_forum_message_waiting fancy_forum_message_info')
				.addClass(msgClass);

			$("#fancy_forum_message").fadeIn("fast");

			FORUM.message.defferedClear();
		},


		//
		defferedClear: function() {
			// set mouse and keyboard
			$(document)
				.stopTime('message_timer_1')
				.oneTime(2500, 'message_timer_1', function () {
					bind_events();
				});

			// set just timeout gone
			$(document)
				.stopTime('message_timer_2')
				.oneTime(5000, 'message_timer_2', function () {
					FORUM.message.hide();
				});
		}
	};
}();
