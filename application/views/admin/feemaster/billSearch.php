<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> <?php echo $this->lang->line('monthly_bills'); ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                        <form role="form" action="<?php echo site_url('admin/feemaster/bills') ?>" method="post" class="row">
                            <?php echo $this->customlib->getCSRF(); ?>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('class'); ?></label>
                                        <select id="class_id" name="class_id" class="form-control"> <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($classlist as $class) { ?>
                                                <option value="<?php echo $class['id'] ?>" <?php echo set_select('class_id', $class['id']); ?>><?php echo $class['class'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label>
                                        <select id="section_id" name="section_id" class="form-control"> <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                    </div>
                                </div>
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-primary btn-sm" style="margin-top:25px"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                            </div>
                        </form>
                    </div>

                    <?php if (isset($studentList)) { ?>
                        <div class="box-header ptbnull"></div>
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-users"></i> Student List</h3>
                        </div>
                        <div class="box-body">
                            <form action="<?php echo site_url('admin/feemaster/print_bills') ?>" method="post" target="_blank">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="select_all"></th>
                                                <th><?php echo $this->lang->line('admission_no'); ?></th>
                                                <th><?php echo $this->lang->line('student_name'); ?></th>
                                                <th><?php echo $this->lang->line('class'); ?></th>
                                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($studentList)) { ?>
                                                <tr><td colspan="5" class="text-danger text-center">No student found</td></tr>
                                            <?php } else {
                                                foreach ($studentList as $student) { ?>
                                                    <tr>
                                                        <td><input type="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>" class="student_checkbox"></td>
                                                        <td><?php echo $student['admission_no']; ?></td>
                                                        <td><?php echo $student['firstname'] . " " . $student['lastname']; ?></td>
                                                        <td><?php echo $student['class'] . " (" . $student['section'] . ")"; ?></td>
                                                        <td><?php echo $student['father_name']; ?></td>
                                                    </tr>
                                            <?php } } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="submit" class="btn btn-info btn-sm pull-right"><i class="fa fa-print"></i> Générer les factures</button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // Dynamic Section Loading
        $(document).on('change', '#class_id', function () {
            var class_id = $(this).val();
            $('#section_id').html("<option value=''><?php echo $this->lang->line('select'); ?></option>");
            $.ajax({
                type: "GET",
                url: "<?php echo base_url(); ?>sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj) {
                        $('#section_id').append("<option value='" + obj.section_id + "'>" + obj.section + "</option>");
                    });
                }
            });
        });

        // Select/Deselect All
        $('#select_all').on('click', function () {
            $('.student_checkbox').prop('checked', this.checked);
        });
    });
</script>