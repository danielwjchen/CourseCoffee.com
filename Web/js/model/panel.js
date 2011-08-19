/**
 * @file
 * Organize infromation into panels
 */
window.CommentPanel = function() {

	/**
	 * Add a Facebook comment panel
	 * 
	 * @param id
	 *  the primary key that identifies a subject
	 * @param url
	 *  the url that directs to the subject
	 */
	$.fn.panelizeFbComments = function(id, url) {
		this.html('<fb:comments href="' + url + '" num_posts="10" width="459"></fb:comments>');

		// weird hack I have to put in... because it complains it's not defiend 
		// but still runs the code
		if (window.FB !== undefined) {
			FB.XFBML.parse(document.getElementById(id));
		}

		return this;
	};

	/**
	 * Slide panel to the right
	 */
	var panelCollapse = function () {
		$('.panel-03', $P).remove();
		$('.content', $P).animate({marginLeft: '0px'}, 400, function() {
			$('.panel-03', $P).remove();
			$('a.fb-comment', $P).html('comments &#187;');
		});
	}

	/**
	 * Slide panel to the left
	 */
	var panelExpand = function (id, url) {
		var thread = 'thread-' + id;
		var content = $('.content', $P);
		content.animate({marginLeft: '-479px', marginRight: '-960px'}, 400, function() {
			content.after('<div class="panel-03" id="'+ thread + '">' + 
				'<div class="panel-inner"></div>' + 
				'<a href="#" id="remove-panel"><span class="hidden">remove</span></a>' +
			'</div>');
			$('.panel-03 > .panel-inner', $P).panelizeFbComments(thread, url);
			$('.panel-03', $P).delegate('a#remove-panel', 'click', function(e) {
				e.preventDefault();
				panelCollapse();
			});
		});


	}

	/**
	if ($P('.node-type-course').exists()) {
		var nid = null;
		var url = null;
		$P('.node-type-course').each(function(index) {
			nid = $P(this).attr('id').replace('node-', '');
			url = $P('a', this).attr('href').replace('?q=', '');
			$P('a', this).remove();
			$P(this).after('<div class="course-comment" />');
			$P('.course-comment').panelizeFbComments(nid, url);
		});
	}
	*/

	$('.task-list', $P).delegate('a.fb-comment', 'click', function(e) {
		e.preventDefault();
		var target = $(this);
		$('a.fb-comment', $P).html('comments &#187;');
		var panel03 = $('.panel-03', $P);
		var task_id = target.attr('id');
		var task_url = window.location.href + '/' + task_id;
		if (panel03.length != 0) {
			if (panel03.attr('id') == 'thread-' + task_id) {
				panelCollapse();
			} else {
				var panel_inner = $('.panel-inner', panel03);
				var thread = 'thread-' + task_id;
				target.html('comments &#171;');
				panel03.attr('id', thread);
				panel_inner.empty().panelizeFbComments(thread, task_url);
			}

		} else {
			target.html('comments &#171;');
			panelExpand(task_id, task_url);

		}
		return false;
	});
};
