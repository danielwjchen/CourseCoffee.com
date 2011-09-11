/**
 * @file
 * Load records from a imported table to coursecoffee_dev
 *
 * This is mostly a one-off, fix-up script
 */
INSERT subject (title, abbr)                                                                         
  SELECT                                                                                                              
		`COL 5`,
		REPLACE(REPLACE(`COL 6`, '"', ''), ' ', '')
	FROM um_import          
	GROUP BY `COL 5`, `COL 6`
;
INSERT course (subject_id, title, num)
	SELECT
		subject.id,
		`COL 9`,
		REPLACE(`COL 7`, ' ', '')
	FROM um_import
	INNNER JOIN subject
		ON subject.title = `COL 5`
	GROUP BY `COL 9`
;
INSERT section (`course_id`, `num`)
	SELECT
		crs.id,
		LPAD(REPLACE(um_import.`COL 8`, ' ', ''), 3, '0')
	FROM course crs
	INNER JOIN (um_import, subject sub)
		ON crs.subject_id = sub.id
		AND sub.title = um_import.`COL 5`
		AND crs.num = REPLACE(um_import.`COL 7`, ' ', '')
;
