-- Smart School CBSE Examination DB
-- Version 3.0
-- https://smart-school.in
-- https://qdocs.net
-- Tables added: 26

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exams`
--

CREATE TABLE `cbse_exams` (
  `id` int(11) NOT NULL,
  `total_working_days` int(11) DEFAULT '0',
  `cbse_term_id` int(11) DEFAULT NULL,
  `cbse_exam_assessment_id` int(11) DEFAULT NULL,
  `cbse_exam_grade_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `exam_code` varchar(200) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `is_publish` int(1) NOT NULL,
  `is_active` int(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `use_exam_roll_no` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_assessments`
--

CREATE TABLE `cbse_exam_assessments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_assessment_types`
--

CREATE TABLE `cbse_exam_assessment_types` (
  `id` int(11) NOT NULL,
  `cbse_exam_assessment_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `maximum_marks` float NOT NULL,
  `pass_percentage` float NOT NULL,
  `description` mediumtext NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_class_sections`
--

CREATE TABLE `cbse_exam_class_sections` (
  `id` int(11) NOT NULL,
  `cbse_exam_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_grades`
--

CREATE TABLE `cbse_exam_grades` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_grades_range`
--

CREATE TABLE `cbse_exam_grades_range` (
  `id` int(11) NOT NULL,
  `cbse_exam_grade_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `minimum_percentage` float NOT NULL,
  `maximum_percentage` float NOT NULL,
  `description` mediumtext NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_observations`
--

CREATE TABLE `cbse_exam_observations` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_students`
--

CREATE TABLE `cbse_exam_students` (
  `id` int(11) NOT NULL,
  `cbse_exam_id` int(11) NOT NULL,
  `student_session_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `roll_no` varchar(20) DEFAULT NULL,
  `remark` text,
  `total_present_days` int(11) DEFAULT NULL,
  `delete_student_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_student_subject_rank`
--

CREATE TABLE `cbse_exam_student_subject_rank` (
  `id` int(11) NOT NULL,
  `cbse_template_id` int(11) DEFAULT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `rank_percentage` float(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_timetable`
--

CREATE TABLE `cbse_exam_timetable` (
  `id` int(11) NOT NULL,
  `cbse_exam_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `time_from` time NOT NULL,
  `time_to` time NOT NULL,
  `duration` int(11) NOT NULL,
  `room_no` varchar(255) NOT NULL,
  `is_written` int(1) NOT NULL DEFAULT '1',
  `written_maximum_marks` float NOT NULL,
  `is_practical` int(1) NOT NULL,
  `practical_maximum_mark` float DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_marksheet_type`
--

CREATE TABLE `cbse_marksheet_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `cbse_marksheet_type` (`id`, `name`, `short_code`) VALUES
(1, 'All Terms', 'all_term'),
(2, 'Term Wise', 'term_wise'),
(3, 'Single Exam Without Term', 'exam_wise'),
(4, 'Multiple Exams Without Term', 'without_term');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_observation_class_section`
--

CREATE TABLE `cbse_observation_class_section` (
  `id` int(11) NOT NULL,
  `cbse_observation_parameter_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_observation_parameters`
--

CREATE TABLE `cbse_observation_parameters` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_observation_subparameter`
--

CREATE TABLE `cbse_observation_subparameter` (
  `id` int(11) NOT NULL,
  `cbse_exam_observation_id` int(11) NOT NULL,
  `cbse_observation_parameter_id` int(11) NOT NULL,
  `maximum_marks` float NOT NULL,
  `description` mediumtext,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_observation_terms`
--

CREATE TABLE `cbse_observation_terms` (
  `id` int(11) NOT NULL,
  `cbse_exam_observation_id` int(11) NOT NULL,
  `cbse_term_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_observation_term_student_subparameter`
--

CREATE TABLE `cbse_observation_term_student_subparameter` (
  `id` int(11) NOT NULL,
  `cbse_ovservation_term_id` int(11) DEFAULT NULL,
  `cbse_observation_subparameter_id` int(11) DEFAULT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `obtain_marks` float(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_student_exam_ranks`
--

CREATE TABLE `cbse_student_exam_ranks` (
  `id` int(11) NOT NULL,
  `cbse_exam_id` int(11) NOT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `rank_percentage` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;	
				
-- --------------------------------------------------------

--
-- Table structure for table `cbse_student_subject_marks`
--

CREATE TABLE `cbse_student_subject_marks` (
  `id` int(11) NOT NULL,
  `cbse_exam_timetable_id` int(11) DEFAULT NULL,
  `cbse_exam_student_id` int(11) DEFAULT NULL,
  `cbse_exam_assessment_type_id` int(11) DEFAULT NULL,
  `is_absent` int(1) NOT NULL DEFAULT '0',
  `marks` float(10,2) DEFAULT '0.00',
  `note` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_student_subject_result`
--

CREATE TABLE `cbse_student_subject_result` (
  `id` int(11) NOT NULL,
  `cbse_exam_timetable_id` int(11) DEFAULT NULL,
  `cbse_exam_student_id` int(11) DEFAULT NULL,
  `note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_student_template_rank`
--

CREATE TABLE `cbse_student_template_rank` (
  `id` int(11) NOT NULL,
  `cbse_template_id` int(11) DEFAULT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `rank` int(20) DEFAULT NULL,
  `rank_percentage` float(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;										  

-- --------------------------------------------------------

--
-- Table structure for table `cbse_template`
--

CREATE TABLE `cbse_template` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `orientation` varchar(1) NOT NULL DEFAULT 'P',
  `description` varchar(255) NOT NULL,
  `gradeexam_id` int(11) DEFAULT NULL,
  `remarkexam_id` int(11) DEFAULT NULL,
  `is_weightage` varchar(10) NOT NULL,
  `marksheet_type` varchar(50) NOT NULL,
  `created_by` int(11) NOT NULL,
  `header_image` varbinary(500) DEFAULT NULL,
  `title` text,
  `left_logo` varchar(200) DEFAULT NULL,
  `right_logo` varchar(200) DEFAULT NULL,
  `exam_name` varchar(200) DEFAULT NULL,
  `school_name` varchar(200) DEFAULT NULL,
  `exam_center` varchar(200) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `left_sign` varchar(200) DEFAULT NULL,
  `middle_sign` varchar(200) DEFAULT NULL,
  `right_sign` varchar(200) DEFAULT NULL,
  `background_img` varchar(200) DEFAULT NULL,
  `content` text,
  `content_footer` text,
  `date` date DEFAULT NULL,
  `is_name` int(1) DEFAULT '1',
  `is_father_name` int(1) DEFAULT '1',
  `is_mother_name` int(1) DEFAULT '1',
  `exam_session` int(1) DEFAULT '1',
  `is_admission_no` int(1) DEFAULT '1',
  `is_division` int(1) NOT NULL DEFAULT '1',
  `is_roll_no` int(1) DEFAULT '1',
  `is_photo` int(1) DEFAULT '1',
  `is_class` int(1) NOT NULL DEFAULT '0',
  `is_section` int(1) NOT NULL DEFAULT '0',
  `is_dob` int(1) DEFAULT '1',
  `is_remark` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_template_class_sections`
--

CREATE TABLE `cbse_template_class_sections` (
  `id` int(11) NOT NULL,
  `cbse_template_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_template_terms`
--

CREATE TABLE `cbse_template_terms` (
  `id` int(11) NOT NULL,
  `cbse_template_id` int(11) NOT NULL,
  `cbse_term_id` int(11) NOT NULL,
  `weightage` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_template_term_exams`
--

CREATE TABLE `cbse_template_term_exams` (
  `id` int(11) NOT NULL,
  `cbse_template_term_id` int(11) DEFAULT NULL,
  `cbse_exam_id` int(11) NOT NULL,
  `cbse_template_id` int(11) NOT NULL,
  `weightage` float NOT NULL DEFAULT '100',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_terms`
--

CREATE TABLE `cbse_terms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `term_code` varchar(100) NOT NULL,
  `description` mediumtext NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--
INSERT INTO `notification_setting` (`id`, `type`, `is_mail`, `is_sms`, `is_notification`, `display_notification`, `display_sms`, `is_student_recipient`, `is_guardian_recipient`, `is_staff_recipient`, `display_student_recipient`, `display_guardian_recipient`, `display_staff_recipient`, `subject`, `template_id`, `template`, `variables`, `created_at`) VALUES
(null, 'cbse_email_pdf_exam_marksheet', '1', '1', 1, 1, 1, 1, 1, NULL, 1, 1, NULL, 'CBSE Exam Marksheet PDF ( {{student_name}} - {{admission_no}} )', '', 'Dear {{student_name}} ({{admission_no}}) {{class}} Section {{section}}. We have mailed you the marksheet with Roll no.{{roll_no}}', '{{student_name}} {{class}} {{section}} {{admission_no}} {{roll_no}}', '2023-06-21 07:59:44'),
(null, 'cbse_exam_result', '1', '1', 1, 1, 1, 1, 1, NULL, 1, 1, NULL, 'CBSE Exam Result', '', 'Dear {{student_name}} - {{roll_no}}, your {{exam}} result has been published.', '{{student_name}} {{roll_no}} {{exam}}', '2023-06-21 07:59:47');

INSERT INTO `permission_group` (`id`, `name`, `short_code`, `is_active`, `system`, `created_at`) VALUES (900, 'CBSE Examination', 'cbseexam', 1, 0, '2023-05-25 12:04:56');

INSERT INTO `permission_student` (`id`, `name`, `short_code`, `system`, `student`, `parent`, `group_id`, `created_at`) VALUES
(900, 'CBSE Examination', 'cbseexam', 0, 1, 1, 900, '2023-05-25 12:07:15');

INSERT INTO `permission_category` (`id`, `perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`, `created_at`) VALUES
(9001, 900, 'CBSE Exam', 'cbse_exam', 1, 1, 1, 1, '2022-11-03 08:58:30'),
(9002, 900, 'CBSE Exam Schedule', 'cbse_exam_schedule', 1, 0, 0, 0, '2023-05-09 11:01:34'),
(9003, 900, 'CBSE Exam Assign / View Student', 'cbse_exam_assign_view_student', 1, 0, 1, 0, '2022-11-03 09:18:15'),
(9004, 900, 'CBSE Exam Subjects', 'cbse_exam_subjects', 1, 0, 1, 0, '2022-11-04 08:01:41'),
(9005, 900, 'CBSE Exam Marks', 'cbse_exam_marks', 1, 0, 1, 0, '2022-11-03 09:18:24'),
(9006, 900, 'CBSE Exam Attendance', 'cbse_exam_attendance', 1, 0, 1, 0, '2022-11-03 09:18:28'),
(9007, 900, 'CBSE Exam Teacher Remark', 'cbse_exam_teacher_remark', 1, 0, 1, 0, '2022-11-03 09:18:32'),
(9008, 900, 'CBSE Exam Print Marksheet', 'cbse_exam_print_marksheet', 1, 0, 0, 0, '2022-11-03 09:18:43'),
(9009, 900, 'CBSE Exam Grade', 'cbse_exam_grade', 1, 1, 1, 1, '2022-11-03 09:18:46'),
(9010, 900, 'CBSE Exam Assign Observation', 'cbse_exam_assign_observation', 1, 1, 1, 1, '2023-05-08 12:33:23'),
(9011, 900, 'CBSE Exam Observation', 'cbse_exam_observation', 1, 1, 1, 1, '2023-05-09 10:57:16'),
(9012, 900, 'CBSE Exam Observation Parameter', 'cbse_exam_observation_parameter', 1, 1, 1, 1, '2023-05-09 11:01:54'),
(9013, 900, 'CBSE Exam Assessment', 'cbse_exam_assessment', 1, 1, 1, 1, '2023-05-09 11:01:51'),
(9014, 900, 'CBSE Exam Term', 'cbse_exam_term', 1, 1, 1, 1, '2023-05-09 11:01:47'),
(9015, 900, 'CBSE Exam Template', 'cbse_exam_template', 1, 1, 1, 1, '2023-05-09 11:01:43'),
(9016, 900, 'CBSE Exam Link Exam', 'cbse_exam_link_exam', 1, 0, 0, 0, '2023-05-09 11:01:40'),
(9017, 900, 'CBSE Exam Subject Marks Report', 'subject_marks_report', 1, 0, 0, 0, '2023-05-09 11:01:38'),
(9018, 900, 'CBSE Exam Template Marks Report', 'template_marks_report', 1, 0, 0, 0, '2023-05-09 11:01:34'),
(9019, 900, 'CBSE Exam Setting', 'cbse_exam_setting', 1, 0, 0, 0, '2023-07-03 05:24:57'),
(9020, 900, 'CBSE Exam Generate Rank', 'cbse_exam_generate_rank', 1, 0, 0, 0, '2023-07-03 05:24:57');


INSERT INTO `sidebar_menus` (`id`, `product_name`, `permission_group_id`, `icon`, `menu`, `activate_menu`, `lang_key`, `system_level`, `level`, `sidebar_display`, `access_permissions`, `is_active`, `created_at`) VALUES
(34, 'sscbse', 900, 'fa fa-file-text-o', 'CBSE Examination', 'cbse_exam', 'cbse_exam', 69, 11, 1, '(\'subject_marks_report\', \'can_view\') || (\'template_marks_report\', \'can_view\') || (\'cbse_exam\', \'can_view\') ||  (\'cbse_exam_print_marksheet\', \'can_view\') ||  (\'cbse_exam_grade\', \'can_view\') ||  (\'cbse_exam_assign_observation\', \'can_view\') || (\'cbse_exam_observation\', \'can_view\') || (\'cbse_exam_observation_parameter\', \'can_view\') || (\'cbse_exam_assessment\', \'can_view\') || (\'cbse_exam_term\', \'can_view\') || (\'cbse_exam_template\', \'can_view\') || (\'cbse_exam_schedule\', \'can_view\') ', 0, '2023-07-04 13:03:29');


INSERT INTO `sidebar_sub_menus` (`id`, `sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`, `created_at`) VALUES
(201, 34, 'exam', NULL, 'exam', 'cbseexam/exam', 1, '(\'cbse_exam\', \'can_view\')', NULL, 'exam', 'index,examwiserank', 'sscbse', 1, '2023-07-04 09:57:01'),
(202, 34, 'exam_schedule', NULL, 'exam_schedule', 'cbseexam/exam/examtimetable', 1, '(\'cbse_exam_schedule\', \'can_view\')', NULL, 'exam', 'examtimetable', 'sscbse', 1, '2023-07-04 13:01:15'),
(203, 34, 'print_marksheet', NULL, 'print_marksheet', 'cbseexam/result/marksheet', 1, '(\'cbse_exam_print_marksheet\', \'can_view\')', NULL, 'result', 'marksheet', 'sscbse', 1, '2023-05-25 05:23:50'),
(204, 34, 'exam_grade', NULL, 'exam_grade', 'cbseexam/grade/gradelist', 1, '(\'cbse_exam_grade\', \'can_view\')', NULL, 'grade', 'gradelist', 'sscbse', 1, '2023-07-05 07:17:24'),
(205, 34, 'assign_observation', NULL, 'assign_observation', 'cbseexam/observation/assign', 1, '(\'cbse_exam_assign_observation\', \'can_view\')', NULL, 'observation', 'assign', 'sscbse', 1, '2023-05-25 05:31:49'),
(206, 34, 'observation', NULL, 'observation', 'cbseexam/observation', 1, '(\'cbse_exam_observation\', \'can_view\')', NULL, 'observation', 'index', 'sscbse', 1, '2023-07-05 07:17:37'),
(207, 34, 'observation_parameter', NULL, 'observation_parameter', 'cbseexam/observationparameter', 1, '(\'cbse_exam_observation_parameter\', \'can_view\')', NULL, 'observationparameter', 'index,edit', 'sscbse', 1, '2023-06-30 07:39:42'),
(208, 34, 'assessment', NULL, 'assessment', 'cbseexam/assessment', 1, '(\'cbse_exam_assessment\', \'can_view\')', NULL, 'assessment', 'index', 'sscbse', 1, '2023-05-25 05:34:19'),
(209, 34, 'term', NULL, 'term', 'cbseexam/term', 1, '(\'cbse_exam_term\', \'can_view\')', NULL, 'term', 'index', 'sscbse', 1, '2023-05-25 05:34:53'),
(210, 34, 'template', NULL, 'template', 'cbseexam/template', 1, '(\'cbse_exam_template\', \'can_view\')', NULL, 'template', 'index,templatewiserank', 'sscbse', 1, '2023-07-04 09:57:06'),
(211, 34, 'reports', NULL, 'reports', 'cbseexam/report/index', 1, '(\'subject_marks_report\', \'can_view\') || (\'template_marks_report\', \'can_view\')', NULL, 'report', 'index,templatewise,examsubject', 'sscbse', 1, '2023-07-01 05:22:34'),
(212, 34, 'setting', NULL, 'setting', 'cbseexam/setting/index', 1, '(\'cbse_exam_setting\', \'can_view\')', NULL, 'setting', 'index', '', 1, '2023-07-03 05:26:03');

--
-- Indexes for table `cbse_exams`
--
ALTER TABLE `cbse_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_term_id` (`cbse_term_id`),
  ADD KEY `cbse_exam_grade_id` (`cbse_exam_grade_id`),
  ADD KEY `cbse_exam_assessment_id` (`cbse_exam_assessment_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `cbse_exam_assessments`
--
ALTER TABLE `cbse_exam_assessments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbse_exam_assessment_types`
--
ALTER TABLE `cbse_exam_assessment_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_assessment_id` (`cbse_exam_assessment_id`);

--
-- Indexes for table `cbse_exam_class_sections`
--
ALTER TABLE `cbse_exam_class_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_section_id` (`class_section_id`),
  ADD KEY `cbse_exam_id` (`cbse_exam_id`);

--
-- Indexes for table `cbse_exam_grades`
--
ALTER TABLE `cbse_exam_grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbse_exam_grades_range`
--
ALTER TABLE `cbse_exam_grades_range`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_grade_id` (`cbse_exam_grade_id`);

--
-- Indexes for table `cbse_exam_observations`
--
ALTER TABLE `cbse_exam_observations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbse_exam_students`
--
ALTER TABLE `cbse_exam_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_id` (`cbse_exam_id`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `cbse_exam_student_subject_rank`
--
ALTER TABLE `cbse_exam_student_subject_rank`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_template_id` (`cbse_template_id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `subject_id` (`subject_id`);				  
--
-- Indexes for table `cbse_exam_timetable`
--
ALTER TABLE `cbse_exam_timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_id` (`cbse_exam_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `cbse_marksheet_type`
--
ALTER TABLE `cbse_marksheet_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbse_observation_class_section`
--
ALTER TABLE `cbse_observation_class_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbse_observation_parameters`
--
ALTER TABLE `cbse_observation_parameters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbse_observation_subparameter`
--
ALTER TABLE `cbse_observation_subparameter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_observation_parameter_id_ibfk_1` (`cbse_observation_parameter_id`),
  ADD KEY `cbse_exam_observation_id_ibfk_1` (`cbse_exam_observation_id`);

--
-- Indexes for table `cbse_observation_terms`
--
ALTER TABLE `cbse_observation_terms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_term_id` (`cbse_term_id`),
  ADD KEY `cbse_ovservation_terms_ibfk_3` (`session_id`),
  ADD KEY `cbse_exam_observations_ibfk_1` (`cbse_exam_observation_id`);

--
-- Indexes for table `cbse_observation_term_student_subparameter`
--
ALTER TABLE `cbse_observation_term_student_subparameter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_observation_term_student_subparameter_ibfk_1` (`cbse_ovservation_term_id`),
  ADD KEY `cbse_observation_subparameter_id` (`cbse_observation_subparameter_id`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `cbse_student_exam_ranks`
--
ALTER TABLE `cbse_student_exam_ranks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_id` (`cbse_exam_id`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `cbse_student_subject_marks`
--
ALTER TABLE `cbse_student_subject_marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_timetable_id` (`cbse_exam_timetable_id`),
  ADD KEY `cbse_exam_student_id` (`cbse_exam_student_id`),
  ADD KEY `cbse_exam_assessment_type_id` (`cbse_exam_assessment_type_id`);

--
-- Indexes for table `cbse_student_subject_result`
--
ALTER TABLE `cbse_student_subject_result`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbse_student_template_rank`
--
ALTER TABLE `cbse_student_template_rank`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `cbse_template_id` (`cbse_template_id`);						  

--
-- Indexes for table `cbse_template`
--
ALTER TABLE `cbse_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_template_ibfk_3` (`session_id`),
  ADD KEY `cbse_template_ibfk_1` (`gradeexam_id`),
  ADD KEY `cbse_template_ibfk_2` (`remarkexam_id`);

--
-- Indexes for table `cbse_template_class_sections`
--
ALTER TABLE `cbse_template_class_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_template_id` (`cbse_template_id`),
  ADD KEY `class_section_id` (`class_section_id`);

--
-- Indexes for table `cbse_template_terms`
--
ALTER TABLE `cbse_template_terms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_template_id` (`cbse_template_id`),
  ADD KEY `cbse_term_id` (`cbse_term_id`);

--
-- Indexes for table `cbse_template_term_exams`
--
ALTER TABLE `cbse_template_term_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_template_term_id` (`cbse_template_term_id`),
  ADD KEY `cbse_template_term_exams_ibfk_3` (`cbse_exam_id`),
  ADD KEY `cbse_template_term_exams_ibfk_4` (`cbse_template_id`);

--
-- Indexes for table `cbse_terms`
--
ALTER TABLE `cbse_terms`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `cbse_exams`
--
ALTER TABLE `cbse_exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_exam_assessments`
--
ALTER TABLE `cbse_exam_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_exam_assessment_types`
--
ALTER TABLE `cbse_exam_assessment_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_exam_class_sections`
--
ALTER TABLE `cbse_exam_class_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_exam_grades`
--
ALTER TABLE `cbse_exam_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_exam_grades_range`
--
ALTER TABLE `cbse_exam_grades_range`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_exam_observations`
--
ALTER TABLE `cbse_exam_observations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_exam_students`
--
ALTER TABLE `cbse_exam_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cbse_exam_student_subject_rank`
--  
ALTER TABLE `cbse_exam_student_subject_rank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_exam_timetable`
--
ALTER TABLE `cbse_exam_timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_marksheet_type`
--
ALTER TABLE `cbse_marksheet_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_observation_class_section`
--
ALTER TABLE `cbse_observation_class_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_observation_parameters`
--
ALTER TABLE `cbse_observation_parameters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_observation_subparameter`
--
ALTER TABLE `cbse_observation_subparameter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_observation_terms`
--
ALTER TABLE `cbse_observation_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_observation_term_student_subparameter`
--
ALTER TABLE `cbse_observation_term_student_subparameter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  
--
-- AUTO_INCREMENT for table `cbse_student_exam_ranks`
--  
ALTER TABLE `cbse_student_exam_ranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;													 

--
-- AUTO_INCREMENT for table `cbse_student_subject_marks`
--
ALTER TABLE `cbse_student_subject_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_student_subject_result`
--
ALTER TABLE `cbse_student_subject_result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  
--
-- AUTO_INCREMENT for table `cbse_student_template_rank`
--
ALTER TABLE `cbse_student_template_rank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;                                                                  

--
-- AUTO_INCREMENT for table `cbse_template`
--
ALTER TABLE `cbse_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_template_class_sections`
--
ALTER TABLE `cbse_template_class_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_template_terms`
--
ALTER TABLE `cbse_template_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_template_term_exams`
--
ALTER TABLE `cbse_template_term_exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_terms`
--
ALTER TABLE `cbse_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `cbse_marksheet_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
  
--
-- Constraints for dumped tables
--

--
-- Constraints for table `cbse_exams`
--
ALTER TABLE `cbse_exams`
  ADD CONSTRAINT `cbse_exams_ibfk_1` FOREIGN KEY (`cbse_term_id`) REFERENCES `cbse_terms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exams_ibfk_2` FOREIGN KEY (`cbse_exam_grade_id`) REFERENCES `cbse_exam_grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exams_ibfk_3` FOREIGN KEY (`cbse_exam_assessment_id`) REFERENCES `cbse_exam_assessments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exams_ibfk_4` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_assessment_types`
--
ALTER TABLE `cbse_exam_assessment_types`
  ADD CONSTRAINT `cbse_exam_assessment_types_ibfk_1` FOREIGN KEY (`cbse_exam_assessment_id`) REFERENCES `cbse_exam_assessments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_class_sections`
--
ALTER TABLE `cbse_exam_class_sections`
  ADD CONSTRAINT `cbse_exam_class_sections_ibfk_1` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_class_sections_ibfk_2` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_grades_range`
--
ALTER TABLE `cbse_exam_grades_range`
  ADD CONSTRAINT `cbse_exam_grades_range_ibfk_1` FOREIGN KEY (`cbse_exam_grade_id`) REFERENCES `cbse_exam_grades` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_students`
--
ALTER TABLE `cbse_exam_students`
  ADD CONSTRAINT `cbse_exam_students_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_students_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_student_subject_rank`
--
ALTER TABLE `cbse_exam_student_subject_rank`
  ADD CONSTRAINT `cbse_exam_student_subject_rank_ibfk_1` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_student_subject_rank_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_student_subject_rank_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
  
--
-- Constraints for table `cbse_exam_timetable`
--
ALTER TABLE `cbse_exam_timetable`
  ADD CONSTRAINT `cbse_exam_timetable_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_timetable_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_observation_subparameter`
--
ALTER TABLE `cbse_observation_subparameter`
  ADD CONSTRAINT `cbse_exam_observation_id_ibfk_1` FOREIGN KEY (`cbse_exam_observation_id`) REFERENCES `cbse_exam_observations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_observation_parameter_id_ibfk_1` FOREIGN KEY (`cbse_observation_parameter_id`) REFERENCES `cbse_observation_parameters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_observation_terms`
--
ALTER TABLE `cbse_observation_terms`
  ADD CONSTRAINT `cbse_exam_observations_ibfk_1` FOREIGN KEY (`cbse_exam_observation_id`) REFERENCES `cbse_exam_observations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_observation_terms_ibfk_2` FOREIGN KEY (`cbse_term_id`) REFERENCES `cbse_terms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_observation_terms_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_observation_term_student_subparameter`
--
ALTER TABLE `cbse_observation_term_student_subparameter`
  ADD CONSTRAINT `cbse_observation_term_student_subparameter_ibfk_1` FOREIGN KEY (`cbse_ovservation_term_id`) REFERENCES `cbse_observation_terms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_observation_term_student_subparameter_ibfk_2` FOREIGN KEY (`cbse_observation_subparameter_id`) REFERENCES `cbse_observation_subparameter` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_observation_term_student_subparameter_ibfk_3` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_student_exam_ranks`
--
ALTER TABLE `cbse_student_exam_ranks`
  ADD CONSTRAINT `cbse_student_exam_ranks_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_student_exam_ranks_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;
                                                                                                                                          
                                                                                                                                                  
--
-- Constraints for table `cbse_student_subject_marks`
--
ALTER TABLE `cbse_student_subject_marks`
  ADD CONSTRAINT `cbse_student_subject_marks_ibfk_1` FOREIGN KEY (`cbse_exam_timetable_id`) REFERENCES `cbse_exam_timetable` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_student_subject_marks_ibfk_2` FOREIGN KEY (`cbse_exam_student_id`) REFERENCES `cbse_exam_students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_student_subject_marks_ibfk_3` FOREIGN KEY (`cbse_exam_assessment_type_id`) REFERENCES `cbse_exam_assessment_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_student_template_rank`
--
ALTER TABLE `cbse_student_template_rank`
  ADD CONSTRAINT `cbse_student_template_rank_ibfk_1` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_student_template_rank_ibfk_2` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE;

--                                                                                                                                         
-- Constraints for table `cbse_template`
--
ALTER TABLE `cbse_template`
  ADD CONSTRAINT `cbse_template_ibfk_1` FOREIGN KEY (`gradeexam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cbse_template_ibfk_2` FOREIGN KEY (`remarkexam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cbse_template_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_template_class_sections`
--
ALTER TABLE `cbse_template_class_sections`
  ADD CONSTRAINT `cbse_template_class_sections_ibfk_1` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_template_class_sections_ibfk_2` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_template_terms`
--
ALTER TABLE `cbse_template_terms`
  ADD CONSTRAINT `cbse_template_terms_ibfk_1` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_template_terms_ibfk_2` FOREIGN KEY (`cbse_term_id`) REFERENCES `cbse_terms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_template_term_exams`
--
ALTER TABLE `cbse_template_term_exams`
  ADD CONSTRAINT `cbse_template_term_exams_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_template_term_exams_ibfk_2` FOREIGN KEY (`cbse_template_term_id`) REFERENCES `cbse_template_terms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_template_term_exams_ibfk_4` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE;
  
  
CREATE TABLE `cbse_exam_timetable_assessment_types` (
  `id` int(11) NOT NULL,
  `cbse_exam_timetable_id` int(11) DEFAULT NULL,
  `cbse_exam_assessment_type_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE cbse_student_subject_marks
  ADD `cbse_exam_timetable_assessment_type_id` int(11) NOT NULL after id;
  
 
ALTER TABLE `cbse_exams`
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_exam_code` (`exam_code`);


ALTER TABLE `cbse_exam_assessments`
  ADD KEY `idx_name` (`name`);


ALTER TABLE `cbse_exam_assessment_types`
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_code` (`code`);


ALTER TABLE `cbse_exam_grades`
  ADD KEY `idx_name` (`name`);


ALTER TABLE `cbse_exam_grades_range`
  ADD KEY `idx_name` (`name`);


ALTER TABLE `cbse_exam_observations`
  ADD KEY `idx_name` (`name`);

 
ALTER TABLE `cbse_exam_student_subject_rank`
  ADD KEY `idx_rank` (`rank`);
  
 
ALTER TABLE `cbse_exam_timetable_assessment_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_timetable_id` (`cbse_exam_timetable_id`),
  ADD KEY `cbse_exam_assessment_type_id` (`cbse_exam_assessment_type_id`);


ALTER TABLE `cbse_marksheet_type`
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_short_code` (`short_code`);


ALTER TABLE `cbse_observation_parameters`
  ADD KEY `idx_name` (`name`);


ALTER TABLE `cbse_student_exam_ranks`
  ADD KEY `idx_rank` (`rank`),
  ADD KEY `idx_rank_percentage` (`rank_percentage`);


ALTER TABLE `cbse_student_subject_marks`
  ADD KEY `cbse_exam_timetable_assessment_type_ibfk_4` (`cbse_exam_timetable_assessment_type_id`);
  
  
ALTER TABLE `cbse_student_template_rank`
  ADD KEY `idx_rank` (`rank`),
  ADD KEY `idx_rank_percentage` (`rank_percentage`);


ALTER TABLE `cbse_template`
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_marksheet_type` (`marksheet_type`),
  ADD KEY `idx_exam_name` (`exam_name`),
  ADD KEY `idx_school_name` (`school_name`);


ALTER TABLE `cbse_template_terms`
  ADD KEY `idx_weightage` (`weightage`);


ALTER TABLE `cbse_template_term_exams`
  ADD KEY `idx_weightage` (`weightage`);


ALTER TABLE `cbse_terms`
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_term_code` (`term_code`);
  

ALTER TABLE `cbse_exam_timetable_assessment_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `cbse_exam_timetable_assessment_types`
  ADD CONSTRAINT `cbse_exam_timetable_assessment_types_ibfk_1` FOREIGN KEY (`cbse_exam_timetable_id`) REFERENCES `cbse_exam_timetable` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_timetable_assessment_types_ibfk_2` FOREIGN KEY (`cbse_exam_assessment_type_id`) REFERENCES `cbse_exam_assessment_types` (`id`) ON DELETE CASCADE;


ALTER TABLE `cbse_student_subject_marks`
  ADD CONSTRAINT `cbse_exam_timetable_assessment_type_ibfk_4` FOREIGN KEY (`cbse_exam_timetable_assessment_type_id`) REFERENCES `cbse_exam_timetable_assessment_types` (`id`) ON DELETE CASCADE;  
