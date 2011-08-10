/**
 * @file
 * Handle event inputs on /clas and lazy load contents to reduce load time
 *
 * @see js/model/task.js
 * @see js/model/calendar.js
 * @see js/model/doc.js
 *
 */
$P.ready(function() {
	panelMenu = $('.panel-menu');
	classBookList = $('#class-book-list');

	// cache book list to save time
	bookListCache = {};

	// mark the first item in class list as active on page load
	$('a:first', panelMenu).addClass('active');
	sectionId = $('a:first', panelMenu).attr('id');
	bookList = new BookSuggest('#class-book-list');
	bookList.getBookList(sectionId);
	


	// Load tasks
	task.init();
	var userTaskOption = $('#user-list-task-option');
	var agendaPanel = $('.panel-02 .panel-inner .task-list');

	// Over see inputs in panel menu
	panelMenu.delegate('a', 'click', function(e) {
		e.preventDefault();
		target = $(this);

		currentBookListId = $('.active', panelMenu).attr('id');
		// if the current book list is not cached already
		if (!bookListCache[currentBookListId]) {
			bookListCache[currentBookListId] = classBookList.html();
			classBookList.empty();
			
			// debug
			// console.log(currentBookListId);
		}

		selectedBookListId = target.attr('id');
		console.log(selectedBookListId);
		if (bookListCache[selectedBookListId]) {
			classBookList.empty();
			classBookList.html(bookListCache[selectedBookListId]);

			// debug
			// console.log('selected id');
			// console.log(bookListCache[selectedBookListId]);
		} else {
			classBookList.empty();
			bookList.getBookList(selectedBookListId);
			bookListCache[selectedBookListId] = classBookList.html();

			// debug
			// console.log('selected book list');
			// console.log(bookListCache[selectedBookListId]);
		}

		$('.option', panelMenu).removeClass('active');
		target.addClass('active');
		agendaPanel.empty();
	});

	// Initilize the date-time selector
	$('#time-picker').datetime({  
		duration: '15',  
		showTime: true,  
		constrainInput: false,  
		stepMinutes: 1,  
		stepHours: 1,  
		time24h: false  
	});  

	// toggle task creation form
	$('input.objective').live('click', function(e) {
		$('.additional').removeClass('hidden');
	});
	blurInput(body);

	// Over see inputs in task panel
	taskRegion = $('.panel-02');
	taskRegion.delegate('a', 'click', function(e) {
		e.preventDefault();
		target = $(this);

		if (target.hasClass('show-detail')) {
			if ($('.optional').hasClass('hidden')) {
				$('.optional').removeClass('hidden');
				target.text('less detail');
			} else {
				$('.optional').addClass('hidden');
				target.text('more detail');
			}

		} else if (target.hasClass('upload')) {
			doc.init();
		} else if (target.hasClass('submit')) {
			task.submit();
			setTimeout("calendar.populate()", 2000);

		}
	});

});
