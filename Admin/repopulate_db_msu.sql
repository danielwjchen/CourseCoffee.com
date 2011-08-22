/**
 * @file
 * Load records from msucoursemash_production to coursecoffee_dev
 *
 * This is mostly a one-off, fix-up script
 */
INSERT INTO coursecoffee_dev.institution (name, uri, domain)
	SELECT 
		msucoursemash_production.institution.name,
		msucoursemash_production.institution.uri,
		msucoursemash_production.institution.domain
	FROM msucoursemash_production.institution
;

INSERT coursecoffee_dev.institution_term (institution_id, year_id, name, begin_timestamp, end_timestamp)
	SELECT 
		msucoursemash_production.institution_term.institution_id,
		msucoursemash_production.institution_term.year_id,
		msucoursemash_production.institution_term.name,
		msucoursemash_production.institution_term.begin_timestamp,
		msucoursemash_production.institution_term.end_timestamp
	FROM msucoursemash_production.institution_term
;

INSERT coursecoffee_dev.institution_year (period)
	SELECT 
		msucoursemash_production.institution_year.period
	FROM msucoursemash_production.institution_year
;

INSERT coursecoffee_dev.institution_year_linkage (institution_id, year_id)
	SELECT 
		msucoursemash_production.institution_year_linkage.institution_id,
		msucoursemash_production.institution_year_linkage.year_id
	FROM msucoursemash_production.institution_year_linkage
;

INSERT coursecoffee_dev.subject (title, abbr)
	SELECT
		msucoursemash_production.subject.title,
		msucoursemash_production.subject.abbr
	FROM msucoursemash_production.subject
;

INSERT coursecoffee_dev.subject_term_linkage (term_id, subject_id)
	SELECT
		msucoursemash_production.subject_term_linkage.term_id,
		msucoursemash_production.subject_term_linkage.subject_id
	FROM msucoursemash_production.subject_term_linkage
;

INSERT INTO coursecoffee_dev.course (subject_id, num, title, description)
	SELECT
		msucoursemash_production.course.subject_id,
		msucoursemash_production.course.num,
		msucoursemash_production.node.title,
		msucoursemash_production.node_revisions.body
	FROM msucoursemash_production.course
	INNER JOIN msucoursemash_production.node
		ON msucoursemash_production.course.nid = msucoursemash_production.node.nid
	INNER JOIN msucoursemash_production.node_revisions
		ON msucoursemash_production.course.nid = msucoursemash_production.node_revisions.nid
;

INSERT INTO coursecoffee_dev.section (course_id, num, credit)
	SELECT
		msucoursemash_production.section.course_vid,
		msucoursemash_production.section.num,
		msucoursemash_production.section.credit
	FROM msucoursemash_production.section
;
