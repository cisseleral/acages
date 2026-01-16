<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-file-text-o"></i> Bulletin de Notes</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> Sélectionner les critères</h3>
                    </div>
                    <div class="box-body">
                        <form role="form" action="<?php echo site_url('admin/examresult/bulletin') ?>" method="post" class="row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Groupe d'examen</label><small class="req"> *</small>
                                    <select id="exam_group_id" name="exam_group_id" class="form-control select2" required>
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($examgrouplist as $examgroup) { ?>
                                            <option value="<?php echo $examgroup->id ?>" <?php echo set_select('exam_group_id', $examgroup->id); ?>><?php echo $examgroup->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Examen</label><small class="req"> *</small>
                                    <select id="exam_id" name="exam_id" class="form-control select2" required>
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Session</label><small class="req"> *</small>
                                    <select id="session_id" name="session_id" class="form-control select2" required>
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($sessionlist as $session) { ?>
                                            <option value="<?php echo $session['id'] ?>" <?php echo set_select('session_id', $session['id'], ($session['is_active'] == 1)); ?>><?php echo $session['session'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Classe</label><small class="req"> *</small>
                                    <select id="class_id" name="class_id" class="form-control select2" required>
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($classlist as $class) { ?>
                                            <option value="<?php echo $class['id'] ?>" <?php echo set_select('class_id', $class['id']); ?>><?php echo $class['class'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label>Section</label><small class="req"> *</small>
                                    <select id="section_id" name="section_id" class="form-control select2" required>
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> Rechercher</button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (isset($studentList)) { ?>
                <form action="<?php echo site_url('admin/examresult/print_bulletin') ?>" method="post" target="_blank">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <input type="hidden" name="exam_id" value="<?php echo $this->input->post('exam_id'); ?>">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Liste des élèves</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select_all" /></th>
                                        <th>Matricule</th>
                                        <th>Nom et Prénom</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php if (!empty($studentList)) { 
    foreach ($studentList as $student) { ?>
    <tr>
        <td>
            <input type="checkbox" name="student_ids[]" value="<?php echo $student->student_id; ?>" class="checkbox">
        </td>
        <td><?php echo $student->admission_no; ?></td>
        <td>
            <?php echo $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname); ?>
        </td>
        <td><?php echo $student->father_name; ?></td>
        <td>
            <?php 
            if ($student->dob != "0000-00-00" && $student->dob != "") {
                echo date($this->customlib->getSchoolDateFormat(), strtotime($student->dob));
            }
            ?>
        </td>
        <td><?php echo $student->gender; ?></td>
    </tr>
<?php } } ?>
                                </tbody>
                            </table>
                                <button type="submit" name="search" value="search_full" class="btn btn-primary btn-sm checkbox-toggle">
                                    <i class="fa fa-print"></i> <?php echo $this->lang->line('generate_bulletin'); ?>
                                </button>

                                <button type="submit" name="search" value="generate_ar" class="btn btn-warning btn-sm checkbox-toggle">
                                    <i class="fa fa-file-pdf-o"></i> <?php echo $this->lang->line('generate_bulletin_ar'); ?>
                                </button>
                                <button type="submit" name="search" value="generate_class_report" class="btn btn-info btn-sm">
                                    <i class="fa fa-list-alt"></i> Class Exam Result (Landscape)
                                </button>
                        </div>
                    </div>
                </form>
                <?php } ?>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
$(document).ready(function () {
    var base_url = '<?php echo base_url(); ?>';

    // 1. Get Exams for Group
    $(document).on('change', '#exam_group_id', function () {
        var exam_group_id = $(this).val();
        $('#exam_id').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
        if (exam_group_id != "") {
            $.ajax({
                type: "POST",
                url: base_url + "admin/examgroup/getExamByExamgroup",
                data: {'exam_group_id': exam_group_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj) {
                        $('#exam_id').append("<option value='" + obj.id + "'>" + obj.exam + "</option>");
                    });
                }
            });
        }
    });

    // 2. Get Sections for Class
    $(document).on('change', '#class_id', function () {
        var class_id = $(this).val();
        $('#section_id').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
        if (class_id != "") {
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj) {
                        $('#section_id').append("<option value='" + obj.section_id + "'>" + obj.section + "</option>");
                    });
                }
            });
        }
    });

    // 3. Select All Toggle
    $('#select_all').on('click', function() {
        $('.checkbox').prop('checked', this.checked);
    });
});
</script>