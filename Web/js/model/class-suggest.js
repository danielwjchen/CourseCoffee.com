/**
 * @file
 * Oversee class suggestion
 *
 * This supposed to be an abstract class
 */
window.ClassSuggest = function(formName, inputName, callback) {
	var form  = $(formName);
	var input = $(inputName, form);
	form.append('<div id="hint-message" class="message hidden">' +
		'<strong>Please enter section number.</strong>' +
	'</div>');
	var _message = $('#hint-message');

	/**
	 * Class suggest
	 */
	input.autocomplete({
		source: function(request, response) {
			$.ajax({
				url: '/college-class-suggest',
				type: 'post',
				data: 'term=' + request.term + '&' + form.serialize(),
				success: function(data) {
					if (data.error) {
						return;
					}

					// we get a specific item, this is a hack and someone needs to fix it
					if (data.message) {
					}

					var list = null;
					if (data.success) {
						list = data.list;
					}

					if (list['subject_abbr'] != undefined) {
						fixedData  = {
							0 : {
								'institution' : list['institution'],
								'subject_abbr' : list['subject_abbr'],
								'course_num' : list['course_num'],
								'section_num' : list['section_num'],
								'course_title' : list['course_title'],
								'section_id' : list['section_id']
							}
						}
						response( $.map(fixedData, function(item) {
							return {
								courseCode : item['subject_abbr'] + ' ' + item['course_num'] + ' ' + item['section_num'],
								institution : item['institution'],
								title: item['course_title'],
								section_id : item['section_id'],
								value: item['subject_abbr'] + ' ' + item['course_num'] + ' ' + item['section_num']
							}
						}));
					} else {
						response( $.map(list, function(item) {
							if (item['section_num'] != undefined) {
								return {
									courseCode : item['subject_abbr'] + ' ' + item['course_num'] + ' ' + item['section_num'],
									institution : item['institution'],
									title: item['course_title'],
									section_id : item['section_id'],
									value: item['subject_abbr'] + ' ' + item['course_num'] + ' ' + item['section_num']
								}
							} else {
								return {
									courseCode : item['subject_abbr'] + ' ' + item['course_num'],
									institution : item['institution'],
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
				_message.remove();
				callback(ui.item.section_id);
			}
		},
		open: function(event, ui) {
		},
		close: function(event, ui) {
			if ($('input[name=section_id]', form).val() == '') {
				$(_message).removeClass('hidden');

				return ;
			}

		}
	}).data("autocomplete")._renderItem = function(ul, item) {
		return $( "<li></li>" )
			.data( "item.autocomplete", item )
			.append('<a href="#">' +
				'<span class="course-code">' + item.courseCode + '</span>' +
				'<span class="course-title">' + item.title + '</span>' + 
				'<span class="institution">' + item.institution + '</span>' + 
			'</a>')
			.appendTo( ul );
	};

}
