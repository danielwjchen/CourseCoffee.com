/**
 * @file
 * Handle access to class information
 */
window.ClassInfo = function(regionName, optionName) {
	var region = $(regionName);
	var option = $(optionName);
	var cache  = {};
	var defaultData = option.serializeArray();
	//console.log(defaultData);
	if (defaultData.length != 0) {
		data = {};
		for (i in defaultData) {
			key = defaultData[i].name.replace('-', '_');
			data[key] = defaultData[i].value;
		}

		cache[data['section_id']] = data;
	}

	/**
	 * Set option values to the data recieved
	 *
	 * @param object data
	 */
	var setClassOption = function(data) {
		$('input[name=institution-id]', option).val(data.institution_id);
		$('input[name=institution-uri]', option).val(data.institution_uri);
		$('input[name=institution]', option).val(data.institution);
		$('input[name=year-id]', option).val(data.year_id);
		$('input[name=year]', option).val(data.year);
		$('input[name=term]', option).val(data.term_id);
		$('input[name=term-id]', option).val(data.term);
		$('input[name=subject-id]', option).val(data.subject_id);
		$('input[name=subject-abbr]', option).val(data.subject_abbr);
		$('input[name=course-id]', option).val(data.course_id);
		$('input[name=course-title]', option).val(data.course_title);
		$('input[name=course-description]', option).val(data.course_description);
		$('input[name=course-num]', option).val(data.course_num);
		$('input[name=section-id]', option).val(data.section_id);
		$('input[name=section-num]', option).val(data.section_num);
	};
	
	/**
	 * Display class information stored in option
	 */
	var displayClassInfo = function() {
		content = '<h3 class="course-title">' + $('input[name=course-title]', option).val() + '</h3>';
		courseInfo = $('input[name=course-description]', option).val();
		content += courseInfo != undefined ? '<p>' + courseInfo + '</p>' : '';
		region.html(content);
	}

	/**
	 * Get information for a class from server
	 *
	 * @param int sectionId
	 */
	this.getClassInfo = function(sectionId) {
		if (cache[sectionId]) {
			// debug
			console.log(cache);
			setClassOption(cache[sectionId]);
			displayClassInfo();

		} else {
			$.ajax({
				url: '/college-class-info',
				type: 'post',
				cache: true,
				data: 'section_id=' + sectionId,
				success: function(response) {
					if (response.content) {
						cache[response.content.section_id] = response.content;
						setClassOption(response.content);
						displayClassInfo();
					}
				}
			});
		}
	}

	/**
	 * Get the section id of the class storeed in option
	 */
	this.getClassId = function() {
		return $('input[name=section-id]', option).val();
	}
}
