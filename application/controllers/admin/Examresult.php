<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Examresult extends Admin_Controller
{

    public $exam_type = array();

    public function __construct()
    {
        parent::__construct();
        $this->exam_type          = $this->config->item('exam_type');
        $this->attendence_exam    = $this->config->item('attendence_exam');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->model(array('marksdivision_model', 'marksdivision_model'));
        $this->load->library('mailsmsconf');
         $this->load->library('media_storage');
    }

    public function printCard()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('admitcard_template', $this->lang->line('template'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('post_exam_id', $this->lang->line('exam'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('post_exam_group_id', $this->lang->line('exam_group'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('exam_group_class_batch_exam_student_id[]', $this->lang->line('students'), 'required|trim|xss_clean');
        $data = array();

        if ($this->form_validation->run() == false) {
            $data = array(
                'admitcard_template'                     => form_error('admitcard_template'),
                'post_exam_id'                           => form_error('post_exam_id'),
                'post_exam_group_id'                     => form_error('post_exam_group_id'),
                'exam_group_class_batch_exam_student_id' => form_error('exam_group_class_batch_exam_student_id'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {
            $post_exam_id            = $this->input->post('post_exam_id');
            $post_exam_group_id      = $this->input->post('post_exam_group_id');
            $students_array          = $this->input->post('exam_group_class_batch_exam_student_id');
            $exam                    = $this->examgroup_model->getExamByID($post_exam_id);
            $data['exam']            = $exam;
            $exam_grades             = $this->grade_model->getByExamType($exam->exam_group_type);
            $data['exam_grades']     = $exam_grades;
            $data['admitcard']       = $this->admitcard_model->get($this->input->post('admitcard_template'));
            $data['exam_subjects']   = $this->batchsubject_model->getExamSubjects($post_exam_id);
            $data['student_details'] = $this->examstudent_model->getStudentsAdmitCardByExamAndStudentID($students_array, $post_exam_id);
            $data['sch_setting']     = $this->sch_setting_detail;
            $student_admit_cards     = $this->load->view('admin/admitcard/_printadmitcard', $data, true);
            $array                   = array('status' => '1', 'error' => '', 'page' => $student_admit_cards);
            echo json_encode($array);
        }
    }

    public function admitcard()
    {
        if (!$this->rbac->hasPrivilege('print_admit_card', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Examinations');
        $this->session->set_userdata('sub_menu', 'Examinations/examresult/admitcard');
        $examgroup_result      = $this->examgroup_model->get();
        $data['examgrouplist'] = $examgroup_result;
    
        $class                 = $this->class_model->get();
        $data['title']         = 'Add Batch';
        $data['title_list']    = 'Recent Batch';
        $data['examType']      = $this->exam_type;
        $data['classlist']     = $class;
        $session               = $this->session_model->get();
        $data['sessionlist']   = $session;
        $data['get_active_admitcard']  = $this->admitcard_model->get_active_admitcard();

        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('session_id', $this->lang->line('session'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_group_id', $this->lang->line('exam_group'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

        } else {
            $exam_group_id              = $this->input->post('exam_group_id');
            $exam_id                    = $this->input->post('exam_id');
            $session_id                 = $this->input->post('session_id');
            $class_id                   = $this->input->post('class_id');
            $section_id                 = $this->input->post('section_id');
            $data['studentList'] = $this->examgroupstudent_model->searchExamStudents($exam_group_id, $exam_id, $class_id, $section_id, $session_id);
            $data['examList'] = $this->examgroup_model->getExamByExamGroup($exam_group_id, true);
            $data['exam_id']       = $exam_id;
            $data['exam_group_id'] = $exam_group_id;
        }
        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/examresult/admitcard', $data);
        $this->load->view('layout/footer', $data);
    }

    public function marksheet()
    {

        if (!$this->rbac->hasPrivilege('print_marksheet', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Examinations');
        $this->session->set_userdata('sub_menu', 'Examinations/examresult/marksheet');

        $examgroup_result      = $this->examgroup_model->get();
        $data['examgrouplist'] = $examgroup_result;
        $marksheet_result      = $this->marksheet_model->get();
        $data['marksheetlist'] = $marksheet_result;
        $class                 = $this->class_model->get();
        $data['title']         = 'Add Batch';
        $data['title_list']    = 'Recent Batch';
        $data['examType']      = $this->exam_type;
        $data['classlist']     = $class;
        $session               = $this->session_model->get();
        $data['sessionlist']   = $session;
        $this->form_validation->set_rules('marksheet', $this->lang->line('marksheet_template'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('session_id', $this->lang->line('session'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_group_id', $this->lang->line('exam_group'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

        } else {
            $exam_group_id = $this->input->post('exam_group_id');
            $exam_id       = $this->input->post('exam_id');
            $session_id    = $this->input->post('session_id');
            $class_id      = $this->input->post('class_id');
            $section_id    = $this->input->post('section_id');

            $marksheet_template         = $this->input->post('marksheet');
            $data['marksheet_template'] = $marksheet_template;
            $data['studentList']        = $this->examgroupstudent_model->searchExamStudents($exam_group_id, $exam_id, $class_id, $section_id, $session_id);
            $data['examList']           = $this->examgroup_model->getExamByExamGroup($exam_group_id, true);
            $data['exam_id']            = $exam_id;
            $data['exam_group_id']      = $exam_group_id;
        }
        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/examresult/marksheet', $data);
        $this->load->view('layout/footer', $data);
    }

    public function pdftmarksheet()
    {       
        $template         = $this->marksheet_model->get($this->input->post('marksheet_template'));
        $data['template'] = $template;

        $student_id                             = $this->input->post('student_id');
        $post_exam_id                           = $this->input->post('post_exam_id');
        $post_exam_group_id                     = $this->input->post('post_exam_group_id');
        $exam_group_class_batch_exam_student_id = $this->input->post('exam_group_class_batch_exam_student_id');
        $exam                                   = $this->examgroup_model->getExamByID($post_exam_id);
        $data['exam']                           = $exam;
        $marks_division                         = $this->marksdivision_model->get();
        $data['marks_division']                 = $marks_division;
        $student_data                           = $this->student_model->get($student_id);
        $exam_grades                            = $this->grade_model->getByExamType($exam->exam_group_type);
        $data['exam_grades']                    = $exam_grades;
        $data['marksheet']                      = $this->examresult_model->getStudentExamResults($post_exam_id, $post_exam_group_id, $exam_group_class_batch_exam_student_id, $student_id);
        $data['sch_setting']                    = $this->sch_setting_detail;
        $html                                   = $this->load->view('admin/examresult/_printpdfmarksheet', $data, true);

        $type = $this->input->post('type');
        $this->load->library('m_pdf');
        $mpdf       = $this->m_pdf->load();
        $stylesheet = file_get_contents(base_url() . 'backend/pdf_style.css'); // external css
        if ($template->background_img != "") {

            $mpdf->SetDefaultBodyCSS('background', "url('".$this->customlib->getFolderPath()."./uploads/marksheet/" . $template->background_img . "')");
            $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
        }
        $mpdf->WriteHTML($stylesheet, 1); // Writing style to pdf
		$mpdf->SetWatermarkText("", .2); // add watermark text to be show in marksheet
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->showWatermarkText = true;
        $mpdf->autoScriptToLang  = true;
        $mpdf->baseScript        = 1;
        $mpdf->autoLangToFont    = true;
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        $response = true;
        if ($type == "email") {
            $content = $mpdf->Output(random_string() . '.pdf', 'S');
            $student_value=$data['marksheet']['student'];
            $exam_roll_no=($exam->use_exam_roll_no) ? $student_value['exam_roll_no']:$student_value['student_roll_no'];

            $student_name = $this->customlib->getFullName($student_data['firstname'], $student_data['middlename'], $student_data['lastname'], $data['sch_setting']->middlename, $data['sch_setting']->lastname);

            $sender_details = array('email' => $student_data['email'], 'student_name' => $student_name, 'class' => $student_data['class'], 'section' => $student_data['section'], 'admission_no' => $student_data['admission_no'], 'roll_no' => $student_data['roll_no'], 'admit_card_roll_no'=>$exam_roll_no,'dob' => $student_data['dob'], 'guardian_name' => $student_data['guardian_name'], 'guardian_relation' => $student_data['guardian_relation'], 'guardian_phone' => $student_data['guardian_phone'], 'father_name' => $student_data['father_name'], 'father_phone' => $student_data['father_phone'], 'mother_name' => $student_data['mother_name'], 'gender' => $student_data['gender'], 'guardian_email' => $student_data['guardian_email'], 'exam' => $exam->exam);


            $this->mailsmsconf->mailsms('email_pdf_exam_marksheet', $sender_details, '', '', $content);

        } elseif ($type == "download") {

            $content = $mpdf->Output(random_string() . '.pdf', 'I');
            return $content;

        }
        if ($response) {
            $array = array('status' => 1, 'message' => $this->lang->line('mail_sent_successfully'));

        } else {
            $array = array('status' => 0, 'message' => $this->lang->line('something_went_wrong'));

        }
        echo json_encode($array);

    }

    public function printmarksheet()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('post_exam_id', $this->lang->line('exam'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('post_exam_group_id', $this->lang->line('exam_group'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('exam_group_class_batch_exam_student_id[]', $this->lang->line('students'), 'required|trim|xss_clean');
        $data = array();

        if ($this->form_validation->run() == false) {
            $data = array(
                'post_exam_id'                           => form_error('post_exam_id'),
                'post_exam_group_id'                     => form_error('post_exam_group_id'),
                'exam_group_class_batch_exam_student_id' => form_error('exam_group_class_batch_exam_student_id'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {
            $template = $this->marksheet_model->get($this->input->post('marksheet_template'));

            $data['template'] = $template;

            $post_exam_id           = $this->input->post('post_exam_id');
            $post_exam_group_id     = $this->input->post('post_exam_group_id');
            $students_array         = $this->input->post('exam_group_class_batch_exam_student_id');
            $marks_division         = $this->marksdivision_model->get();
            $data['marks_division'] = $marks_division;
            $exam                   = $this->examgroup_model->getExamByID($post_exam_id);
            $data['exam']           = $exam;
            $exam_grades            = $this->grade_model->getByExamType($exam->exam_group_type);
            $data['exam_grades']    = $exam_grades;
            $data['marksheet']      = $this->examresult_model->getExamResults($post_exam_id, $post_exam_group_id, $students_array);
            $data['sch_setting']    = $this->sch_setting_detail;
            $html                   = $this->load->view('admin/examresult/_printmarksheet', $data, true);
            $this->load->library('m_pdf');

            $mpdf       = $this->m_pdf->load();
            $stylesheet = file_get_contents(base_url() . 'backend/pdf_style.css'); // external css
            if ($template->background_img != "") {

				$mpdf->SetDefaultBodyCSS('background', "url('".$this->customlib->getFolderPath()."./uploads/marksheet/" . $template->background_img . "')");
                $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
            }
            $mpdf->WriteHTML($stylesheet, 1); // Writing style to pdf
			$mpdf->SetWatermarkText("", .2); // add watermark text to be show in marksheet
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->showWatermarkText = true;
            $mpdf->autoScriptToLang  = true;
            $mpdf->baseScript        = 1;
            $mpdf->autoLangToFont    = true;
            $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
            $response = true;

            $content = $mpdf->Output(random_string() . '.pdf', 'I');
            return $content;

        }
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('exam_result', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Examinations');
        $this->session->set_userdata('sub_menu', 'Examinations/Examresult');
        $examgroup_result      = $this->examgroup_model->get();
        $data['examgrouplist'] = $examgroup_result;
        $marksheet_result      = $this->marksheet_model->get();
        $data['marksheetlist'] = $marksheet_result;
        $class                 = $this->class_model->get();
        $data['title']         = 'Add Batch';
        $data['title_list']    = 'Recent Batch';
        $data['examType']      = $this->exam_type;
        $data['classlist']     = $class;
        $session               = $this->session_model->get();
        $data['sessionlist']   = $session;
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('session_id', $this->lang->line('session'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_group_id', $this->lang->line('exam_group'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {
            $exam_group_id = $this->input->post('exam_group_id');
            $exam_id       = $this->input->post('exam_id');
            $session_id    = $this->input->post('session_id');
            $class_id      = $this->input->post('class_id');
            $section_id    = $this->input->post('section_id');

            $marksheet_template         = $this->input->post('marksheet');
            $data['marksheet_template'] = $marksheet_template;
            $exam_details               = $this->examgroup_model->getExamByID($exam_id);
            $studentList                = $this->examgroupstudent_model->searchExamStudents($exam_group_id, $exam_id, $class_id, $section_id, $session_id);

            $exam_subjects       = $this->batchsubject_model->getExamSubjects($exam_id);
            $data['subjectList'] = $exam_subjects;

            if (!empty($studentList)) {
                foreach ($studentList as $student_key => $student_value) {
                    $studentList[$student_key]->subject_results = $this->examresult_model->getStudentResultByExam($exam_id, $student_value->exam_group_class_batch_exam_student_id);
                }
            }

            $data['studentList'] = $studentList;

            $exam_grades           = $this->grade_model->getByExamType($exam_details->exam_group_type);
            $data['exam_grades']   = $exam_grades;
            $data['exam_details']  = $exam_details;
            $data['exam_id']       = $exam_id;
            $data['exam_group_id'] = $exam_group_id;
        }

        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/examresult/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function updaterank()
    {
        $exam_group_class_batch_exam_id         = $this->input->post('exam_group_class_batch_exam_id');
        $exam_group_class_batch_exam_student_id = $this->input->post('exam_group_class_batch_exam_student_id');
        if (!empty($exam_group_class_batch_exam_student_id)) {
            $exam_group_class_batch_exam_students = array();
            foreach ($exam_group_class_batch_exam_student_id as $exam_student_id_key => $exam_student_id_value) {
                $exam_group_class_batch_exam_students[] = array(
                    'id'   => $exam_student_id_value,
                    'rank' => $this->input->post('exam_group_class_batch_exam_student_id_' . $exam_student_id_value),
                );

            }
            $this->examresult_model->updaterank($exam_group_class_batch_exam_students, $exam_group_class_batch_exam_id);
        }

        $array = array('status' => '1', 'message' => $this->lang->line('update_message'));
        echo json_encode($array);
    }

    public function examrank()
    {
        $exam_id       = $this->input->post('exam_id');
        $studentList   = $this->examgroupstudent_model->searchExamStudentsByExam($exam_id);         
        $exam_details  = $this->examgroup_model->getExamByID($exam_id);
        $exam_subjects = $this->batchsubject_model->getExamSubjects($exam_id);        
        $subjectList   = $exam_subjects;

        if (!empty($studentList)) {
            foreach ($studentList as $student_key => $student_value) {
                $studentList[$student_key]->subject_results = $this->examresult_model->getStudentResultByExam($exam_id, $student_value->exam_group_class_batch_exam_student_id);
            }
        }
        $data['subjectList']  = $exam_subjects;
        $data['studentList']  = $studentList;
        $exam_grades          = $this->grade_model->getByExamType($exam_details->exam_group_type);
        $data['exam_grades']  = $exam_grades;
        $data['exam_details'] = $exam_details;
        $data['exam_id']      = $exam_id;
        $data['sch_setting']  = $this->sch_setting_detail;
        $page                 = $this->load->view('admin/examresult/_partialexamrank', $data, true);

        $array = array('status' => '1', 'page' => $page, 'exam_details' => $exam_details, 'message' => $this->lang->line('success_message'));
        echo json_encode($array);

    }

    public function getStudentByClassBatch()
    {
        $class_id            = $this->input->post('class_id');
        $section_id          = $this->input->post('section_id');
        $session_id          = $this->input->post('session_id');
        $data['studentList'] = $this->examgroupstudent_model->searchStudentByClassSectionSession($class_id, $section_id, $session_id);
        echo json_encode($data);
    }

    public function getExamGroupByStudent()
    {
        $student_id = $this->input->post('student_id');
        $data['examgrouplist'] = $this->examgroup_model->getExamGroupByStudent($student_id);
        echo json_encode($data);
    }

    public function studentresult()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('exam_group_id', $this->lang->line('exam_group_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('student_id', $this->lang->line('student_id'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'exam_group_id' => form_error('exam_group_id'),
                'student_id'    => form_error('student_id'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {

            $student_id         = $this->input->post('student_id');
            $exam_group_id      = $this->input->post('exam_group_id');
            $exam_group_exam_id = $this->input->post('exam_id');

            $examresult  = array();
            $exam_grades = array();
            if ($exam_group_exam_id != "") {
                $examresult = $this->examgroup_model->getExamResultDetailStudent($exam_group_exam_id, $exam_group_id, $student_id);
                $data['examresult']  = $examresult;
                $exam_grades         = $this->grade_model->getByExamType($examresult->exam_type);
                $data['exam_grades'] = $exam_grades;
                $examresult          = $this->load->view('admin/examresult/_getExam', $data, true);
            } else {
                $exam_group         = $this->examgroup_model->get($exam_group_id);
                $data['exam_group'] = $exam_group;
                $exam_grades         = $this->grade_model->getByExamType($exam_group->exam_type);
                $data['exam_grades'] = $exam_grades;
                $exam_result              = $this->examgroup_model->getExamGroupExamsResultByStudentID($exam_group_id, $student_id);
                $data['examresult']       = $exam_result;
                $exam_connections         = $this->examgroup_model->getExamGroupConnection($exam_group_id);
                $data['exam_connections'] = $exam_connections;
                $examresult               = $this->load->view('admin/examresult/_getExamGroupResult', $data, true);
            }

            $data['exam_grades'] = $exam_grades;

            $array = array('status' => '1', 'result' => $examresult, 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function getStudentCurrentResult()
    {
        $this->form_validation->set_rules('student_session_id', $this->lang->line('student_id'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'student_session_id' => form_error('student_session_id'),
            );

            $array = array('status' => 0, 'error' => $msg);
        } else {
            $student_session_id  = $this->input->post('student_session_id');
            $data['exam_grades'] = $this->grade_model->get();
            $exam_groups_attempt = $this->examgroup_model->getExamGroupByStudentSession($student_session_id);

            $data['exam_groups_attempt'] = $exam_groups_attempt;
            $examresult                  = $this->load->view('admin/examresult/_getExamGroupResult', $data, true);
            $array                       = array('status' => 1, 'error' => '', 'result' => $examresult);
        }
        echo json_encode($array);
    }

    public function generatemarksheet()
    {
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('check[]', $this->lang->line('students'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'exam_id' => form_error('exam_id'),
                'check'   => form_error('check'),
            );

            $array = array('status' => 0, 'error' => $msg);
        } else {
            echo "<pre/>";
            $exam_id         = $this->input->post('exam_id');
            $students        = $this->input->post('check');
            $exam            = $this->examgroup_model->getExamByID($exam_id);
            $exam_id         = $exam->id;
            $students_result = array();
            if (!empty($students)) {
                foreach ($students as $student_key => $student_value) {
                    print_r($student_value);
                    exit();

                    $students_result[] = $this->examresult_model->getStudentExamResult($exam_id, $student_value);
                }
            }
            print_r($students_result);
            exit();
        }
        echo json_encode($array);
    }

    public function rankreport()
    {
        if (!$this->rbac->hasPrivilege('rank_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/examinations');
        $this->session->set_userdata('subsub_menu', 'Reports/examinations/rankreport');
        $examgroup_result      = $this->examgroup_model->get();
        $data['examgrouplist'] = $examgroup_result;

        $marksheet_result      = $this->marksheet_model->get();
        $data['marksheetlist'] = $marksheet_result;

        $class               = $this->class_model->get();
        $data['title']       = 'Add Batch';
        $data['title_list']  = 'Recent Batch';
        $data['examType']    = $this->exam_type;
        $data['classlist']   = $class;
        $session             = $this->session_model->get();
        $data['sessionlist'] = $session;
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('session_id', $this->lang->line('session'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_group_id', $this->lang->line('exam_group'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {

   
            $exam_group_id = $this->input->post('exam_group_id');
            $exam_id       = $this->input->post('exam_id');
            $session_id    = $this->input->post('session_id');
            $class_id      = $this->input->post('class_id');
            $section_id    = $this->input->post('section_id');

            $marksheet_template         = $this->input->post('marksheet');
            $data['marksheet_template'] = $marksheet_template;
            $exam_details               = $this->examgroup_model->getExamByID($exam_id);

            $studentList = $this->examgroupstudent_model->searchExamStudents($exam_group_id, $exam_id, $class_id, $section_id, $session_id);

            $exam_subjects       = $this->batchsubject_model->getExamSubjects($exam_id);
            $data['subjectList'] = $exam_subjects;

            if (!empty($studentList)) {
                foreach ($studentList as $student_key => $student_value) {
                    $studentList[$student_key]->subject_results = $this->examresult_model->getStudentResultByExam($exam_id, $student_value->exam_group_class_batch_exam_student_id);
                }
            }

            $data['studentList'] = $studentList;

            $exam_grades           = $this->grade_model->getByExamType($exam_details->exam_group_type);
            $data['exam_grades']   = $exam_grades;
            $data['exam_details']  = $exam_details;
            $data['exam_id']       = $exam_id;
            $data['exam_group_id'] = $exam_group_id;
        }
        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/examresult/rankreport', $data);
        $this->load->view('layout/footer', $data);
    }
    
    public function examinations()
    {
        if (!$this->rbac->hasPrivilege('rank_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/examinations');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('admin/examresult/examinations');
        $this->load->view('layout/footer');
    }
    public function bulletin()
    {
        if (!$this->rbac->hasPrivilege('print_marksheet', 'can_view')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'Examinations'); 
        $this->session->set_userdata('sub_menu', 'Examinations/bulletin_de_notes');
        
        // FETCH THE MISSING SETTINGS HERE
        $data['sch_setting']   = $this->sch_setting_detail; 
        
        $data['title']         = 'Bulletin de Notes';
        $data['examgrouplist'] = $this->examgroup_model->get();
        $data['classlist']     = $this->class_model->get();
        $data['sessionlist']   = $this->session_model->getAllSession(); 

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $exam_group_id   = $this->input->post('exam_group_id');
            $exam_id         = $this->input->post('exam_id');
            $class_id        = $this->input->post('class_id');
            $section_id      = $this->input->post('section_id'); 
            $session_id      = $this->input->post('session_id');
            
            $data['exam_group_id'] = $exam_group_id;
            $data['exam_id']       = $exam_id;
            $data['class_id']      = $class_id;
            $data['section_id']    = $section_id;
            $data['session_id']    = $session_id;
            
            $data['examList']    = $this->examgroup_model->getExamByExamGroup($exam_group_id);
            $data['studentList'] = $this->examgroupstudent_model->searchExamStudents($exam_group_id, $exam_id, $class_id, $section_id, $session_id);
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/examresult/bulletinSearch', $data);
        $this->load->view('layout/footer', $data);
    }

 public function print_bulletin()
{
    // AUTHORIZATION FIX: Ensure printing is also authorized for teachers
    if (!$this->rbac->hasPrivilege('print_marksheet', 'can_view')) {
        access_denied();
    }

    $student_array = array();
    $exam_id       = $this->input->post('exam_id');
    $student_ids   = $this->input->post('student_ids');
    $search_type   = $this->input->post('search'); // Capture which button was clicked

    if (empty($student_ids) || empty($exam_id)) {
        redirect('admin/examresult/bulletin');
    }

    $data['sch_setting'] = $this->sch_setting_detail;
    
    // Fetch Exam Details
    $exam_details         = $this->examgroup_model->getExamByID($exam_id);
    $data['exam']         = $exam_details; 
    $data['exam_name']    = $exam_details->exam;
    $data['exam_description'] = $exam_details->description; 

    $current_session = $this->setting_model->getCurrentSession();

    foreach ($student_ids as $student_id) {
        $this->db->select('students.*, classes.class, sections.section, student_session.id as student_session_id, student_session.class_id, student_session.section_id');
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'student_session.section_id = sections.id');
        $this->db->where('students.id', $student_id);
        $this->db->where('student_session.session_id', $current_session);
        $query = $this->db->get();
        $student_info = $query->row_array();

        if ($student_info) {
            $student_session_id = $student_info['student_session_id'];
            
            // Get Total Class Students
            $this->db->where('class_id', $student_info['class_id']);
            $this->db->where('section_id', $student_info['section_id']);
            $this->db->where('session_id', $current_session);
            $student_info['total_class_students'] = $this->db->count_all_results('student_session');

            // Fetch Marks 
            $this->db->select('exam_group_exam_results.get_marks, exam_group_exam_results.note, 
                            exam_group_class_batch_exam_subjects.max_marks, 
                            exam_group_class_batch_exam_subjects.min_marks, 
                            subjects.name as subject_name, subjects.code');
            $this->db->from('exam_group_class_batch_exam_subjects');
            $this->db->join('subjects', 'subjects.id = exam_group_class_batch_exam_subjects.subject_id');
            $this->db->join('exam_group_class_batch_exam_students', 
                            'exam_group_class_batch_exam_students.exam_group_class_batch_exam_id = exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id');
            $this->db->join('exam_group_exam_results', 
                            'exam_group_exam_results.exam_group_class_batch_exam_subject_id = exam_group_class_batch_exam_subjects.id 
                            AND exam_group_exam_results.exam_group_class_batch_exam_student_id = exam_group_class_batch_exam_students.id', 'left');
            
            $this->db->where('exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id', $exam_id);
            $this->db->where('exam_group_class_batch_exam_students.student_session_id', $student_session_id);
            
            $marks_query = $this->db->get();
            $student_info['exam_result'] = $marks_query->result();
            
            $student_array[] = $student_info;
        }
    }

    $data['student_list'] = $student_array;

    // --- ROUTING LOGIC BASED ON BUTTON CLICKED ---
    if ($search_type == 'generate_ar') {
        // Load the new Arabic template
        $this->load->view('admin/examresult/_printBulletinAr', $data);
    } else {
        // Load the default standard template
        $this->load->view('admin/examresult/_printBulletin', $data);
    }
    }


    public function exam_result_search()
    {
        if (!$this->rbac->hasPrivilege('print_marksheet', 'can_view')) {
            access_denied();
        }
        
        // This MUST match the URL in your sidebar_sub_menus table to keep the menu highlighted
        $this->session->set_userdata('top_menu', 'Examinations'); 
        $this->session->set_userdata('sub_menu', 'admin/examresult/exam_result_search');
        
        $data['sch_setting']   = $this->sch_setting_detail; 
        $data['title']         = 'Exam Result Landscape';
        $data['examgrouplist'] = $this->examgroup_model->get();
        $data['classlist']     = $this->class_model->get();
        $data['sessionlist']   = $this->session_model->getAllSession(); 

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $exam_group_id   = $this->input->post('exam_group_id');
            $exam_id         = $this->input->post('exam_id');
            $class_id        = $this->input->post('class_id');
            $section_id      = $this->input->post('section_id'); 
            $session_id      = $this->input->post('session_id');
            
            $data['exam_group_id'] = $exam_group_id;
            $data['exam_id']       = $exam_id;
            $data['class_id']      = $class_id;
            $data['section_id']    = $section_id;
            $data['session_id']    = $session_id;
            
            $data['examList']    = $this->examgroup_model->getExamByExamGroup($exam_group_id);
            $data['studentList'] = $this->examgroupstudent_model->searchExamStudents($exam_group_id, $exam_id, $class_id, $section_id, $session_id);
        }

        $this->load->view('layout/header', $data);
        // Pointing to your new duplicated view file
        $this->load->view('admin/examresult/examResultSearch', $data);
        $this->load->view('layout/footer', $data);
    }



    public function print_class_result()
    {
        if (!$this->rbac->hasPrivilege('print_marksheet', 'can_view')) {
            access_denied();
        }

        $student_array = array();
        $exam_id       = $this->input->post('exam_id');
        $student_ids   = $this->input->post('student_ids');
        $search_type   = $this->input->post('search'); 

        if (empty($student_ids) || empty($exam_id)) {
            redirect('admin/examresult/bulletin');
        }

        $data['sch_setting'] = $this->sch_setting_detail;
        $exam_details        = $this->examgroup_model->getExamByID($exam_id);
        $data['exam']        = $exam_details; 

        $current_session = $this->setting_model->getCurrentSession();
        $all_subjects = array();

        foreach ($student_ids as $student_id) {
            $this->db->select('students.*, classes.class, sections.section, student_session.id as student_session_id, student_session.class_id, student_session.section_id');
            $this->db->from('students');
            $this->db->join('student_session', 'student_session.student_id = students.id');
            $this->db->join('classes', 'student_session.class_id = classes.id');
            $this->db->join('sections', 'student_session.section_id = sections.id');
            $this->db->where('students.id', $student_id);
            $this->db->where('student_session.session_id', $current_session);
            $query = $this->db->get();
            $student_info = $query->row_array();

            if ($student_info) {
                // ADDED max_marks TO THE SELECT BELOW
                $this->db->select('exam_group_exam_results.get_marks, exam_group_class_batch_exam_subjects.max_marks, subjects.name as subject_name, subjects.code');
                $this->db->from('exam_group_class_batch_exam_subjects');
                $this->db->join('subjects', 'subjects.id = exam_group_class_batch_exam_subjects.subject_id');
                $this->db->join('exam_group_class_batch_exam_students', 'exam_group_class_batch_exam_students.exam_group_class_batch_exam_id = exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id');
                $this->db->join('exam_group_exam_results', 'exam_group_exam_results.exam_group_class_batch_exam_subject_id = exam_group_class_batch_exam_subjects.id AND exam_group_exam_results.exam_group_class_batch_exam_student_id = exam_group_class_batch_exam_students.id', 'left');
                $this->db->where('exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id', $exam_id);
                $this->db->where('exam_group_class_batch_exam_students.student_session_id', $student_info['student_session_id']);
                
                $marks_query = $this->db->get();
                $results = $marks_query->result_array();
                $student_info['exam_result'] = $results;

                foreach ($results as $res) {
                    if (!in_array($res['subject_name'], $all_subjects)) {
                        $all_subjects[] = $res['subject_name'];
                    }
                }
                
                $student_array[] = $student_info;
            }
        }

        $data['student_list'] = $student_array;
        $data['subjects']     = $all_subjects; 
        $data['academic_year'] = $this->setting_model->getCurrentSessionName();

        $this->load->view('admin/examresult/_printClassResult', $data);
    }

    public function bulletincollege()
    {
        if (!$this->rbac->hasPrivilege('print_marksheet', 'can_view')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'Examinations'); 
        $this->session->set_userdata('sub_menu', 'admin/examresult/bulletincollege');
        
        $data['sch_setting']   = $this->sch_setting_detail; 
        $data['title']         = 'Bulletin de Notes College';
        $data['examgrouplist'] = $this->examgroup_model->get();
        $data['classlist']     = $this->class_model->get();
        $data['sessionlist']   = $this->session_model->get(); // Using working get() method

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $exam_group_id   = $this->input->post('exam_group_id');
            $exam_id         = $this->input->post('exam_id');
            $class_id        = $this->input->post('class_id');
            $section_id      = $this->input->post('section_id'); 
            $session_id      = $this->input->post('session_id');
            
            $data['exam_group_id'] = $exam_group_id;
            $data['exam_id']       = $exam_id;
            $data['class_id']      = $class_id;
            $data['section_id']    = $section_id;
            $data['session_id']    = $session_id;
            
            $data['examList'] = $this->examgroup_model->getExamByExamGroup($exam_group_id);

            // Only search if we have the minimum required data to prevent SQL NULL errors
            if ($exam_id && $class_id && $session_id) {
                $data['studentList'] = $this->examgroupstudent_model->searchExamStudents($exam_group_id, $exam_id, $class_id, $section_id, $session_id);
            } else {
                $data['studentList'] = array();
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/examresult/bulletinSearchCollege', $data);
        $this->load->view('layout/footer', $data);
    }

    public function print_bulletin_college()
    {
        if (!$this->rbac->hasPrivilege('print_marksheet', 'can_view')) {
            access_denied();
        }

        $student_array = array();
        $subject_leaderboard = array(); 
        $class_averages = array(); // To store everyone's average for ranking
        $exam_id = $this->input->post('exam_id');
        $student_ids = $this->input->post('student_ids');

        if (empty($student_ids) || empty($exam_id)) {
            redirect('admin/examresult/bulletincollege');
        }

        $data['sch_setting'] = $this->sch_setting_detail;
        $exam_details = $this->examgroup_model->getExamByID($exam_id);
        $data['exam_description'] = $exam_details->description;
        $current_session = $this->setting_model->getCurrentSession();

        // --- PASS 1: COLLECT ALL DATA FOR RANKING ---
        foreach ($student_ids as $student_id) {
            $this->db->select('students.*, classes.class, sections.section, student_session.id as student_session_id');
            $this->db->from('students');
            $this->db->join('student_session', 'student_session.student_id = students.id');
            $this->db->join('classes', 'student_session.class_id = classes.id');
            $this->db->join('sections', 'student_session.section_id = sections.id');
            $this->db->where('students.id', $student_id);
            $this->db->where('student_session.session_id', $current_session);
            $student_info = $this->db->get()->row_array();

            if ($student_info) {
                $this->db->select('exam_group_exam_results.get_marks, subjects.id as subject_id, subjects.name as subject_name, exam_group_class_batch_exam_subjects.coef');
                $this->db->from('exam_group_class_batch_exam_subjects');
                $this->db->join('subjects', 'subjects.id = exam_group_class_batch_exam_subjects.subject_id');
                $this->db->join('exam_group_class_batch_exam_students', 'exam_group_class_batch_exam_students.exam_group_class_batch_exam_id = exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id');
                $this->db->join('exam_group_exam_results', 'exam_group_exam_results.exam_group_class_batch_exam_subject_id = exam_group_class_batch_exam_subjects.id AND exam_group_exam_results.exam_group_class_batch_exam_student_id = exam_group_class_batch_exam_students.id', 'left');
                $this->db->where('exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id', $exam_id);
                $this->db->where('exam_group_class_batch_exam_students.student_session_id', $student_info['student_session_id']);
                
                $marks = $this->db->get()->result();
                $student_info['exam_result'] = $marks;

                // Calculations for Overall Rank
                $st_m_coef = 0; $st_coef = 0;
                foreach ($marks as $m) {
                    $c = ($m->coef > 0) ? (float)$m->coef : 1.0;
                    $st_m_coef += ((float)$m->get_marks * $c);
                    $st_coef += $c;
                    $subject_leaderboard[$m->subject_id][] = (float)$m->get_marks;
                }
                
                if ($st_coef > 0) {
                    $class_averages[$student_id] = $st_m_coef / $st_coef;
                }
                $student_array[] = $student_info;
            }
        }

        // --- PASS 2: CALCULATE RANKS ---
        foreach ($subject_leaderboard as $sub_id => $marks_list) {
            rsort($subject_leaderboard[$sub_id]);
            $subject_leaderboard[$sub_id] = array_values(array_unique($subject_leaderboard[$sub_id]));
        }

        // Overall Class Ranks
        arsort($class_averages);
        $rank_map = []; $r = 1;
        foreach ($class_averages as $sid => $avg) {
            $rank_map[$sid] = $r++;
        }

        $data['class_average'] = count($class_averages) > 0 ? array_sum($class_averages) / count($class_averages) : 0;
        $data['max_class_avg'] = count($class_averages) > 0 ? max($class_averages) : 0;
        $data['min_class_avg'] = count($class_averages) > 0 ? min($class_averages) : 0;
        $data['rank_map'] = $rank_map;
        $data['subject_leaderboard'] = $subject_leaderboard;
        $data['student_list'] = $student_array;

        $this->load->view('admin/examresult/_printBulletinCollege', $data);
    }

public function btn_sem_college()
{
    if (!$this->rbac->hasPrivilege('exam_result', 'can_view')) {
        access_denied();
    }

    $this->session->set_userdata('top_menu', 'Examinations');
    $this->session->set_userdata('sub_menu', 'admin/examresult/btn_sem_college');

    $data['sch_setting']   = $this->sch_setting_detail;
    $data['title']         = 'Bulletin Semestre Collège';
    $data['examgrouplist'] = $this->examgroup_model->get();
    $data['classlist']     = $this->class_model->get();
    $data['sessionlist']   = $this->session_model->get();

    if ($this->input->server('REQUEST_METHOD') == "POST") {
        $exam_group_id = $this->input->post('exam_group_id');
        $devoir_exams  = $this->input->post('devoir_exams'); // This is an array
        $comp_exam     = $this->input->post('comp_exam');    // This is a single ID
        $class_id      = $this->input->post('class_id');
        $section_id    = $this->input->post('section_id');
        $session_id    = $this->input->post('session_id');

        $data['exam_group_id'] = $exam_group_id;
        $data['devoir_exams']  = $devoir_exams;
        $data['comp_exam']     = $comp_exam;
        $data['class_id']      = $class_id;
        $data['section_id']    = $section_id;
        $data['session_id']    = $session_id;

        // Populate exams for the dropdowns based on group
        $data['examList'] = $this->examgroup_model->getExamByExamGroup($exam_group_id);

        // Fetch students using the main Composition exam as the base
        if ($comp_exam && $class_id && $session_id) {
            $data['studentList'] = $this->examgroupstudent_model->searchExamStudents($exam_group_id, $comp_exam, $class_id, $section_id, $session_id);
        } else {
            $data['studentList'] = array();
        }
    }

    $this->load->view('layout/header', $data);
    $this->load->view('admin/examresult/semester_college_search', $data);
    $this->load->view('layout/footer', $data);
}

public function print_semester_college()
{
    if (!$this->rbac->hasPrivilege('print_marksheet', 'can_view')) {
        access_denied();
    }

    $student_ids     = $this->input->post('student_ids');
    $exam_group_id   = $this->input->post('exam_group_id');
    $devoir_exam_ids = $this->input->post('devoir_exams'); // Array of IDs
    $comp_exam_id    = $this->input->post('comp_exam');    // Single ID

    if (empty($student_ids) || empty($comp_exam_id)) {
        redirect('admin/examresult/btn_sem_college');
    }

    $data['sch_setting'] = $this->sch_setting_detail;
    $exam_details        = $this->examgroup_model->getExamByID($comp_exam_id);
    $data['exam_description'] = $exam_details->description;
    $current_session     = $this->setting_model->getCurrentSession();

    $student_array       = array();
    $class_averages      = array(); // For ranking

    // --- PASS 1: COLLECT ALL DATA AND CALCULATE AVERAGES ---
    foreach ($student_ids as $student_id) {
        $this->db->select('students.*, classes.class, sections.section, student_session.id as student_session_id');
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'student_session.section_id = sections.id');
        $this->db->where('students.id', $student_id);
        $this->db->where('student_session.session_id', $current_session);
        $student_info = $this->db->get()->row_array();

        if ($student_info) {
            $session_id = $student_info['student_session_id'];

            // Master subjects list from Composition Exam
            $this->db->select('exam_group_class_batch_exam_subjects.*, subjects.name as subject_name, subjects.id as sub_real_id');
            $this->db->from('exam_group_class_batch_exam_subjects');
            $this->db->join('subjects', 'subjects.id = exam_group_class_batch_exam_subjects.subject_id');
            $this->db->where('exam_group_class_batch_exams_id', $comp_exam_id);
            $subjects = $this->db->get()->result();

            $semester_results = array();
            $st_weighted_total = 0;
            $st_coef_total = 0;

            foreach ($subjects as $sub) {
                // 1. Calculate Devoir Average
                $total_devoir = 0;
                $devoir_count = 0;
                foreach ($devoir_exam_ids as $d_id) {
                    $d_mark = $this->get_semester_mark($d_id, $session_id, $sub->subject_id);
                    if ($d_mark !== null) {
                        $total_devoir += (float)$d_mark;
                        $devoir_count++;
                    }
                }
                $avg_devoir = ($devoir_count > 0) ? ($total_devoir / $devoir_count) : 0;

                // 2. Get Comp Mark
                $comp_mark = $this->get_semester_mark($comp_exam_id, $session_id, $sub->subject_id);
                $final_comp = ($comp_mark !== null) ? (float)$comp_mark : 0;

                // 3. Semester Logic
                $moy_20 = ($avg_devoir + $final_comp) / 2;
                $coef   = ($sub->coef > 0) ? (float)$sub->coef : 1.0;
                $m_coef = $moy_20 * $coef;

                $semester_results[] = (object) array(
                    'subject_name' => $sub->subject_name,
                    'devoir'       => $avg_devoir,
                    'comp'         => $final_comp,
                    'moy_20'       => $moy_20,
                    'coef'         => $coef,
                    'm_coef'       => $m_coef
                );

                $st_weighted_total += $m_coef;
                $st_coef_total     += $coef;
            }

            $student_info['exam_result'] = $semester_results;
            if ($st_coef_total > 0) {
                $class_averages[$student_id] = $st_weighted_total / $st_coef_total;
            }
            $student_array[] = $student_info;
        }
    }

    // --- PASS 2: CALCULATE RANKING ---
    arsort($class_averages);
    $rank_map = array(); $r = 1;
    foreach ($class_averages as $sid => $avg) { $rank_map[$sid] = $r++; }

    $data['student_list']   = $student_array;
    $data['rank_map']       = $rank_map;
    $data['class_average']  = (count($class_averages) > 0) ? array_sum($class_averages) / count($class_averages) : 0;
    $data['max_avg']        = (count($class_averages) > 0) ? max($class_averages) : 0;
    $data['min_avg']        = (count($class_averages) > 0) ? min($class_averages) : 0;

    $this->load->view('admin/examresult/_printSemesterCollege', $data);
}

/**
 * Helper to fetch mark based on your specific table structure
 */
private function get_semester_mark($exam_id, $student_session_id, $subject_id)
{
    $this->db->select('exam_group_exam_results.get_marks');
    $this->db->from('exam_group_class_batch_exam_subjects');
    $this->db->join('exam_group_class_batch_exam_students', 'exam_group_class_batch_exam_students.exam_group_class_batch_exam_id = exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id');
    $this->db->join('exam_group_exam_results', 'exam_group_exam_results.exam_group_class_batch_exam_subject_id = exam_group_class_batch_exam_subjects.id AND exam_group_exam_results.exam_group_class_batch_exam_student_id = exam_group_class_batch_exam_students.id');
    $this->db->where('exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id', $exam_id);
    $this->db->where('exam_group_class_batch_exam_subjects.subject_id', $subject_id);
    $this->db->where('exam_group_class_batch_exam_students.student_session_id', $student_session_id);
    $res = $this->db->get()->row();
    return ($res) ? $res->get_marks : null;
}
}