<?php
/**
 * @file
 * Oversee the syllabus process from start to finish
 */
class SyllabusProcessFlowModel extends Model {
	public defineFlow() {
		return array(
			'uploadSyllabus',
			'selectSchoolAndClass',
		);
	}
}
