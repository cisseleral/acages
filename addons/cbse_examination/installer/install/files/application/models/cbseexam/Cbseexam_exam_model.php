<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbseexam_exam_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /*
    This function is used to add and update cbse exam
    */
    public function add($data, $assessment_delete = true)
    {


        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_exams', $data);


            if ($assessment_delete) { //============if assessment value changed
                $findexam_assessments = $this->db->select('id')
                    ->from('cbse_exam_timetable')
                    ->where('cbse_exam_id', $data['id'])
                    ->get()
                    ->result();
                if (!empty($findexam_assessments)) {
                    $delete_assessment = [];
                    foreach ($findexam_assessments as $findexam_assessments_key => $findexam_assessments_value) {
                        $delete_assessment[] = $findexam_assessments_value->id;
                    }
                    $this->db->where_in('cbse_exam_timetable_id', $delete_assessment);
                    $this->db->delete('cbse_student_subject_marks');

                    //========================
                    $this->db->where_in('cbse_exam_timetable_id', $delete_assessment);
                    $this->db->delete('cbse_exam_timetable_assessment_types');

                    //============================
                }
            }




            $message = UPDATE_RECORD_CONSTANT . " On cbse exams id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $insert_id = $data['id'];
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
        } else {
            $this->db->insert('cbse_exams', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On cbse exams id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
        }
        return $insert_id;
    }

    /*
    This function is used to assign exam to student
    */
    public function addexamstudent($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_exam_students', $data);
            $message = UPDATE_RECORD_CONSTANT . " On cbse exam students id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $insert_id = $data['id'];
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
        } else {
            $this->db->insert('cbse_exam_students', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On cbse exam students id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
        }
        return $insert_id;
    }

    /*
    This function is used to get exam list base on current session
    */
    public function getexamlist()
    {

         $userdata = $this->customlib->getUserData();
            $role_id = $userdata["role_id"];
            $carray = array();
            $class_section_id=array();
            if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
                if ($userdata["class_teacher"] == 'yes') {
                    $classlist = $this->teacher_model->get_teacherrestricted_mode($userdata["id"]);
                }
				
                foreach ($classlist as $key => $value) {
                    $class_section=$this->teacher_model->get_teacherrestricted_modesections($userdata["id"], $value['id']);
                    foreach ($class_section as $key => $value_section) {
                        $class_section_id[]=$value_section['id'];
                    }
                } 
            }
           
			$this->db->select('cbse_exams.*,CONCAT(classes.class, " (", GROUP_CONCAT(sections.section ORDER BY sections.section ASC SEPARATOR ","), ")" ) AS class_sections,cbse_terms.name as term_name, (select count(cbse_exam_timetable.id) from cbse_exam_timetable where cbse_exam_timetable.cbse_exam_id = cbse_exams.id)  as subjectsincluded ')->from('cbse_exams')->join('cbse_exam_class_sections', 'cbse_exam_class_sections.cbse_exam_id=cbse_exams.id')->join('class_sections', 'class_sections.id=cbse_exam_class_sections.class_section_id')->join('classes', 'classes.id=class_sections.class_id')->join('sections', 'sections.id=class_sections.section_id')->join('cbse_terms', 'cbse_terms.id=cbse_exams.cbse_term_id', 'left')->group_by('cbse_exams.id')->order_by('cbse_exams.id', 'desc');
			
        if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
			if(!empty($class_section_id)){
				$this->db->where_in('cbse_exam_class_sections.class_section_id', $class_section_id);
			}
		}
        $this->db->where('session_id', $this->current_session);
        return $this->db->get()->result_array();
    }

    /*
    This function is used to get published exam list
    */
    public function getPublishexams()
    {
        return $this->db->select('cbse_exams.*')->from('cbse_exams')->where('session_id', $this->current_session)->where('cbse_exams.is_publish', '1')->order_by('cbse_exams.name', 'asc')->get()->result_array();
    }


    public function getExamResultByExamIdByTemplate($cbse_exam_id, $cbse_template_id, $class_section_id)
    {
        $sql   = "SELECT  `cbse_exams`.*,cbse_student_template_rank.rank,cbse_student_template_rank.rank_percentage,cbse_template.gradeexam_id,cbse_template.remarkexam_id,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_students.remark,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_template_term_exams.weightage,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,cbse_student_subject_marks.is_absent,cbse_student_subject_marks.note,cbse_student_subject_marks.cbse_exam_timetable_id,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
        students.guardian_is,students.parent_id,students.admission_no,
        students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id` FROM `cbse_template` INNER JOIN cbse_template_term_exams on cbse_template_term_exams.cbse_template_id=cbse_template.id INNER JOIN `cbse_exams` on cbse_exams.id=cbse_template_term_exams.cbse_exam_id INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id  INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id  INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join  classes on student_session.class_id = classes.id INNER join  sections on sections.id = student_session.section_id left join cbse_student_template_rank on cbse_student_template_rank.cbse_template_id=cbse_template.id and cbse_student_template_rank.student_session_id=student_session.id INNER join class_sections on  class_sections.class_id = classes.id and class_sections.section_id = sections.id  WHERE cbse_template.id=" . $this->db->escape($cbse_template_id) . " and cbse_exams.`id` = " . $this->db->escape($cbse_exam_id) . " and cbse_exams.session_id=" . $this->current_session . " and class_sections.id=" . $this->db->escape($class_section_id);

        $query = $this->db->query($sql);
        return $query->result();
    }



    public function getExamResultByExamId($cbse_exam_id)
    {
        $sql   = "SELECT  `cbse_exams`.*,cbse_student_exam_ranks.rank,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_students.remark,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,cbse_student_subject_marks.is_absent,cbse_student_subject_marks.note,cbse_student_subject_marks.cbse_exam_timetable_id,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
        students.guardian_is,students.parent_id,students.admission_no,
        students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id`  FROM `cbse_exams` INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join classes on student_session.class_id = classes.id INNER join sections on sections.id = student_session.section_id left join cbse_student_exam_ranks on cbse_student_exam_ranks.student_session_id = student_session.id and cbse_student_exam_ranks.cbse_exam_id=" . $cbse_exam_id . " WHERE cbse_exams.`id` = " . $this->db->escape($cbse_exam_id) . " and cbse_exams.session_id=" . $this->current_session . " order by cbse_student_exam_ranks.rank asc";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getStudentExamResultByExamIdAndAdmissionNo($cbse_exam_id, $admission_no)
    {
        $sql   = "SELECT `cbse_exams`.*,cbse_student_exam_ranks.rank,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,cbse_student_subject_marks.is_absent,cbse_student_subject_marks.note,cbse_student_subject_marks.cbse_exam_timetable_id,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
           students.guardian_is,students.parent_id,students.admission_no,
           students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id` ,cbse_exam_students.remark FROM `cbse_exams` INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id  INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id  INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join  classes on student_session.class_id = classes.id INNER join  sections on sections.id = student_session.section_id left join cbse_student_exam_ranks on cbse_student_exam_ranks.student_session_id = student_session.id and cbse_student_exam_ranks.cbse_exam_id=cbse_exams.id WHERE cbse_exams.`id` = " . $this->db->escape($cbse_exam_id) . " and cbse_exams.session_id=" . $this->current_session . " and students.admission_no = " . $this->db->escape($admission_no);

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getStudentExamResultByExamId($cbse_template_id, $cbse_exam_id, $students)
    {
        $students = implode(', ', array_map(function ($val) {
            return sprintf("'%s'", $val);
        }, $students));

        $sql   = "SELECT  `cbse_exams`.*,cbse_exam_student_subject_rank.rank as subject_rank ,cbse_student_template_rank.rank,cbse_student_template_rank.rank_percentage,cbse_template.gradeexam_id,cbse_template.remarkexam_id,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_students.remark,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_template_term_exams.weightage,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,cbse_student_subject_marks.is_absent,cbse_student_subject_marks.note,cbse_student_subject_marks.cbse_exam_timetable_id,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
        students.guardian_is,students.parent_id,students.admission_no,
        students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id` FROM `cbse_template` INNER JOIN cbse_template_term_exams on cbse_template_term_exams.cbse_template_id=cbse_template.id INNER JOIN `cbse_exams` on cbse_exams.id=cbse_template_term_exams.cbse_exam_id INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id  INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id  INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join  classes on student_session.class_id = classes.id INNER join  sections on sections.id = student_session.section_id left join cbse_student_template_rank on cbse_student_template_rank.cbse_template_id=cbse_template.id and cbse_student_template_rank.student_session_id=student_session.id LEFT JOIN cbse_exam_student_subject_rank on cbse_exam_student_subject_rank.cbse_template_id=cbse_template.id and cbse_exam_student_subject_rank.student_session_id=student_session.id and cbse_exam_student_subject_rank.subject_id=subjects.id WHERE cbse_template.id=" . $this->db->escape($cbse_template_id) . " and cbse_exams.`id` = " . $this->db->escape($cbse_exam_id) . " and cbse_exams.session_id=" . $this->current_session . " and student_session.id in (" . $students . ")";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getStudentResultByExamId($cbse_exam_id, $students)
    {
        $students = implode(', ', array_map(function ($val) {
            return sprintf("'%s'", $val);
        }, $students));

        $sql   = "SELECT  `cbse_exams`.*,cbse_student_exam_ranks.rank,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_students.remark,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,cbse_student_subject_marks.is_absent,cbse_student_subject_marks.note,cbse_student_subject_marks.cbse_exam_timetable_id,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
        students.guardian_is,students.parent_id,students.admission_no,
        students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id`  FROM `cbse_exams` INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join classes on student_session.class_id = classes.id INNER join sections on sections.id = student_session.section_id left join cbse_student_exam_ranks on cbse_student_exam_ranks.student_session_id = student_session.id and cbse_student_exam_ranks.cbse_exam_id=" . $cbse_exam_id . " WHERE cbse_exams.`id` = " . $this->db->escape($cbse_exam_id) . " and cbse_exams.session_id=" . $this->current_session . " and student_session.id in (" . $students . ")";

        $query = $this->db->query($sql);

        return $query->result();
    }

    public function getStudentResultByTemplateId($cbse_template_id, $class_section_id)
    {
        $sql   = "SELECT  `cbse_exams`.*,cbse_student_template_rank.rank,cbse_template.gradeexam_id,cbse_template.remarkexam_id,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_students.remark,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_template_term_exams.weightage,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,cbse_student_subject_marks.is_absent,cbse_student_subject_marks.note,cbse_student_subject_marks.cbse_exam_timetable_id,cbse_exam_timetable_assessment_types.id as cbse_exam_timetable_assessment_type_id,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
            students.guardian_is,students.parent_id,students.admission_no,
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id` FROM `cbse_template` INNER JOIN cbse_template_term_exams on cbse_template_term_exams.cbse_template_id=cbse_template.id INNER JOIN `cbse_exams` on cbse_exams.id=cbse_template_term_exams.cbse_exam_id INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id  INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id  INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join  classes on student_session.class_id = classes.id INNER join  sections on sections.id = student_session.section_id INNER join class_sections on  class_sections.class_id = classes.id and class_sections.section_id = sections.id left join cbse_student_template_rank on cbse_student_template_rank.cbse_template_id=cbse_template.id and cbse_student_template_rank.student_session_id=student_session.id LEFT join cbse_exam_timetable_assessment_types on cbse_exam_timetable_assessment_types.cbse_exam_timetable_id= cbse_exam_timetable.id and cbse_exam_timetable_assessment_types.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id WHERE cbse_template.id=" . $this->db->escape($cbse_template_id) . " and class_sections.id=" . $this->db->escape($class_section_id);

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getStudentExamResultByTemplateId($cbse_template_id, $students)
    {
        $students = implode(', ', array_map(function ($val) {
            return sprintf("'%s'", $val);
        }, $students));
        $sql   = "SELECT  `cbse_exams`.*,cbse_exam_student_subject_rank.rank as subject_rank ,cbse_student_template_rank.rank,cbse_student_template_rank.rank_percentage,cbse_template.gradeexam_id,cbse_template.remarkexam_id,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_students.remark,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_template_term_exams.weightage,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,cbse_student_subject_marks.is_absent,cbse_student_subject_marks.note,cbse_student_subject_marks.cbse_exam_timetable_id,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
            students.guardian_is,students.parent_id,students.admission_no,
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id`,cbse_exam_timetable_assessment_types.id as cbse_exam_timetable_assessment_type_id FROM `cbse_template` INNER JOIN cbse_template_term_exams on cbse_template_term_exams.cbse_template_id=cbse_template.id INNER JOIN `cbse_exams` on cbse_exams.id=cbse_template_term_exams.cbse_exam_id INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id  INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id  INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join  classes on student_session.class_id = classes.id INNER join  sections on sections.id = student_session.section_id left join cbse_student_template_rank on cbse_student_template_rank.cbse_template_id=cbse_template.id and cbse_student_template_rank.student_session_id=student_session.id LEFT JOIN cbse_exam_student_subject_rank on cbse_exam_student_subject_rank.cbse_template_id=cbse_template.id and cbse_exam_student_subject_rank.student_session_id=student_session.id and cbse_exam_student_subject_rank.subject_id=subjects.id LEFT join cbse_exam_timetable_assessment_types on cbse_exam_timetable_assessment_types.cbse_exam_timetable_id= cbse_exam_timetable.id and cbse_exam_timetable_assessment_types.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id WHERE cbse_template.id=" . $this->db->escape($cbse_template_id) . " and student_session.id in (" . $students . ") order by cbse_student_template_rank.rank asc";

            

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getResultTermwiseByTemplateIdWithSelectedTerm($cbse_template_id, $class_section_id)
    {
        $sql   = "SELECT  `cbse_exams`.*,cbse_template.gradeexam_id,cbse_student_template_rank.rank,cbse_template.remarkexam_id,cbse_template_terms.weightage as `cbse_template_terms_weightage`,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_students.remark,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,cbse_student_subject_marks.is_absent,cbse_student_subject_marks.note,cbse_student_subject_marks.cbse_exam_timetable_id,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
            students.guardian_is,students.parent_id,students.admission_no,
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id`,cbse_template_terms.weightage,cbse_exam_timetable_assessment_types.id as cbse_exam_timetable_assessment_type_id FROM `cbse_template` INNER JOIN cbse_template_terms on cbse_template_terms.cbse_template_id=cbse_template.id INNER JOIN cbse_template_term_exams on cbse_template_term_exams.cbse_template_term_id=cbse_template_terms.id INNER JOIN `cbse_exams` on cbse_exams.id=cbse_template_term_exams.cbse_exam_id INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id  INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id  INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join  classes on student_session.class_id = classes.id INNER join  sections on sections.id = student_session.section_id  INNER join class_sections on  class_sections.class_id = classes.id and class_sections.section_id = sections.id left join cbse_student_template_rank on cbse_student_template_rank.cbse_template_id=cbse_template.id and cbse_student_template_rank.student_session_id=student_session.id LEFT join cbse_exam_timetable_assessment_types on cbse_exam_timetable_assessment_types.cbse_exam_timetable_id= cbse_exam_timetable.id and cbse_exam_timetable_assessment_types.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id WHERE cbse_template.id=" . $this->db->escape($cbse_template_id) . " and class_sections.id=" . $this->db->escape($class_section_id) . " order by cbse_student_template_rank.rank asc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getResultTermwiseByTemplateId($cbse_template_id, $class_section_id)
    {
        $sql   = "SELECT  `cbse_exams`.*,cbse_student_template_rank.rank,cbse_template.gradeexam_id,cbse_template.remarkexam_id,cbse_template_terms.weightage as `cbse_template_terms_weightage`,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_students.remark,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,cbse_student_subject_marks.is_absent,cbse_student_subject_marks.note,cbse_student_subject_marks.cbse_exam_timetable_id,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
            students.guardian_is,students.parent_id,students.admission_no,
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id`,cbse_template_terms.weightage,cbse_exam_timetable_assessment_types.id as cbse_exam_timetable_assessment_type_id FROM `cbse_template` INNER JOIN cbse_template_terms on cbse_template_terms.cbse_template_id=cbse_template.id INNER JOIN cbse_template_term_exams on cbse_template_term_exams.cbse_template_term_id=cbse_template_terms.id INNER JOIN `cbse_exams` on cbse_exams.id=cbse_template_term_exams.cbse_exam_id INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id  INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id  INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join  classes on student_session.class_id = classes.id INNER join  sections on sections.id = student_session.section_id INNER join class_sections on  class_sections.class_id = classes.id and class_sections.section_id = sections.id left join cbse_student_template_rank on cbse_student_template_rank.cbse_template_id=cbse_template.id and cbse_student_template_rank.student_session_id=student_session.id LEFT join cbse_exam_timetable_assessment_types on cbse_exam_timetable_assessment_types.cbse_exam_timetable_id= cbse_exam_timetable.id and cbse_exam_timetable_assessment_types.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id WHERE cbse_template.id=" . $this->db->escape($cbse_template_id) . " and class_sections.id=" . $this->db->escape($class_section_id);

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getStudentExamResultTermwiseByTemplateId($cbse_template_id, $students)
    {
        $students = implode(', ', array_map(function ($val) {
            return sprintf("'%s'", $val);
        }, $students));

        $sql   = "SELECT  `cbse_exams`.*,cbse_student_template_rank.rank,cbse_student_template_rank.rank_percentage,cbse_template.id as `cbse_template_id`,cbse_template.gradeexam_id,cbse_template.remarkexam_id,cbse_template_terms.weightage as `cbse_template_terms_weightage`,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_students.remark,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,cbse_student_subject_marks.is_absent,cbse_student_subject_marks.note,cbse_student_subject_marks.cbse_exam_timetable_id,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
            students.guardian_is,students.parent_id,students.admission_no,
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id`,cbse_template_terms.weightage,cbse_exam_student_subject_rank.rank as subject_rank,cbse_exam_timetable_assessment_types.id as cbse_exam_timetable_assessment_type_id FROM `cbse_template` INNER JOIN cbse_template_terms on cbse_template_terms.cbse_template_id=cbse_template.id INNER JOIN cbse_template_term_exams on cbse_template_term_exams.cbse_template_term_id=cbse_template_terms.id INNER JOIN `cbse_exams` on cbse_exams.id=cbse_template_term_exams.cbse_exam_id INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id  INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id  INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join  classes on student_session.class_id = classes.id INNER join  sections on sections.id = student_session.section_id left join cbse_student_template_rank on cbse_student_template_rank.cbse_template_id=cbse_template.id and cbse_student_template_rank.student_session_id=student_session.id LEFT JOIN cbse_exam_student_subject_rank on cbse_exam_student_subject_rank.cbse_template_id=cbse_template.id and cbse_exam_student_subject_rank.student_session_id=student_session.id and cbse_exam_student_subject_rank.subject_id=subjects.id LEFT join cbse_exam_timetable_assessment_types on cbse_exam_timetable_assessment_types.cbse_exam_timetable_id= cbse_exam_timetable.id and cbse_exam_timetable_assessment_types.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id WHERE cbse_template.id=" . $this->db->escape($cbse_template_id) . " and student_session.id in (" . $students . ") order by cbse_student_template_rank.rank asc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getTemplateAssessment($cbse_template_id)
    {
        $sql = "SELECT cbse_template_term_exams.*,cbse_exams.cbse_exam_assessment_id,cbse_exam_assessments.name as `cbse_exam_assessment_name`,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_exams.name as `exam_name`,cbse_exam_assessment_types.name,cbse_exam_assessment_types.code,cbse_exam_assessment_types.maximum_marks ,cbse_exam_assessment_types.pass_percentage,cbse_template_terms.cbse_term_id,cbse_template_terms.weightage as `cbse_template_term_weightage`   FROM `cbse_template` INNER JOIN cbse_template_terms on cbse_template_terms.cbse_template_id=cbse_template.id INNER JOIN cbse_template_term_exams on cbse_template_term_exams.cbse_template_term_id=cbse_template_terms.id INNER JOIN `cbse_exams` on cbse_exams.id=cbse_template_term_exams.cbse_exam_id INNER JOIN cbse_exam_assessments on cbse_exam_assessments.id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exam_assessments.id WHERE cbse_template.id=" . $this->db->escape($cbse_template_id);

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getTemplateSingleExam($cbse_template_id)
    {
        $sql = "SELECT cbse_template_term_exams.*  FROM `cbse_template_term_exams`  WHERE cbse_template_term_exams.cbse_template_id=" . $this->db->escape($cbse_template_id);
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function searchTermStudentsByClass($cbse_observation_term_id, $class_id, $section_id)
    {
        $section_condition = "";
        if ($section_id != "") {
            $section_condition = "  and student_session.section_id=" . $this->db->escape($section_id);
        }

        $sql = "SELECT cbse_exams.*,cbse_exam_students.student_session_id,student_session.class_id,student_session.section_id, `classes`.`class`, `sections`.`id` AS `section_id`, `sections`.`section`, `students`.`id` as `student_id`, `students`.`admission_no`, `students`.`roll_no`, `students`.`admission_date`, `students`.`firstname`,`students`.`middlename`, `students`.`lastname`, `students`.`image`, `students`.`mobileno`, `students`.`email`, `students`.`state`, `students`.`city`, `students`.`pincode`, `students`.`religion`, `students`.`dob`, `students`.`current_address`, `students`.`permanent_address`, IFNULL(students.category_id, 0) as `category_id`, IFNULL(categories.category, '') as `category`, `students`.`adhar_no`, `students`.`samagra_id`, `students`.`bank_account_no`, `students`.`bank_name`, `students`.`ifsc_code`, `students`.`guardian_name`, `students`.`guardian_relation`, `students`.`guardian_phone`, `students`.`guardian_address`, `students`.`is_active`, `students`.`created_at`, `students`.`updated_at`, `students`.`father_name`, `students`.`rte`, `students`.`gender`,cbse_observation_term_student_subparameter.id as cbse_observation_term_student_subparameter_id,cbse_observation_subparameter.id as cbse_observation_subparameter_id,cbse_observation_terms.id as cbse_observation_term_id,cbse_observation_parameters.name as cbse_observation_parameter_name,cbse_observation_subparameter.cbse_exam_observation_id,cbse_observation_subparameter.cbse_observation_parameter_id,cbse_observation_term_student_subparameter.obtain_marks FROM `cbse_exams` INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join `students` ON `student_session`.`student_id` = `students`.`id`  INNER JOIN cbse_observation_terms on cbse_observation_terms.cbse_term_id=cbse_exams.cbse_term_id and cbse_observation_terms.id= " . $this->db->escape($cbse_observation_term_id) . " JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` JOIN `sections` ON `sections`.`id` = `student_session`.`section_id` LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id` INNER JOIN cbse_observation_subparameter on cbse_observation_subparameter.cbse_exam_observation_id =cbse_observation_terms.cbse_exam_observation_id LEFT JOIN cbse_observation_term_student_subparameter on cbse_observation_term_student_subparameter.cbse_ovservation_term_id=cbse_observation_terms.id and cbse_observation_term_student_subparameter.cbse_observation_subparameter_id = cbse_observation_subparameter.id and  cbse_observation_term_student_subparameter.student_session_id=cbse_exam_students.student_session_id inner join cbse_observation_parameters on cbse_observation_parameters.id =cbse_observation_subparameter.cbse_observation_parameter_id where cbse_exams.session_id=" . $this->current_session . " and student_session.class_id=" . $this->db->escape($class_id) . $section_condition . " GROUP by cbse_exam_students.student_session_id order by cbse_observation_subparameter.id desc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function searchStudentOverservationMarks($cbse_observation_term_id, $class_id, $section_id)
    {
        $section_condition = "";
        if ($section_id != "") {
            $section_condition = "  and student_session.section_id=" . $this->db->escape($section_id);
        }

        $sql = "SELECT cbse_exams.*,cbse_exam_students.student_session_id,student_session.class_id,student_session.section_id, `classes`.`class`, `sections`.`id` AS `section_id`, `sections`.`section`, `students`.`id` as `student_id`, `students`.`admission_no`, `students`.`roll_no`, `students`.`admission_date`, `students`.`firstname`,`students`.`middlename`, `students`.`lastname`, `students`.`image`, `students`.`mobileno`, `students`.`email`, `students`.`state`, `students`.`city`, `students`.`pincode`, `students`.`religion`, `students`.`dob`, `students`.`current_address`, `students`.`permanent_address`, IFNULL(students.category_id, 0) as `category_id`, IFNULL(categories.category, '') as `category`, `students`.`adhar_no`, `students`.`samagra_id`, `students`.`bank_account_no`, `students`.`bank_name`, `students`.`ifsc_code`, `students`.`guardian_name`, `students`.`guardian_relation`, `students`.`guardian_phone`, `students`.`guardian_address`, `students`.`is_active`, `students`.`created_at`, `students`.`updated_at`, `students`.`father_name`, `students`.`rte`, `students`.`gender`,cbse_observation_term_student_subparameter.id as cbse_observation_term_student_subparameter_id,cbse_observation_subparameter.id as cbse_observation_subparameter_id,cbse_observation_terms.id as cbse_observation_term_id,cbse_observation_parameters.name as cbse_observation_parameter_name,cbse_observation_subparameter.cbse_exam_observation_id,cbse_observation_subparameter.cbse_observation_parameter_id,cbse_observation_term_student_subparameter.obtain_marks FROM `cbse_exams` INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join `students` ON `student_session`.`student_id` = `students`.`id`  INNER JOIN cbse_observation_terms on cbse_observation_terms.cbse_term_id=cbse_exams.cbse_term_id and cbse_observation_terms.id= " . $this->db->escape($cbse_observation_term_id) . " JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` JOIN `sections` ON `sections`.`id` = `student_session`.`section_id` LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id` INNER JOIN cbse_observation_subparameter on cbse_observation_subparameter.cbse_exam_observation_id =cbse_observation_terms.cbse_exam_observation_id LEFT JOIN cbse_observation_term_student_subparameter on cbse_observation_term_student_subparameter.cbse_ovservation_term_id=cbse_observation_terms.id and cbse_observation_term_student_subparameter.cbse_observation_subparameter_id = cbse_observation_subparameter.id and  cbse_observation_term_student_subparameter.student_session_id=cbse_exam_students.student_session_id inner join cbse_observation_parameters on cbse_observation_parameters.id =cbse_observation_subparameter.cbse_observation_parameter_id where cbse_exams.session_id=" . $this->current_session . " and student_session.class_id=" . $this->db->escape($class_id) . $section_condition . " order by cbse_observation_subparameter.id desc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getTemplateAssessmentWithoutTerm($cbse_template_id)
    {
        $sql = "SELECT cbse_template_term_exams.*,cbse_exams.cbse_exam_assessment_id,cbse_exam_assessments.name as `cbse_exam_assessment_name`,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_exams.name as `exam_name`,cbse_exam_assessment_types.name,cbse_exam_assessment_types.code,cbse_exam_assessment_types.maximum_marks ,cbse_exam_assessment_types.pass_percentage  FROM `cbse_template`  INNER JOIN cbse_template_term_exams on cbse_template_term_exams.cbse_template_id=cbse_template.id INNER JOIN `cbse_exams` on cbse_exams.id=cbse_template_term_exams.cbse_exam_id INNER JOIN cbse_exam_assessments on cbse_exam_assessments.id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exam_assessments.id WHERE cbse_template.id=" . $this->db->escape($cbse_template_id);
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getexams()
    {
        return $this->db->select('*')->get('cbse_exams')->result_array();
    }

    public function get()
    {
        return $this->db->select('cbse_exams.cbse_term_id,cbse_terms.name as term_name')->join('cbse_terms', 'cbse_terms.id=cbse_exams.cbse_term_id')->group_by('cbse_term_id')->get('cbse_exams')->result_array();
    }

    public function get_editdetails($id)
    {
        $result['list'] = $this->db->select('*')->from('cbse_exams')->where('cbse_term_id', $id)->get()->result_array();
        return $result;
    }

    public function get_exambyId($id)
    {
        $result = $this->db->select('*')->from('cbse_exams')->where('id', $id)->get()->row_array();
        return $result;
    }

    public function getExamWithGrade($id)
    {
        $result = $this->db->select('*')->from('cbse_exams')->where('id', $id)->get()->row();
        $result->grades = $this->db->select('*')->from('cbse_exam_grades_range')->where('cbse_exam_grade_id', $result->cbse_exam_grade_id)->get()->result();
        return $result;
    }

    public function remove_exam($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('cbse_exams');
        $message = DELETE_RECORD_CONSTANT . " On cbse exams id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $id;
        }
    }

    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('cbse_term_id', $id);
        $this->db->delete('cbse_exams');
        $message = DELETE_RECORD_CONSTANT . " On cbse exams id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $id;
        }
    }

    public function add_exam_class_section($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('cbse_exam_class_sections', $data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On cbse exam class sections id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }

    public function searchExamStudents($exam_class_section, $exam_id)
    {
        $userdata = $this->customlib->getUserData();
            $role_id = $userdata["role_id"];
            $carray = array();
            $class_section_id=array();
            if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
                if ($userdata["class_teacher"] == 'yes') {
                    $classlist = $this->teacher_model->get_teacherrestricted_mode($userdata["id"]);
                }
                foreach ($classlist as $key => $value) {
                    $class_section=$this->teacher_model->get_teacherrestricted_modesections($userdata["id"], $value['id']);
                    $class_section_id[]=$class_section[0]['id'];
                }
            }

        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender,IFNULL(cbse_exam_students.id, 0) as exam_student_id')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->join('class_sections', 'class_sections.class_id=student_session.class_id and class_sections.section_id=student_session.section_id');
        $this->db->join('cbse_exam_students', 'cbse_exam_students.cbse_exam_id="' . $exam_id . '" and cbse_exam_students.student_session_id=student_session.id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);
         if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
        if(!empty($class_section_id)){
             $this->db->where_in('class_sections.id', $class_section_id);
        }
    }
        $this->db->where('students.is_active', 'yes');
        $this->db->where_in('class_sections.id', $exam_class_section);
        $this->db->order_by('students.admission_no');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getExamStudents($exam_id)
    {
        $this->db->select('cbse_student_exam_ranks.rank,classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');

        $this->db->join('class_sections', 'class_sections.class_id=student_session.class_id and class_sections.section_id=student_session.section_id');
        $this->db->join('cbse_exam_students', 'cbse_exam_students.student_session_id= student_session.id and cbse_exam_students.cbse_exam_id=' . $exam_id);
        $this->db->join('cbse_student_exam_ranks', 'cbse_student_exam_ranks.student_session_id = student_session.id and cbse_student_exam_ranks.cbse_exam_id=' . $exam_id, 'left');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('students.is_active', 'yes');
        $this->db->order_by('cbse_student_exam_ranks.rank');
        $query = $this->db->get();
        return $query->result();
    }

    public function getExamByGrade($cbse_term_id)
    {
        $exams = $this->db->select('*')->from('cbse_exams')->where('cbse_term_id', $cbse_term_id)->get()->result();
        return $exams;
    }

    public function getClassByExam($exam_id)
    {
        $this->db->select('cbse_exam_class_sections.*,class_sections.class_id,classes.class');
        $this->db->from('cbse_exam_class_sections');
        $this->db->join('class_sections', 'class_sections.id = cbse_exam_class_sections.class_section_id');
        $this->db->join('classes', 'classes.id = class_sections.class_id');
        $this->db->where('cbse_exam_id', $exam_id);
        $this->db->group_by('classes.id');
        $exams = $this->db->get();
        $result = $exams->result();

        return $result;
    }

    public function getExamSectionByClass($exam_id, $class_id)
    {
        $this->db->select('cbse_exam_class_sections.*,class_sections.class_id,classes.class,sections.id as `section_id`,sections.section');
        $this->db->from('cbse_exam_class_sections');
        $this->db->join('class_sections', 'class_sections.id = cbse_exam_class_sections.class_section_id');
        $this->db->join('classes', 'classes.id = class_sections.class_id and classes.id = ' . $class_id);
        $this->db->join('sections', 'sections.id = class_sections.section_id');
        $this->db->where('cbse_exam_id', $exam_id);
        $exams = $this->db->get();
        $result = $exams->result();
        return $result;
    }

    public function get_class_sectionbyexamid($id)
    {
        $class_sections = $this->db->select('*')->from('cbse_exam_class_sections')->where('cbse_exam_id', $id)->get()->result_array();
        foreach ($class_sections as $key => $value) {
            $class_section[] = $value['class_section_id'];
        }
        return $class_section;
    }

    public function add_student($insert_array, $exam_id, $all_students)
    {
        $delete_array = array();
        $new_inserted_array = array('0');
        $this->db->trans_begin();
        if (!empty($insert_array)) {
            foreach ($insert_array as $insert_key => $insert_value) {
                $this->insert($insert_value);
                $new_inserted_array[] = $insert_value['student_session_id'];
            }
        }

        if (!empty($new_inserted_array)) {
            $this->db->where('cbse_exam_id', $exam_id);
            $this->db->where_not_in('student_session_id', $new_inserted_array);
            $this->db->delete('cbse_exam_students');
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function insert($insert_value)
    {
        $this->db->where('cbse_exam_id', $insert_value['cbse_exam_id']);
        $this->db->where('student_session_id', $insert_value['student_session_id']);
        $q = $this->db->get('cbse_exam_students');

        if ($q->num_rows() == 0) {
            $this->db->insert('cbse_exam_students', $insert_value);
        }
        return true;
    }

    public function getexamdetails($exam_id)
    {
        return $this->db->select('cbse_exams.*,CONCAT(classes.class, ": ", GROUP_CONCAT(sections.section ORDER BY sections.section ASC SEPARATOR ",")) AS class_sections,cbse_terms.name as term_name')->from('cbse_exams')->join('cbse_exam_class_sections', 'cbse_exam_class_sections.cbse_exam_id=cbse_exams.id')->join('class_sections', 'class_sections.id=cbse_exam_class_sections.class_section_id')->join('classes', 'classes.id=class_sections.class_id')->join('sections', 'sections.id=class_sections.section_id')->join('cbse_terms', 'cbse_terms.id=cbse_exams.cbse_term_id', 'left')->where('cbse_exams.id', $exam_id)->group_by('cbse_exams.id')->get()->row_array();
    }

    public function add_examsubject($insert_array, $update_array, $not_be_del, $exam_id, $timetable_prev_rows, $assessment_array, $prev_assessment_array)
    {
        $this->db->trans_begin();
        // ====================
        $n_array = [];

        foreach ($assessment_array as $as_key => $as_value) {
            foreach ($as_value as $tkey => $tvalue) {
                $n_array[] = $tvalue;
            }
        }

        $delete_assessments = (array_diff($prev_assessment_array, $n_array));

        if (!empty($delete_assessments)) {
            foreach ($delete_assessments as $d_a_key => $d_a_value) {
                $a = explode("_", $d_a_value);
                $this->db->where('cbse_exam_timetable_id', $a[0]);
                $this->db->where('cbse_exam_assessment_type_id', $a[1]);
                $this->db->delete('cbse_exam_timetable_assessment_types');
            }
        }

        if (!empty($insert_array)) {
            foreach ($insert_array as $insert_key => $insert_value) {
                $insert_assessments = $insert_array[$insert_key]['assessment'];
                unset($insert_array[$insert_key]['assessment']);
                $this->db->insert('cbse_exam_timetable', $insert_array[$insert_key]);
                $inserted_id = $this->db->insert_id();
                $not_be_del[] = $inserted_id;

                foreach ($insert_value['assessment'] as $a_key => $a_value) {

                    $insert_as = explode("_", $a_value);
                    $n = [
                        'cbse_exam_timetable_id' => $inserted_id,
                        'cbse_exam_assessment_type_id' => $insert_as[1]
                    ];

                    $this->db->insert('cbse_exam_timetable_assessment_types', $n);
                }
            }
        }

        if (!empty($update_array)) {
            foreach ($update_array as $up_key => $up_value) {

                foreach ($up_value['assessment'] as $a_key => $a_value) {

                    $insert_as = explode("_", $a_value);
                    $n = [
                        'cbse_exam_timetable_id' => $insert_as[0],
                        'cbse_exam_assessment_type_id' => $insert_as[1]
                    ];
                    $if_exists = $this->find_assessment_exists($prev_assessment_array, $a_value);

                    if (!$if_exists) {
                        $this->db->insert('cbse_exam_timetable_assessment_types', $n);
                    }
                }
                unset($update_array[$up_key]['assessment']);
            }

            $this->db->update_batch('cbse_exam_timetable', $update_array, 'id');
        }

        if (!empty($not_be_del)) {
            $this->db->where('cbse_exam_id', $exam_id);
            $this->db->where_not_in('id', $not_be_del);
            $this->db->delete('cbse_exam_timetable');
        }
        //====================

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function find_assessment_exists($assessment_array, $find_value)
    {
        if (in_array($find_value, $assessment_array)) {
            return true;
        }
        return false;
    }

    public function getexamsubjects($exam_id)
    {
		$subject_condition = 0;
        $userdata = $this->customlib->getUserData();
		if(isset($userdata)){
			$role_id = $userdata["role_id"];
			if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
				if ($userdata["class_teacher"] == 'yes') {
					$my_classes = $this->teacher_model->my_classes($userdata['id']);        
					if (!empty($my_classes)) {
						$subject_condition = 0;
					} else {
						$subject_condition = 1;                    
					}
					$my_subjects = $this->get_examsubjects($userdata['id']);
				}
			}
		}
		
		if (!empty($my_subjects)) {

			foreach($my_subjects as $key=>$value){
				$my_subjects[]=$value;
			}				
				return $this->db->select('cbse_exam_timetable.*,subjects.name as subject_name,subjects.code as subject_code')
				->from('cbse_exam_timetable')
				->join('subjects', 'subjects.id=cbse_exam_timetable.subject_id')
				->where_in('subjects.id', $my_subjects)
				->where('cbse_exam_id', $exam_id)->get()->result();
				
        }elseif($subject_condition == 1 && empty($my_subjects)){
               
             return array();
			 
        }else{
				
                return $this->db->select('cbse_exam_timetable.*,subjects.name as subject_name,subjects.code as subject_code')
				->from('cbse_exam_timetable')
				->join('subjects', 'subjects.id=cbse_exam_timetable.subject_id')			 
				->where('cbse_exam_id', $exam_id)->get()->result();
				
				 
           
        }		
        
    }

    public function getexamSubjectswithAssessment($exam_id, $assessments)
    {
        $assessment_sql_additional = [];
        $assessment_variables = [];
        if (!empty($assessments)) {
            foreach ($assessments as $assessment_key => $assessment_value) {

                $assessment_sql_additional[] = "LEFT join cbse_exam_timetable_assessment_types as assess_type_" . $assessment_value->id . " on assess_type_" . $assessment_value->id . ".cbse_exam_timetable_id=cbse_exam_timetable.id and assess_type_" . $assessment_value->id . ".cbse_exam_assessment_type_id=" . $assessment_value->id;
                $assessment_variables[] = "assess_type_" . $assessment_value->id . ".id as assess_type_" . $assessment_value->id . " , assess_type_" . $assessment_value->id . ".cbse_exam_timetable_id as cbse_exam_timetable_id_" . $assessment_value->id . " , assess_type_" . $assessment_value->id . ".cbse_exam_assessment_type_id as cbse_exam_assessment_type_id_" . $assessment_value->id;
            }
        }
        $additional_sql = implode(" ", $assessment_sql_additional);
        $additional_assessment_variables = implode(" ,", $assessment_variables);

        $sql = "SELECT cbse_exam_timetable.*,`subjects`.`name` as `subject_name`, `subjects`.`code` as `subject_code`,cbse_exams.cbse_exam_assessment_id, " . $additional_assessment_variables . " from cbse_exam_timetable INNER join cbse_exams on cbse_exams.id=cbse_exam_timetable.cbse_exam_id  
        JOIN `subjects` ON `subjects`.`id`=`cbse_exam_timetable`.`subject_id` " . $additional_sql . " WHERE cbse_exam_timetable.cbse_exam_id=" . $exam_id . " order by cbse_exam_timetable.date asc, cbse_exam_timetable.time_from asc, subjects.name asc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getExamTimetable()
    {

        $userdata = $this->customlib->getUserData();
            $role_id = $userdata["role_id"];
            $carray = array();
            $class_section_id=array();
            if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
                if ($userdata["class_teacher"] == 'yes') {
                    $classlist = $this->teacher_model->get_teacherrestricted_mode($userdata["id"]);
                }
                foreach ($classlist as $key => $value) {
                    $class_section=$this->teacher_model->get_teacherrestricted_modesections($userdata["id"], $value['id']);
                    $class_section_id[]=$class_section[0]['id'];
                }
            }
        $this->db->select('cbse_exams.*')
            ->from('cbse_exams')->join('cbse_exam_class_sections', 'cbse_exam_class_sections.cbse_exam_id=cbse_exams.id');
             if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
        if(!empty($class_section_id)){
             $this->db->where_in('cbse_exam_class_sections.class_section_id', $class_section_id);
        }
    }
           $exams =  $this->db->where('cbse_exams.is_active', 1)
            ->where('cbse_exams.session_id', $this->current_session)
            ->order_by('cbse_exams.id', 'desc')
			->group_by('cbse_exams.id')
            ->get()->result();

        if (!empty($exams)) {
            foreach ($exams as $exam_key => $exam_value) {

                $exams[$exam_key]->{"time_table"} = $this->db->select('cbse_exam_timetable.*,subjects.name as subject_name,subjects.code as subject_code')
                    ->from('cbse_exam_timetable')->join('subjects', 'subjects.id=cbse_exam_timetable.subject_id')

                    ->where('cbse_exam_timetable.cbse_exam_id', $exam_value->id)

                    ->get()
                    ->result();
            }
        }

        return $exams;
    }

    public function getStudentExamTimetable($student_session_id)
    {
        $student_exam = $this->db->select('cbse_exam_students.*,cbse_exams.name,cbse_exams.exam_code')
            ->from('cbse_exam_students')->join('cbse_exams', 'cbse_exams.id=cbse_exam_students.cbse_exam_id')
            ->where('student_session_id', $student_session_id)
            ->where('cbse_exams.session_id', $this->current_session)
            ->where('cbse_exams.is_active', 1)
            ->order_by('cbse_exams.id', 'desc')
            ->get()->result();
        if (!empty($student_exam)) {
            foreach ($student_exam as $exam_key => $exam_value) {

                $student_exam[$exam_key]->{"time_table"} = $this->db->select('cbse_exam_timetable.*,subjects.name as subject_name,subjects.code as subject_code')
                    ->from('cbse_exam_timetable')->join('subjects', 'subjects.id=cbse_exam_timetable.subject_id')
                    ->where('cbse_exam_id', $exam_value->cbse_exam_id)
                    ->get()
                    ->result();
            }
        }

        return $student_exam;
    }

    public function getStudentexamSubjectsResult($exam_id, $cbse_exam_student_id)
    {
        return $this->db->select('cbse_exam_timetable.*, `subjects`.`name` as `subject_name`, `cbse_student_subject_result`.`id` as `cbse_student_subject_result_id`, `cbse_exams`.`name` as `exam_name`, `cbse_exam_assessments`.`name` as `cbse_exam_assessments_name`, `cbse_exam_assessment_types`.`name` as `cbse_exam_assessment_type_name`,cbse_student_subject_marks.id as cbse_student_subject_mark_id,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,IFNULL( `cbse_student_subject_marks`.`mark`,0) as mark,cbse_exam_assessment_types.pass_percentage,cbse_exam_assessment_types.maximum_marks,cbse_exam_assessment_types.id as cbse_exam_assessment_type_id')
            ->from('cbse_exam_timetable')
            ->join('cbse_exams', 'cbse_exams.id=cbse_exam_timetable.cbse_exam_id')
            ->join('cbse_exam_assessments', 'cbse_exam_assessments.id=cbse_exams.cbse_exam_assessment_id')
            ->join('cbse_exam_assessment_types', 'cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exam_assessments.id')
            ->join('subjects', 'subjects.id=cbse_exam_timetable.subject_id')
            ->join('cbse_student_subject_result', 'cbse_student_subject_result.cbse_exam_timetable_id=cbse_exam_timetable.id and `cbse_student_subject_result`.`cbse_exam_student_id` =' . $cbse_exam_student_id, 'LEFT')
            ->join('cbse_student_subject_marks', 'cbse_student_subject_marks.cbse_student_subject_result_id=cbse_student_subject_result.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id', 'LEFT')
            ->where('cbse_exam_id', $exam_id)
            ->get()
            ->result();
    }

    public function getStudentExamByStudentSession($student_session_id)
    {
        return $this->db->select('cbse_exam_students.*,cbse_exams.cbse_exam_assessment_id,cbse_exams.cbse_term_id,cbse_exams.name,cbse_exams.use_exam_roll_no,cbse_exams.is_active,cbse_exams.is_publish,cbse_exams.cbse_term_id,cbse_exams.cbse_exam_grade_id,cbse_exams.total_working_days')
            ->from('cbse_exam_students')
            ->join('student_session', 'student_session.id=cbse_exam_students.student_session_id')
            ->join('students', 'students.id=student_session.student_id')
            ->join('cbse_exams', 'cbse_exam_students.cbse_exam_id=cbse_exams.id')
            ->where('cbse_exam_students.student_session_id', $student_session_id)
            ->where('cbse_exams.is_publish', '1')
            ->order_by('cbse_exams.created_at', 'desc')
            ->get()->result();
    }

    public function get_examstudents($exam_id)
    {
         $userdata = $this->customlib->getUserData();
            $role_id = $userdata["role_id"];
            $carray = array();
            $class_section_id=array();
            if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
                if ($userdata["class_teacher"] == 'yes') {
                    $classlist = $this->teacher_model->get_teacherrestricted_mode($userdata["id"]);
                }
                foreach ($classlist as $key => $value) {
                    $class_section=$this->teacher_model->get_teacherrestricted_modesections($userdata["id"], $value['id']);
                    $class_section_id[]=$class_section[0]['id'];
                }
            }
         $this->db->select('students.*,cbse_exam_students.id as exam_student_id,  cbse_exams.total_working_days,cbse_exam_students.total_present_days,cbse_exam_students.roll_no as `exam_roll_no`,classes.class as class_name,sections.section as section_name')
            ->from('cbse_exam_students')
            ->join('cbse_exams', 'cbse_exams.id=cbse_exam_students.cbse_exam_id')
            ->join('cbse_exam_class_sections', 'cbse_exam_class_sections.cbse_exam_id=cbse_exams.id')
            ->join('staff', 'staff.id=cbse_exam_students.staff_id', 'left')
            ->join('student_session', 'student_session.id=cbse_exam_students.student_session_id')
            ->join('students', 'students.id=student_session.student_id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->where('cbse_exam_students.cbse_exam_id', $exam_id);
             if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
        if(!empty($class_section_id)){
             $this->db->where_in('cbse_exam_class_sections.class_section_id', $class_section_id);
        }
    }
           return $this->db->group_by('cbse_exam_students.id')->get()->result_array();
    }

    public function get_markexamstudents($timetable_id)
    {
        $result = array();
        $student_data = $this->db->select('students.*,cbse_exam_students.id as exam_student_id,cbse_exam_timetable.id as cbse_exam_timetable_id,cbse_exam_students.roll_no as `exam_roll_no`,classes.class as class_name,sections.section as section_name')
            ->from('cbse_exam_students')
            ->join('student_session', 'student_session.id=cbse_exam_students.student_session_id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->join('students', 'students.id=student_session.student_id')
            ->join('cbse_exam_timetable', 'cbse_exam_timetable.cbse_exam_id=cbse_exam_students.cbse_exam_id')
            ->where('cbse_exam_timetable.id', $timetable_id)
            ->get()->result_array();

        foreach ($student_data as $key => $value) {
            $cbse_student_subject_marks = $this->db->select('cbse_student_subject_marks.*')
                ->from('cbse_student_subject_marks')
                ->where(array('cbse_student_subject_marks.cbse_exam_timetable_id' => $value['cbse_exam_timetable_id'], ' `cbse_student_subject_marks`.`cbse_exam_student_id` ' => $value['exam_student_id']))
                ->get()
                ->result_array();
            $student_subject_marks = array();

            foreach ($cbse_student_subject_marks as $mkey => $mvalue) {
                $student_subject_marks[$mvalue['cbse_exam_assessment_type_id']] = $mvalue;
            }
            $result[$value['id']] = $value;
            $result[$value['id']]['marks'] = $student_subject_marks;
        }
        return $result;
    }

    public function get_exam_subject_assessment_types($exam_assessment_id, $timetable_id)
    {
        $this->db->select('cbse_exam_assessment_types.*,cbse_exam_timetable_assessment_types.id as cbse_exam_timetable_assessment_type_id');
        $this->db->from('cbse_exam_assessment_types');
        $this->db->join('cbse_exam_timetable_assessment_types', 'cbse_exam_timetable_assessment_types.cbse_exam_timetable_id=' . $timetable_id . ' and cbse_exam_timetable_assessment_types.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id');
        $this->db->where('cbse_exam_assessment_types.cbse_exam_assessment_id', $exam_assessment_id);
        $result = $this->db->get();
        return $result->result();
    }

    public function get_exam_assessment_types($exam_assessment_id)
    {
        return $this->db->select('*')->from('cbse_exam_assessment_types')->where('cbse_exam_assessment_id', $exam_assessment_id)->get()->result();
    }

    public function addresult_data($result_data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($result_data['id']) && !empty($result_data['id'])) {
            $this->db->where('id', $result_data['id']);
            $this->db->update('cbse_student_subject_result', $result_data);
            $message = UPDATE_RECORD_CONSTANT . " On cbse student subject result id " . $result_data['id'];
            $action = "Update";
            $record_id = $result_data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('cbse_student_subject_result', $result_data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On cbse student subject result id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }

        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getresult_data($subject_id, $student_id)
    {
        $this->db->select('cbse_student_subject_result.id');
        $this->db->from('cbse_student_subject_result');
        $this->db->where('cbse_student_subject_result.subject_id', $subject_id);
        $this->db->where('cbse_student_subject_result.cbse_exam_student_id', $student_id);
        $result = $this->db->get();
        return $result->row_array();
    }

    public function addresultmark_data($result_mark, $cbse_exam_timetable_id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        if (isset($result_mark['id']) && !empty($result_mark['id'])) {
            $this->db->where('id', $result_mark['id']);
            $this->db->update('cbse_student_subject_marks', $result_mark);
            $message = UPDATE_RECORD_CONSTANT . " On cbse student subject marks id " . $result_mark['id'];
            $action = "Update";
            $record_id = $result_mark['id'];
            $this->log($message, $record_id, $action);
        } else {
            $insert_ids = [];
            foreach ($result_mark as $mark_key => $mark_value) {
                $this->db->insert('cbse_student_subject_marks', $mark_value);
                $insert_id = $this->db->insert_id();
                $insert_ids[] = $insert_id;
            }
            if (!empty($insert_ids)) {
                $this->db->where('cbse_exam_timetable_id', $cbse_exam_timetable_id);
                $this->db->where_not_in('id', $insert_ids);
                $this->db->delete('cbse_student_subject_marks');
            }
        }

        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    public function add_exam_student_attendance($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well

        $this->db->insert('cbse_exam_student_attendance', $data);
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On cbse exam student attendance id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }

    public function add_exam_attendance($data)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_exam_attendance', $data);
            $insert_id = $data['id'];
        } else {
            $this->db->insert('cbse_exam_attendance', $data);
            $insert_id = $this->db->insert_id();
        }

        return $insert_id;
    }

    public function delete_exam_student_attendance($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('cbse_exam_attendance_id', $data['cbse_exam_attendance_id']);
        $this->db->delete('cbse_exam_student_attendance');
        $message   = DELETE_RECORD_CONSTANT . " On cbse exam student attendance where cbse exam attendance id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function getexamattendance($exam_id)
    {
        $this->db->select('id, cbse_exam_id');
        $this->db->from('cbse_exam_attendance');
        $this->db->where('cbse_exam_attendance.cbse_exam_id', $exam_id);
        $result = $this->db->get();
        return $result->row_array();
    }

    public function get_teacher_remark($exam_id)
    {
        $sql = "SELECT  `students`.*, `a`.`id` as `exam_student_id`, `a`.`remark`, `classes`.`class` as `class_name`, `sections`.`section` as `section_name` ,(SELECT sum(cbse_student_subject_marks.marks) FROM `cbse_exams` INNER join cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id  INNER join cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER join cbse_exam_assessments on cbse_exam_assessments.id=cbse_exams.cbse_exam_assessment_id INNER join cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exam_assessments.id left join `cbse_student_subject_marks` on  cbse_student_subject_marks.`cbse_exam_timetable_id` = cbse_exam_timetable.id  AND cbse_student_subject_marks.`cbse_exam_student_id` = cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id where cbse_exams.id=" . $this->db->escape($exam_id) . " and cbse_exams.session_id=" . $this->current_session . " and student_session_id=a.student_session_id  ORDER BY cbse_exam_timetable.subject_id asc, cbse_exam_assessment_types.id desc)  as gain_total_marks, (SELECT sum(cbse_exam_assessment_types.maximum_marks) FROM `cbse_exams` INNER join cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id  INNER join cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER join cbse_exam_assessments on cbse_exam_assessments.id=cbse_exams.cbse_exam_assessment_id INNER join cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exam_assessments.id left join `cbse_student_subject_marks` on  cbse_student_subject_marks.`cbse_exam_timetable_id` = cbse_exam_timetable.id  AND cbse_student_subject_marks.`cbse_exam_student_id` = cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id where cbse_exams.id=" . $this->db->escape($exam_id) . " and cbse_exams.session_id=" . $this->current_session . " and student_session_id=a.student_session_id  ORDER BY cbse_exam_timetable.subject_id asc, cbse_exam_assessment_types.id desc)  as total_marks FROM `cbse_exams` INNER join cbse_exam_students a on a.cbse_exam_id=cbse_exams.id inner JOIN `student_session` ON `student_session`.`id`=a.`student_session_id` JOIN `students` ON `students`.`id`=`student_session`.`student_id` JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` JOIN `sections` ON `sections`.`id` = `student_session`.`section_id` where cbse_exams.id=" . $this->db->escape($exam_id) . " and cbse_exams.session_id=" . $this->current_session . " order by students.firstname asc";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getremarkbyexamid($exam_id)
    {
        $this->db->select('id, cbse_exam_student_id');
        $this->db->from('cbse_teacher_remarks');
        $this->db->where('cbse_teacher_remarks.cbse_exam_student_id', $exam_id);
        $result = $this->db->get();
        return $result->row_array();
    }

    public function addteacherremark($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_teacher_remarks', $data);
            $message = UPDATE_RECORD_CONSTANT . " On cbse teacher remarks id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $insert_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('cbse_teacher_remarks', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On cbse teacher remarks id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }

    public function get_classsectionbyId($exam_id)
    {
        $this->db->select('cbse_exam_class_sections.class_section_id, classes.id as class_id');
        $this->db->from('cbse_exam_class_sections');
        $this->db->join('class_sections', 'class_sections.id=cbse_exam_class_sections.class_section_id');
        $this->db->join('classes', 'classes.id=class_sections.class_id');
        $this->db->where('cbse_exam_class_sections.cbse_exam_id', $exam_id);
        $result = $this->db->get();
        return $result->result_array();
    }

    public function removeclasssection($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('cbse_exam_id', $id);
        $this->db->delete('cbse_exam_class_sections');
        $message = DELETE_RECORD_CONSTANT . " On cbse exam class sections id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

	public function getSubject($id = null) {

        $subject_condition = 0;
        $userdata = $this->customlib->getUserData();
        $my_subjects=array();
        $role_id = $userdata["role_id"];


        if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
            if ($userdata["class_teacher"] == 'yes') {



                $my_classes = $this->teacher_model->my_classes($userdata['id']);

		
                if (!empty($my_classes)) {
                    $subject_condition = 0;
                } else {
                    $subject_condition = 1;
                    
                }
		$my_subjects = $this->get_examsubjects($userdata['id']);
            }
        }
        
       
           
            if (!empty($my_subjects)) {

foreach($my_subjects as $key=>$value){
$my_subjects[]=$value;
}

                 $this->db->select()->from('subjects');
                $this->db->where_in('subjects.id', $my_subjects);
                $this->db->order_by('id');
                 $query = $this->db->get();
                return $query->result_array(); 
            }elseif($subject_condition == 1 && empty($my_subjects)){
               
             return array();
            }else{
                 $this->db->select()->from('subjects');
                 $this->db->order_by('id');
                 $query = $this->db->get();
                 return $query->result_array(); 
            }
            
        
        
       
    }
  public function get_examsubjects($staff_id)
    {
	
        $subject_id = array();
	$class_teacher_sections      = $this->db->query("SELECT class_id, section_id FROM `class_teacher` where staff_id=".$staff_id." and session_id=" . $this->current_session);
        $class_teacher_subjectquerydata = $class_teacher_sections->result_array();
	
	 foreach ($class_teacher_subjectquerydata as $cskey => $csvalue) {
	$sclass_id=$csvalue['class_id'];
	$ssection_id=$csvalue['section_id'];
	$class_teacher_subject     = $this->db->query("select sgs.subject_id  from class_sections cs inner join subject_group_class_sections sgcs on sgcs.class_section_id=cs.id inner join subject_group_subjects sgs on sgs.subject_group_id=sgcs.subject_group_id where cs.class_id='".$sclass_id."' and cs.section_id='".$ssection_id."' and sgs.session_id='" . $this->current_session . "' ");
        $class_teacher_subjectquerydata = $class_teacher_subject->result_array();
         foreach ($class_teacher_subjectquerydata as $ctskey => $ctsvalue) {
            $subject_id[$ctsvalue['subject_id']] = $ctsvalue['subject_id'];
        }  

        }



        $query      = $this->db->query("select sgs.subject_id  from subject_timetable st inner join subject_group_subjects sgs on st.subject_group_subject_id=sgs.id where st.staff_id='" . $staff_id . "' and st.session_id='" . $this->current_session . "' ");
        $querydata = $query->result_array();
        foreach ($querydata as $key => $value) {
            $subject_id[$value['subject_id']] = $value['subject_id'];
        }

        return $subject_id;
    }
}
