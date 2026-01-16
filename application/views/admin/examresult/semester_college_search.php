<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.min.js"></script>

<style>
    .multiselect-native-select .btn-group { width: 100%; }
    .multiselect { text-align: left; background: #fff; border: 1px solid #ccc; height: 34px; }
    .multiselect-container { width: 100% !important; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-map-o"></i> Bulletin Semestre Collège</h1>
    </section>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> Sélectionner les critères</h3>
            </div>
            <div class="box-body">
                <form role="form" action="<?php echo site_url('admin/examresult/btn_sem_college') ?>" method="post" class="row">
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
                            <label>Sélectionner Devoirs (Moyenne)</label><small class="req"> *</small><br/>
                            <select id="devoir_exams" name="devoir_exams[]" multiple="multiple">
                                <?php if(isset($examList)){
                                    foreach($examList as $ex){
                                        $selected = (isset($devoir_exams) && in_array($ex->id, $devoir_exams)) ? 'selected' : '';
                                        echo "<option value='".$ex->id."' ".$selected.">".$ex->exam."</option>";
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Sélectionner Composition</label><small class="req"> *</small>
                            <select id="comp_exam" name="comp_exam" class="form-control select2" required>
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php if(isset($examList)){
                                    foreach($examList as $ex){
                                        $selected = (isset($comp_exam) && $comp_exam == $ex->id) ? 'selected' : '';
                                        echo "<option value='".$ex->id."' ".$selected.">".$ex->exam."</option>";
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3">
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

                    <div class="col-sm-3">
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

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Section</label><small class="req"> *</small>
                            <select id="section_id" name="section_id" class="form-control select2" required>
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right" style="margin-top:25px;"><i class="fa fa-search"></i> Rechercher Elèves</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($studentList)) { ?>
        <div class="box box-info">
            <form action="<?php echo site_url('admin/examresult/print_semester_college') ?>" method="post" target="_blank">
                <?php echo $this->customlib->getCSRF(); ?>
                <input type="hidden" name="exam_group_id" value="<?php echo $exam_group_id; ?>">
                <input type="hidden" name="comp_exam" value="<?php echo $comp_exam; ?>">
                <?php if(!empty($devoir_exams)) { foreach($devoir_exams as $d_id){ echo '<input type="hidden" name="devoir_exams[]" value="'.$d_id.'">'; } } ?>

                <div class="box-header with-border">
                    <h3 class="box-title">Liste des élèves</h3>
                </div>
                <div class="box-body">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="50"><input type="checkbox" id="select_all" /></th>
                                <th>Matricule</th>
                                <th>Nom et Prénom</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($studentList)) { foreach ($studentList as $student) { ?>
                            <tr>
                                <td><input type="checkbox" name="student_ids[]" value="<?php echo $student->student_id; ?>" class="checkbox"></td>
                                <td><?php echo $student->admission_no; ?></td>
                                <td><?php echo $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></td>
                            </tr>
                            <?php } } else { echo "<tr><td colspan='3' class='text-center'>Aucun élève trouvé</td></tr>"; } ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary btn-sm pull-right"><i class="fa fa-print"></i> Générer Bulletins Semestre</button>
                </div>
            </form>
        </div>
        <?php } ?>
    </section>
</div>

<script type="text/javascript">
$(document).ready(function () {
    var base_url = '<?php echo base_url(); ?>';

    $('#devoir_exams').multiselect({
        includeSelectAllOption: true,
        buttonWidth: '100%',
        nonSelectedText: 'Choisir les devoirs'
    });

    $(document).on('change', '#exam_group_id', function () {
        var exam_group_id = $(this).val();
        if (exam_group_id != "") {
            $.ajax({
                type: "POST",
                url: base_url + "admin/examgroup/getExamByExamgroup",
                data: {'exam_group_id': exam_group_id},
                dataType: "json",
                success: function (data) {
                    $('#comp_exam').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
                    $('#devoir_exams').empty();
                    $.each(data, function (i, obj) {
                        $('#comp_exam').append("<option value='" + obj.id + "'>" + obj.exam + "</option>");
                        $('#devoir_exams').append("<option value='" + obj.id + "'>" + obj.exam + "</option>");
                    });
                    $('#devoir_exams').multiselect('rebuild');
                }
            });
        }
    });

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

    $('#select_all').on('click', function() {
        $('.checkbox').prop('checked', this.checked);
    });
});
</script>