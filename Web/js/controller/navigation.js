/**
 * @file
 * Oversee user inputs on navigation area
 */
$P.ready(function() {
	window.navigation = $('#navigation-menu');
	classSuggest = new ClassSuggest('#class-suggest-form');

	/**
	 * Class suggest
	 */
	$('#suggest-input').autocomplete({
		source: function(request, response) {
			$.ajax({
				url: '/college-class-suggest',
				type: 'post',
				data: 'term=' + request.term,
				success: function(data) {
					// we get a specific item, this is a hack and someone needs to fix it
					if (data['subject_abbr'] != undefined) {
						fixedData  = {
							0 : {
								'subject_abbr' : data['subject_abbr'],
								'course_num' : data['course_num'],
								'section_num' : data['section_num'],
								'course_title' : data['course_title'],
								'section_id' : data['section_id']
							}
						}
						response( $.map(fixedData, function(item) {
							return {
								courseCode : item['subject_abbr'] + ' ' + item['course_num'] + ' ' + item['section_num'],
								title: item['course_title'],
								section_id : item['section_id'],
								value: item['subject_abbr'] + ' ' + item['course_num'] + ' ' + item['section_num']
							}
						}));
					} else {
						response( $.map(data, function(item) {
							if (item['section_num'] != undefined) {
								return {
									courseCode : item['subject_abbr'] + ' ' + item['course_num'] + ' ' + item['section_num'],
									title: item['course_title'],
									section_id : item['section_id'],
									value: item['subject_abbr'] + ' ' + item['course_num'] + ' ' + item['section_num']
								}
							} else {
								return {
									courseCode : item['subject_abbr'] + ' ' + item['course_num'],
									title: item['course_title'],
									value: item['subject_abbr'] + ' ' + item['course_num']
								}
							}
						}));
					}
				}
			})
		},
		select: function(event, ui) {
			if (ui.item.section_id != undefined) {
				$('#section-id', navigation).val(ui.item.section_id);
				classSuggest.enroll();
			}
		}
	}).data("autocomplete")._renderItem = function(ul, item) {
		return $( "<li></li>" )
			.data( "item.autocomplete", item )
			.append('<a href="#">' +
				'<span class="course-code">' + item.courseCode + '</span>' +
				'<span class="course-title">' + item.title+ '</span>' + 
			'</a>')
			.appendTo( ul );
	};


	// hide/show the default input
	blurInput('#user-login-form');
	blurInput('#class-suggest-form');

	navigation.delegate('a.button', 'click', function(e) {
		var target = $(this);
		if (target.hasClass('login')) {
			e.preventDefault();
			login.submit();
		} else if (target.hasClass('suggest')) {
			classSuggest.enroll();

		} else if (target.hasClass('logout')) {
			e.preventDefault();
			logout.submit();
		}
	});
	
});
