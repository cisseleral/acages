<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; }

        body { 
            margin: 0; padding: 0; 
            background: #eee; 
            font-family: 'Times New Roman', serif;
            <?php if($this->config->item('language') == 'Arabic') { echo 'direction: rtl;'; } ?>
        }

        .landscape-container {
            width: 277mm; 
            min-height: 190mm;
            margin: 0 auto;
            background: #fff;
            padding: 5mm;
            position: relative;
        }

        .header-img { width: 100%; height: auto; display: block; margin-bottom: 5px; }

        .col-total { background-color: #e2efda !important; font-weight: bold; border: 1px solid #000 !important; } 
        .col-moy { background-color: #fff2cc !important; font-weight: bold; border: 1px solid #000 !important; }   
        .col-rang { background-color: #d9e1f2 !important; font-weight: bold; border: 1px solid #000 !important; }  

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .results-table th, .results-table td {
            border: 1px solid #000;
            font-size: 9px;
            padding: 4px 2px;
            text-align: center;
        }

        .results-table thead th { 
            background-color: #69abeeff !important; 
            color: #000 !important; 
            font-weight: bold; 
            font-size: 15px;
        }

        /* Vertical Text Logic */
        .vertical-text { height: 110px; vertical-align: middle; font-weight: bold;}
        .vertical-text span {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            display: inline-block;
            max-width: 25px;
            line-height: 1.1;
            white-space: nowrap;
        }

        @media print {
            body { background: none; }
            .landscape-container { margin: 0; padding: 0; width: 100%; }
        }
    </style>
</head>
<body>

<?php
$processed_students = [];

if (!empty($student_list)) {
    foreach ($student_list as $student) {
        $t_get = 0;
        $t_max = 0; 
        $marks_map = [];

        if (!empty($student['exam_result'])) {
            foreach ($student['exam_result'] as $res) {
                $m  = (float)$res['get_marks'];
                $mx = (float)$res['max_marks'];
                $s_code = !empty($res['code']) ? $res['code'] : $res['subject_name'];
                
                $marks_map[$s_code] = $m;
                $t_get += $m;
                $t_max += $mx;
            }
        }
        
        $divider = ($t_max / 10);
        $average = ($divider > 0) ? ($t_get / $divider) : 0;
        
        $student['calc_total'] = $t_get;
        $student['calc_avg'] = $average;
        $student['marks_map'] = $marks_map;
        $processed_students[] = $student;
    }

    usort($processed_students, function($a, $b) {
        return $b['calc_avg'] <=> $a['calc_avg'];
    });

    foreach ($processed_students as $key => $val) {
        $processed_students[$key]['rank'] = $key + 1;
    }
}
?>

<div class="landscape-container">
    <img src="<?php echo base_url('uploads/marksheet/header_image.png'); ?>" class="header-img">

    <div style="background: #103153; color: #fff; text-align: center; padding: 6px; font-size: 14px; font-weight:bold; margin: 5px 0;">
        <?php echo strtoupper($this->lang->line('exam_result_summary')); ?> - <?php echo strtoupper($exam->exam); ?>
    </div>

    <table style="width: 100%; margin-bottom: 8px; font-weight: bold; font-size: 11px;">
        <tr>
            <td><?php echo $this->lang->line('academic_year'); ?>: <?php echo $academic_year; ?></td>
            <td style="text-align: center;"><?php echo $this->lang->line('class'); ?>: <?php echo $student_list[0]['class']; ?></td>
            <td style="text-align: right;">
                <?php echo $this->lang->line('total_students'); ?>: <?php echo count($student_list); ?> 
                <?php echo $this->lang->line('date'); ?>: <?php echo date($sch_setting->date_format); ?>
            </td>
        </tr>
    </table>

    <table class="results-table">
        <thead>
            <tr>
                <th><?php echo $this->lang->line('admission_no'); ?></th>
                <th style="width: 18%; text-align: Center; padding-left: 5px;"><?php echo $this->lang->line('student_name'); ?></th>
                
                <th class="vertical-text"><span><?php echo $this->lang->line('date_of_birth'); ?></span></th>
                
                <?php 
                $subject_list_to_display = [];
                if(!empty($student_list[0]['exam_result'])){
                    foreach($student_list[0]['exam_result'] as $sub){
                        $subject_list_to_display[] = !empty($sub['code']) ? $sub['code'] : $sub['subject_name'];
                    }
                }
                
                foreach ($subject_list_to_display as $code): ?>
                    <th class="vertical-text"><span><?php echo $code; ?></span></th>
                <?php endforeach; ?>

                <th class="col-total"><?php echo $this->lang->line('total'); ?></th>
                <th class="col-moy"><?php echo $this->lang->line('average'); ?></th>
                <th class="col-rang"><?php echo $this->lang->line('rank'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($processed_students as $student): ?>
            <tr>
                <td><?php echo $student['admission_no']; ?></td>
                <td style="text-align: left; padding-left: 5px;">
                    <?php echo $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?>
                </td>
                
                <td>
                    <?php 
                        if (!empty($student['dob']) && $student['dob'] != '0000-00-00') {
                            echo date($sch_setting->date_format, strtotime($student['dob']));
                        }
                    ?>
                </td>
                
                <?php foreach ($subject_list_to_display as $code): ?>
                    <td>
                        <?php 
                        if (isset($student['marks_map'][$code])) {
                            echo number_format($student['marks_map'][$code], 2, ',', ''); 
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>
                <?php endforeach; ?>

                <td class="col-total"><?php echo number_format($student['calc_total'], 2, ',', ''); ?></td>
                <td class="col-moy"><?php echo number_format($student['calc_avg'], 2, ',', ''); ?></td>
                <td class="col-rang">
                    <?php echo $student['rank']; ?><?php if($this->config->item('language') != 'Arabic') echo ($student['rank'] == 1) ? 'er' : ''; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>