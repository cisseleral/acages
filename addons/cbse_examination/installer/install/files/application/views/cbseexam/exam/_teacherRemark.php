<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/cbse-exam.css">
<form method="post" role="form" id="addTeacherRemark">
    <?php
    if (isset($resultlist) && !empty($resultlist)) {        
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <div class="scroll-area overflow-auto">
                        <table class="table table-striped">
                            <thead class="heading-sticky">
                                <tr class="active">
                                    <th><?php echo $this->lang->line('admission_no'); ?></th>
                                    <th><?php echo $this->lang->line('roll_no'); ?></th>
                                    <th><?php echo $this->lang->line('class'); ?></th>
                                    <th><?php echo $this->lang->line('section'); ?></th>
                                    <th><?php echo $this->lang->line('student_name'); ?></th>                                
                                    <th><?php echo $this->lang->line('gender'); ?></th>
                                    <th><?php echo $this->lang->line('marks'); ?><br/><?php echo "(".two_digit_float($total_exam_marks).")"; ?></th>
                                    <th><?php echo $this->lang->line('remark') ?></th> 
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($resultlist)) {
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>
                                    </tr>
                                    <?php
                                } else {
                                    
                                    foreach ($resultlist as $student) {

                                    ?>
                                    <tr class="cbse_exam_student_id_<?php echo $student['exam_student_id']; ?>">                  
                                        <td><?php echo $student['admission_no']; ?></td>
                                        <td><?php echo $student['roll_no']; ?></td>
                                        <td><?php echo $student['class_name']; ?></td>
                                        <td><?php echo $student['section_name']; ?></td>                                
                                        <td><?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname);?></td>
                                                                 
                                        <td><?php echo $this->lang->line(strtolower($student['gender'])); ?></td>     
                                        <td><?php echo is_null($student['gain_total_marks']) ? '<span class="text text-danger">'.$this->lang->line('n_a').'</span>' : $student['gain_total_marks']; ?></td>   
                                        <td class="white-space-nowrap"> 
                                            <input type="hidden" class="marksssss form-control w-sm-150" name="exam_student_id[]" value="<?php echo $student['exam_student_id']; ?>">
                                           
                                            <input type="text" class="marksssss form-control w-sm-150" name="teacher_remark[<?php echo $student['exam_student_id']; ?>]" value="<?php echo $student['remark']; ?>">
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>    
                </div>

                <?php if ($this->rbac->hasPrivilege('cbse_exam_teacher_remark', 'can_edit')) { ?>
                    <div class="modal-footer clearboth mx-nt-lr-15 pb0">
                        <button type="submit" class="allot-fees btn btn-primary pull-right" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait.."><?php echo $this->lang->line('save'); ?>
                        </button>
                    </div>    
                <?php } ?>
            </div>
        </div>
        <?php
    } else {
        ?>

        <div class="alert alert-info">
            <?php echo $this->lang->line('no_record_found'); ?>
        </div>
        <?php
    }
    ?>
</form>

<script>
(function ($){
    "use strict";
    
    $("#addTeacherRemark").on('submit', (function (e) {
        e.preventDefault();
        var $this = $(this).find("button[type=submit]:focus");
       
        $.ajax({
            url: base_url+"cbseexam/exam/addteacherremark",
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $this.button('loading');
            },
            success: function (res)
            {
                window.location.reload(true);
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }
        });
    }));
    
})(jQuery);
</script>