<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4; margin: 0; }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }
        body { 
            margin: 0; padding: 0; 
            font-family: 'Times New Roman', Times, serif; 
            background: #eee; color: #000; 
        }

        /* --- COMPLETE RTL SUPPORT --- */
        <?php if($this->config->item('language') == 'Arabic') { ?>
            body { direction: rtl; text-align: right; }
            /* Flip the identity table layout */
            .identity-table td { text-align: right !important; }
            /* Flip the Observation Box border to the right side */
            .obs-box { 
                border-left: none !important; 
                border-right: 4px solid #42b80bff !important; 
                padding-left: 0 !important; 
                padding-right: 10px !important; 
            }
            /* Flip the Statistics Table values */
            .stats-table td { text-align: center !important; }
            .stats-table td:first-child { text-align: right !important; }
            /* Ensure signatures flip order (Teacher on right, Parent on left) */
            .signature-row { flex-direction: row; }
            /* Reverse table alignments */
            .marks-table th, .marks-table td { text-align: center !important; }
            .marks-table th:nth-child(2), .marks-table td:nth-child(2) { text-align: right !important; padding-right: 8px; }
        <?php } ?>

        .a4-container {
            width: 210mm; height: 297mm; padding: 10mm 12mm 5mm 12mm;
            margin: 10px auto; background: #fff; 
            display: flex; flex-direction: column;
            position: relative; overflow: hidden;
        }

        .watermark { 
            position: absolute; top: 50%; left: 50%; 
            transform: translate(-50%, -50%); 
            width: 90%; opacity: 0.11; z-index: 0; pointer-events: none; 
        }

        .header-img { width: 100%; height: auto; z-index: 1; margin-bottom: 8px; }
        
        .exam-banner { 
            background: #05AD05 !important; color: #000 !important; 
            text-align: center; padding: 2px; font-size: 24px; 
            font-weight: bold; text-transform: uppercase; margin-bottom: 10px;
            z-index: 1;
        }

        .identity-table { width: 100%; margin-bottom: 5px; border-collapse: collapse; font-size: 15px; z-index: 1; line-height: 2;}
        .photo-box { width: 90px; height: 85px; border: 1px solid #05AD05; object-fit: cover; }

        .marks-table { width: 100%; border-collapse: collapse; z-index: 1; }
        .marks-table th { border: 2px solid #000; padding: 6px; background: #05AD05 !important; font-size: 16px; }
        .marks-table td { border: 2px solid #000; padding: 2px 5px; text-align: center; font-size: 12px; }

        .total-row td { font-size: 16px !important; padding: 5px !important; font-weight: bold; }

        .bottom-section { margin-top: 15px; z-index: 1; }
        .recap-flex { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .obs-box { width: 50%; border-left: 4px solid #42b80bff; padding-left: 10px; font-size: 16px;}
        
        .writing-lines {
            margin-top: 10px; height: 75px; width: 95%;
            background: repeating-linear-gradient(to bottom, transparent, transparent 24px, #000 24px, #000 25px);
        }

        .stats-table { width: 48%; border-collapse: collapse; }
        .stats-table td { border: 2px solid #000; padding: 4px 6px; font-weight: bold; font-size: 15px; text-align: center; }
        .val-bg { background: #f9f9f9 !important; }

        .signature-section { margin-top: 10px; width: 100%; z-index: 1; }
        .signature-row { display: flex; justify-content: space-between; text-align: center; font-weight: bold; font-size: 14px; }
        .signature-row div { width: 30%; height: 65px; }

        .footer-fixed { margin-top: auto; z-index: 1; }
        .footer-info { display: flex; justify-content: space-between; align-items: center; font-size: 10px; border-top: 1px solid #000; padding-top: 5px; font-style: italic; }
        .footer-img { width: 100%; height: auto; display: block; margin-top: 2px; }

        @media print { body { background: none; } .a4-container { margin: 0; box-shadow: none; page-break-after: always; height: 297mm; } }
    </style>
</head>
<body>

<?php 
$all_student_averages = [];
foreach ($student_list as $s) {
    $st_get = 0; $st_max = 0;
    foreach ($s['exam_result'] as $r) { $st_get += (float)$r->get_marks; $st_max += (float)$r->max_marks; }
    $all_student_averages[$s['admission_no']] = ($st_max > 0) ? ($st_get / $st_max) * 10 : 0;
}
arsort($all_student_averages);
$rank_map = []; $rank = 1; $prev_score = -1; $i = 1;
foreach ($all_student_averages as $adm_no => $score) {
    if ($score != $prev_score) { $rank = $i; }
    $rank_map[$adm_no] = $rank; $prev_score = $score; $i++;
}
$class_average = (count($all_student_averages) > 0) ? array_sum($all_student_averages) / count($all_student_averages) : 0;

if (!empty($student_list)) { 
    foreach ($student_list as $student) { 
        $t_get = 0; $t_max = 0; $row_count = 1;
?>

<div class="a4-container">
    <img src="<?php echo base_url('uploads/school_content/logo/'.$sch_setting->admin_logo); ?>" class="watermark">
    <img src="<?php echo base_url('uploads/marksheet/header_image.png'); ?>" class="header-img">
    <div class="exam-banner"><?php echo $exam_name; ?></div>

    <table class="identity-table">
        <tr>
            <td width="110">
                <img src="<?php echo base_url($student['image']); ?>" class="photo-box" onerror="this.src='<?php echo base_url('uploads/student_images/no_image.png'); ?>'">
            </td>
            <td valign="top" style="padding: 0 15px;">
                <strong><?php echo $this->lang->line('name'); ?>:</strong> <?php echo $student['firstname'].' '.$student['lastname']; ?><br>
                <strong><?php echo $this->lang->line('admission_no'); ?>:</strong> <?php echo $student['admission_no']; ?><br>
                <strong><?php echo $this->lang->line('date_of_birth'); ?>:</strong> <?php echo date($sch_setting->date_format, strtotime($student['dob'])); ?>
            </td>
            <td valign="top" style="text-align: right;">
                <strong><?php echo $this->lang->line('session'); ?>:</strong> <?php echo $sch_setting->session; ?><br>
                <strong><?php echo $this->lang->line('class'); ?>:</strong> <?php echo $student['class'].' ('.$student['section'].')'; ?><br>
                <strong><?php echo $this->lang->line('total_students'); ?>:</strong> <?php echo $student['total_class_students']; ?>
            </td>
        </tr>
    </table>

    <table class="marks-table">
        <thead>
            <tr>
                <th width="5%">Nº</th>
                <th align="<?php echo ($this->config->item('language') == 'Arabic') ? 'right' : 'left'; ?>" style="padding:0 8px;"><?php echo $this->lang->line('subject'); ?></th>
                <th width="10%"><?php echo $this->lang->line('marks'); ?></th>
                <th width="10%"><?php echo $this->lang->line('out_of'); ?></th>
                <th width="25%"><?php echo $this->lang->line('appreciation'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($student['exam_result'] as $res) { 
                $m = (float)$res->get_marks; $mx = (float)$res->max_marks; $t_get += $m; $t_max += $mx;
            ?>
                <tr>
                    <td><?php echo sprintf("%02d", $row_count++); ?></td>
                    <td align="<?php echo ($this->config->item('language') == 'Arabic') ? 'right' : 'left'; ?>" style="padding:0 8px;"><?php echo $res->subject_name; ?></td>
                    <td><strong><?php echo (float)$res->get_marks; ?></strong></td>
                    <td><?php echo (float)$res->max_marks; ?></td>
                    <td><?php 
                        $p = ($mx > 0) ? ($m / $mx) * 100 : 0;
                        if ($p >= 90) echo $this->lang->line('app_excellent');
                        elseif ($p >= 80) echo $this->lang->line('app_very_good');
                        elseif ($p >= 70) echo $this->lang->line('app_good');
                        elseif ($p >= 60) echo $this->lang->line('app_satisfactory');
                        elseif ($p >= 50) echo $this->lang->line('app_passable');
                        elseif ($p >= 40) echo $this->lang->line('app_poor');
                        else echo $this->lang->line('app_insufficient');
                    ?></td>
                </tr>
            <?php } ?>
            
            <tr class="total-row" style="background:#05AD05 !important;">
                <td colspan="2" align="<?php echo ($this->config->item('language') == 'Arabic') ? 'right' : 'left'; ?>" style="padding:0 8px;"><?php echo $this->lang->line('total_marks'); ?></td>
                <td><?php echo (float)$t_get; ?></td>
                <td><?php echo (float)$t_max; ?></td>
                <td><?php echo ($t_max > 0) ? (float)number_format(($t_get / $t_max) * 10, 2) : '0'; ?> / 10</td>
            </tr>
        </tbody>
    </table>

    <div class="bottom-section">
        <div class="recap-flex">
            <div class="obs-box">
                <div style="font-weight:bold; text-decoration:underline; font-style:italic; font-size:20px; text-align: center; width:100%;">
                    <?php echo $this->lang->line('observations'); ?>
                </div>
                <div class="writing-lines">
                    </div>
            </div>
            
            <table class="stats-table">
                <tr>
                    <td colspan="2" style="text-align:<?php echo ($this->config->item('language') == 'Arabic') ? 'right' : 'left'; ?>;" width="50%"><?php echo $this->lang->line('average'); ?></td>
                    <td colspan="2" class="val-bg"><?php echo (float)number_format((($t_max > 0) ? ($t_get / $t_max) * 10 : 0), 2); ?> / 10</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:<?php echo ($this->config->item('language') == 'Arabic') ? 'right' : 'left'; ?>;"><?php echo $this->lang->line('rank'); ?></td>
                    <td colspan="2" class="val-bg">
                        <?php 
                            $r = $rank_map[$student['admission_no']];
                            echo $r . (($r == 1) ? "<sup>er</sup>" : "<sup>ème</sup>");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:<?php echo ($this->config->item('language') == 'Arabic') ? 'right' : 'left'; ?>;"><?php echo $this->lang->line('class_average'); ?></td>
                    <td colspan="2" class="val-bg"><?php echo (float)number_format($class_average, 2); ?> / 10</td>
                </tr>
                <tr>
                    <td width="25%"><?php echo $this->lang->line('max_marks'); ?></td>
                    <td width="25%" class="val-bg"><?php echo !empty($all_student_averages) ? (float)number_format(max($all_student_averages), 1) : '0'; ?></td>
                    <td width="25%"><?php echo $this->lang->line('min_marks'); ?></td>
                    <td width="25%" class="val-bg"><?php echo !empty($all_student_averages) ? (float)number_format(min($all_student_averages), 1) : '0'; ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-row">
            <div><?php echo $this->lang->line('teacher'); ?></div>
            <div><?php echo $this->lang->line('principal'); ?></div>
            <div><?php echo $this->lang->line('parent'); ?></div>
        </div>
    </div>

    <div class="footer-fixed">
        <div class="footer-info">
            <span><?php echo $this->lang->line('warning_bulletin'); ?></span>
            <strong><?php echo $this->lang->line('date_of_issue'); ?> : <?php echo date('d/m/Y'); ?></strong>
        </div>
        <img src="<?php echo base_url('uploads/marksheet/footer_image.png'); ?>" class="footer-img">
    </div>
</div>

<?php } } ?>

</body>
</html>