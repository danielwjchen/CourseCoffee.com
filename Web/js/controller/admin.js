/**
 * @file
 * Interact with user to perform administrative tasks
 */
$P.ready(function() {
	var adminMenu = $('.admin-menu');
	var adminSyllabus = null;
	var adminUser     = null;
	var queuePaginate = $('.queue-menu-form input[name=paginate]');
	console.log(queuePaginate);
	switch ($('input[name=type]', adminMenu).val()) {
		case 'syllabus' :
			adminSyllabus = new AdminSyllabus('.queue-menu', '.queue-menu-form', '.queue-list');
			adminSyllabus.getList();
			break;
		case 'user':
			adminUser = new AdminUser('.queue-menu', '.queue-menu-form', '.queue-list');
			adminUser.getList();
			break;
	}
	
	var queueMoreButton = $('.panel-01 .more');

	/**
	 * Queue menu
	 */
	var queueMenu = $('.queue-menu');
	var resetQueueMenu = function() {
		$('li', queueMenu).removeClass('active');
		$('li:first', queueMenu).addClass('active');
	}
	adminMenu.delegate('a.option', 'click', function(e) {
		var target = $(this);
		var selected = target.attr('id');
		$('a.option', adminMenu).removeClass('active');
		queuePaginate.val(0);
		target.addClass('active');
		queueMoreButton.removeClass('disabled');
		if (selected == 'admin-option-syllabus') {
			adminSyllabus = new AdminSyllabus('.queue-menu', '.queue-menu-form', '.queue-list');
			adminSyllabus.resetMenu();
			adminSyllabus.getList();

		} else if(selected == 'admin-option-user') {
			adminUser = new AdminUser('.queue-menu', '.queue-menu-form', '.queue-list');
			adminUser.resetMenu();
			adminUser.getList();
		}
		
	});

	queueMoreButton.click(function(e) {
		e.preventDefault();
		queuePaginate.val(parseInt(queuePaginate.val()) +1);
		switch ($('input[name=type]', adminMenu).val()) {
			case 'syllabus' :
				adminSyllabus.updateList();
				break;
			case 'user':
				adminUser.updateList();
				break;
		}
	});
});
