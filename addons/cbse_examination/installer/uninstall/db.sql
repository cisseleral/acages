-- Smart School CBSE Examination Uninstall DB
-- Version 3.0
-- https://smart-school.in
-- https://qdocs.net 
 
DROP TABLE IF EXISTS `cbse_exam_student_subject_rank`;
DROP TABLE IF EXISTS `cbse_observation_class_section`;
DROP TABLE IF EXISTS `cbse_observation_term_student_subparameter`;
DROP TABLE IF EXISTS `cbse_observation_terms`;
DROP TABLE IF EXISTS `cbse_observation_subparameter`;
DROP TABLE IF EXISTS `cbse_student_subject_marks`;
DROP TABLE IF EXISTS `cbse_student_subject_result`;
DROP TABLE IF EXISTS `cbse_student_template_rank`;
DROP TABLE IF EXISTS `cbse_template_class_sections`;
DROP TABLE IF EXISTS `cbse_exam_timetable_assessment_types`;
DROP TABLE IF EXISTS `cbse_exam_class_sections`;
DROP TABLE IF EXISTS `cbse_exam_students`;
DROP TABLE IF EXISTS `cbse_exam_timetable`;
DROP TABLE IF EXISTS `cbse_student_exam_ranks`;
DROP TABLE IF EXISTS `cbse_template_term_exams`;
DROP TABLE IF EXISTS `cbse_template_terms`;
DROP TABLE IF EXISTS `cbse_template`;
DROP TABLE IF EXISTS `cbse_exams`;
DROP TABLE IF EXISTS `cbse_exam_grades_range`;
DROP TABLE IF EXISTS `cbse_exam_grades`;
DROP TABLE IF EXISTS `cbse_terms`;
DROP TABLE IF EXISTS `cbse_observation_parameters`;
DROP TABLE IF EXISTS `cbse_exam_observations`;
DROP TABLE IF EXISTS `cbse_exam_assessment_types`;
DROP TABLE IF EXISTS `cbse_exam_assessments`;
DROP TABLE IF EXISTS `cbse_marksheet_type`;

-- --------------------------------------------------------

DELETE FROM `notification_setting` WHERE TYPE='cbse_email_pdf_exam_marksheet';
DELETE FROM `notification_setting` WHERE TYPE='cbse_exam_result';

DELETE FROM `permission_group` WHERE ID='900';

DELETE FROM `permission_student` WHERE ID='900';

DELETE FROM `permission_category` WHERE ID='9001';
DELETE FROM `permission_category` WHERE ID='9002';
DELETE FROM `permission_category` WHERE ID='9003';
DELETE FROM `permission_category` WHERE ID='9004';
DELETE FROM `permission_category` WHERE ID='9005';
DELETE FROM `permission_category` WHERE ID='9006';
DELETE FROM `permission_category` WHERE ID='9007';
DELETE FROM `permission_category` WHERE ID='9008';
DELETE FROM `permission_category` WHERE ID='9009';
DELETE FROM `permission_category` WHERE ID='9010';
DELETE FROM `permission_category` WHERE ID='9011';
DELETE FROM `permission_category` WHERE ID='9012';
DELETE FROM `permission_category` WHERE ID='9013';
DELETE FROM `permission_category` WHERE ID='9014';
DELETE FROM `permission_category` WHERE ID='9015';
DELETE FROM `permission_category` WHERE ID='9016';
DELETE FROM `permission_category` WHERE ID='9017';
DELETE FROM `permission_category` WHERE ID='9018';
DELETE FROM `permission_category` WHERE ID='9019';
DELETE FROM `permission_category` WHERE ID='9020';

DELETE FROM `sidebar_menus` WHERE ID=34;

DELETE FROM `sidebar_sub_menus` WHERE ID=201;
DELETE FROM `sidebar_sub_menus` WHERE ID=202;
DELETE FROM `sidebar_sub_menus` WHERE ID=203;
DELETE FROM `sidebar_sub_menus` WHERE ID=204;
DELETE FROM `sidebar_sub_menus` WHERE ID=205;
DELETE FROM `sidebar_sub_menus` WHERE ID=206;
DELETE FROM `sidebar_sub_menus` WHERE ID=207;
DELETE FROM `sidebar_sub_menus` WHERE ID=208;
DELETE FROM `sidebar_sub_menus` WHERE ID=209;
DELETE FROM `sidebar_sub_menus` WHERE ID=210;
DELETE FROM `sidebar_sub_menus` WHERE ID=211;
DELETE FROM `sidebar_sub_menus` WHERE ID=212;

update `addons` set current_version =NULL, installation_by=NULL WHERE product_id=47443722;
