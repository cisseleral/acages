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
                                    <select name="exam_group_id" class="form-control select2">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($examgrouplist as $examgroup) { ?>
                                            <option value="<?php echo $examgroup->id ?>" <?php echo set_select('exam_group_id', $examgroup->id); ?>><?php echo $examgroup->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Classe</label><small class="req"> *</small>
                                    <select id="class_id" name="class_id" class="form-control">
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
                                    <select id="section_id" name="section_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3" style="padding-top:25px;">
                                <button type="submit" name="search" value="search" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Rechercher</button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (isset($studentList)) { ?>
                    <form action="<?php echo site_url('admin/examresult/print_bulletin') ?>" method="post" target="_blank">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Liste des élèves</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Sélectionner l'Examen</label><small class="req"> *</small>
                                            <select name="exam_id" class="form-control" required>
                                                <option value="">Selectionner</option>
                                                <?php foreach ($examList as $ex) { ?>
                                                    <option value="<?php echo $ex->id; ?>"><?php echo $ex->exam; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="select_all" /></th>
                                            <th>Matricule</th>
                                            <th>Nom et Prénom</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($studentList as $student) { ?>
                                            <tr>
                                                <td><input type="checkbox" class="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>" /></td>
                                                <td><?php echo $student['admission_no']; ?></td>
                                                <td><?php echo $student['firstname'] . " " . $student['lastname']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <button type="submit" class="btn btn-info pull-right"><i class="fa fa-print"></i> Générer le Bulletin</button>
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
        // Dropdown Logic for Section
        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj) {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });

        $('#select_all').on('click', function() {
            $('.checkbox').prop('checked', this.checked);
        });
    </script>