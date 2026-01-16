<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-gears"></i> <small class="pull-right">
                <a type="button" class="btn btn-primary btn-sm"><?php echo $this->lang->line('setting') ?></a>
            </small>
        </h1>
    </section>
    <section class="content">
        <?php 
            if (!$this->rbac->hasPrivilege('qr_code_setting', 'can_view')) {
                access_denied();
            } 
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-envelope"></i> <?php echo $this->lang->line('setting') ?></h3>
                    </div>
                    <form id="form1" action="<?php echo site_url('admin/qrattendance/setting') ?>" name="employeeform" class="form-horizontal form-label-left" method="post" accept-charset="utf-8">
                        <div class="box-body">                            

                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg');
                                $this->session->unset_userdata('msg'); ?>
                            <?php } ?>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1"><?php echo $this->lang->line("auto_attendance"); ?><small class="req"> * </small>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12"> &nbsp
                                    <label class="radio-inline">
                                        <input type="radio" name="auto_attendance" value="0" <?php
                                                                                                if (!$setting->auto_attendance) {
                                                                                                    echo "checked";
                                                                                                }
                                                                                                ?>><?php echo $this->lang->line('disabled'); ?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="auto_attendance" value="1" <?php
                                                                                                if ($setting->auto_attendance) {
                                                                                                    echo "checked";
                                                                                                }
                                                                                                ?>><?php echo $this->lang->line('enabled'); ?>
                                    </label>

                                    <span class="text-danger"><?php echo form_error('auto_attendance'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1"><?php echo $this->lang->line("select_camera") ?><small class="req"> *</small>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12"> &nbsp
                                    <label class="radio-inline">
                                        <input type="radio" name="camera_type" value="environment" <?php
                                                                                        if ($setting->camera_type =="environment") {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>><?php echo $this->lang->line("primary_camera") ?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="camera_type" value="user" <?php
                                                                                        if ($setting->camera_type  =="user") {
                                                                                            echo "checked";
                                                                                        }
                                                                                        ?>><?php echo $this->lang->line("secondary_camera") ?>
                                    </label>

                                    <span class="text-danger"><?php echo form_error('camera_type'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="col-md-6 col-sm-6 col-xs-6 col-md-offset-3">
                                 
                                    <button type="submit" class="btn btn-info pull-left"><?php echo $this->lang->line('save'); ?></button>
                                
                            </div>
                            <div class="pull-right"><?php echo $this->lang->line('version') . " " . $version; ?></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>