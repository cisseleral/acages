<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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

        /* --- SUPPORT ARABE / RTL --- */
        <?php if($this->config->item('language') == 'Arabic') { ?>
            body { direction: rtl; text-align: right; }
            .identity-table td { text-align: right !important; }
            .obs-box { border-left: none !important; border-right: 4px solid #42b80bff !important; padding-right: 10px !important; }
        <?php } ?>

        .a4-container {
            width: 210mm; height: 297mm; padding: 10mm 12mm 5mm 12mm;
            margin: 10px auto; background: #fff; 
            display: flex; flex-direction: column;
            position: relative; overflow: hidden;
            page-break-after: always;
        }

        .watermark-container { 
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
            width: 80%; opacity: 0.12; z-index: 0; pointer-events: none; 
        }
        .watermark-container img { width: 100%; height: auto; }
        
        @media print {
            .watermark-container { top: 50vh !important; position: fixed !important; opacity: 0.15 !important; }
            .no-print { display: none !important; }
        }

        .header-img { width: 100%; height: auto; margin-bottom: 8px; }
        .exam-banner { 
            background: #05AD05 !important; color: #000 !important; 
            text-align: center; padding: 2px; font-size: 22px; 
            font-weight: bold; text-transform: uppercase; margin-bottom: 10px;
        }

        .identity-table { width: 100%; margin-bottom: 5px; border-collapse: collapse; font-size: 15px; line-height: 1.8; }
        .photo-box { width: 95px; height: 90px; border: 1px solid #05AD05; object-fit: cover; }

        .marks-table { width: 100%; border-collapse: collapse; }
        .marks-table th { border: 2px solid #000; padding: 5px; background: #05AD05 !important; font-size: 15px; }
        .marks-table td { border: 2px solid #000; padding: 3px 5px; text-align: center; font-size: 13px; }

        .total-row td { font-size: 15px !important; padding: 5px !important; font-weight: bold; background: # !important; }
        
        .bottom-section { margin-top: 15px; width: 100%; }
        .recap-flex { display: flex; justify-content: space-between; margin-bottom: 20px; align-items: flex-start; }
        
        /* Styles fournis pour le tableau recap */
        .stats-table { width: 48%; border-collapse: collapse; background: transparent !important; }
        .stats-table td { border: 2px solid #000; padding: 4px 6px; font-weight: bold; font-size: 15px; text-align: center; background: transparent !important; height: 1px;}
        .val-bg { background: rgba(249, 249, 249, 0.5) !important; }
        .recap-table-right td {
            height: 40px; /* Adjust this value to make rows bigger */
            vertical-align: middle;
        }

        .obs-box { width: 55%; border-left: 4px solid #42b80bff; padding-left: 10px; }
        .writing-lines {
            margin-top: 8px; height: 90px; width: 85%;
            background: repeating-linear-gradient(to bottom, transparent, transparent 21px, #ccc 21px, #ccc 22px);
        }

        .footer-fixed { margin-top: auto; }
        .footer-info { display: flex; justify-content: space-between; font-size: 10px; padding-top: 5px; font-style: italic; }
        .footer-img { width: 100%; height: auto; display: block; margin-top: 2px; }
    </style>
</head>
<body>

<?php 
// 1. CALCULS DES STATISTIQUES (Base de votre code fonctionnel)
$all_student_averages = [];
foreach ($student_list as $s) {
    if(!empty($s['exam_result'])){
        $st_get = 0; $st_coef_total = 0;
        foreach ($s['exam_result'] as $r) { 
            $note = (float)$r->get_marks;
            $coef = (isset($r->coef) && $r->coef > 0) ? (float)$r->coef : 1.0;
            $st_get += ($note * $coef); 
            $st_coef_total += $coef; 
        }
        if ($st_coef_total > 0) {
            $all_student_averages[$s['admission_no']] = ($st_get / $st_coef_total);
        }
    }
}

$total_exam_students = count($all_student_averages);
arsort($all_student_averages);
$rank_map = []; $rank = 1; $prev_score = -1; $pos = 1;
foreach ($all_student_averages as $adm_no => $score) {
    if ($score != $prev_score) { $rank = $pos; }
    $rank_map[$adm_no] = $rank; $prev_score = $score; $pos++;
}

$class_average = ($total_exam_students > 0) ? array_sum($all_student_averages) / $total_exam_students : 0;
$max_score = ($total_exam_students > 0) ? max($all_student_averages) : 0;
$min_score = ($total_exam_students > 0) ? min($all_student_averages) : 0;

if (!empty($student_list)) { 
    foreach ($student_list as $student) { 
        if (empty($student['exam_result'])) continue;

        $t_weighted = 0; $t_coef = 0; $t_marks_raw = 0;
        $row_count = 1;
?>

<div class="a4-container student-page">
    <div class="watermark-container">
        <img src="<?php echo base_url('uploads/marksheet/watermark.png'); ?>">
    </div>

    <img src="<?php echo base_url('uploads/marksheet/header_image.png'); ?>" class="header-img">
    <div class="exam-banner"><?php echo $exam_description; ?></div>

    <table class="identity-table">
        <tr>
            <td width="115">
                <img src="<?php echo base_url(rawurlencode($student['image'])); ?>" class="photo-box" onerror="this.src='<?php echo base_url('uploads/student_images/no_image.png'); ?>'">
            </td>
            <td valign="top" style="padding: 0 15px;">
                <strong><?php echo $this->lang->line('name'); ?>:</strong> <?php echo $student['firstname'].' '.$student['lastname']; ?><br>
                <strong>Matricule:</strong> <?php echo $student['admission_no']; ?><br>
                <strong>Date de naissance:</strong> <?php echo ($student['dob'] != "0000-00-00" && $student['dob'] != "") ? date($sch_setting->date_format, strtotime($student['dob'])) : ""; ?>
            </td>
            <td valign="top" style="text-align: left;">
                <strong><?php echo $this->lang->line('session'); ?>:</strong> <?php echo $sch_setting->session; ?><br>
                <strong><?php echo $this->lang->line('class'); ?>:</strong> <?php echo $student['class'].' ('.$student['section'].')'; ?><br>
                <strong>Effectif:</strong> <?php echo $total_exam_students; ?> 
            </td>
        </tr>
    </table>

    <table class="marks-table">
        <thead>
            <tr>
                <th width="5%">N°</th>
                <th>Disciplines</th>
                <th width="10%">Notes</th>
                <th width="10%">Coef</th>
                <th width="12%">M. Coef</th>
                <th width="10%">Rang</th> 
                <th width="20%">Appréciation</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                foreach ($student['exam_result'] as $res) { 
                    $n = (float)$res->get_marks; 
                    $c = (isset($res->coef) && $res->coef > 0) ? (float)$res->coef : 1.0; 
                    $mc = $n * $c;

                    $leaderboard = isset($subject_leaderboard[$res->subject_id]) ? $subject_leaderboard[$res->subject_id] : [$n];
                    $sub_rank = array_search($n, $leaderboard) + 1;

                    $t_marks_raw += $n; 
                    $t_coef += $c; 
                    $t_weighted += $mc;
                ?>
                <tr>
                    <td><?php echo sprintf("%02d", $row_count++); ?></td>
                    <td style="text-align: left !important; padding-left: 10px;"><strong><?php echo $res->subject_name; ?></strong></td>
                    <td><strong><?php echo number_format($n, 2); ?></strong></td>
                    <td><?php echo number_format($c, 2); ?></td>
                    <td><strong><?php echo number_format($mc, 2); ?></strong></td>
                    <td><?php echo $sub_rank; ?><?php echo ($sub_rank == 1) ? 'er' : 'è'; ?></td>
                    <td style="font-style: italic; font-size: 11px;">
                        <?php 
                            if ($n >= 16) echo "Très Bien";
                            elseif ($n >= 14) echo "Bien";
                            elseif ($n >= 12) echo "Assez Bien";
                            elseif ($n >= 10) echo "Passable";
                            else echo "Insuffisant";
                        ?>
                    </td>
                </tr>
            <?php } ?>
            
            <tr class="total-row">
                <td colspan="2" align="right" style="padding-right:15px;">TOTAUX</td>
                <td><?php echo number_format($t_marks_raw, 2); ?></td>
                <td><?php echo number_format($t_coef, 2); ?></td>
                <td><?php echo number_format($t_weighted, 2); ?></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="bottom-section">
        <div class="recap-flex">
            <table class="stats-table" style="width: 48%;">
                <thead><tr><td colspan="2" style="background:#eee !important;">Distinctions</td></tr></thead>
                <?php $dn = ($t_coef > 0) ? ($t_weighted / $t_coef) : 0; ?>
                <tr><td style="text-align:left;">Félicitation</td><td width="45" class="val-bg"><?php echo ($dn >= 16) ? 'X' : ''; ?></td></tr>
                <tr><td style="text-align:left;">Encouragement</td><td class="val-bg"><?php echo ($dn >= 14 && $dn < 16) ? 'X' : ''; ?></td></tr>
                <tr><td style="text-align:left;">Tableau d’honneur</td><td class="val-bg"><?php echo ($dn >= 12 && $dn < 14) ? 'X' : ''; ?></td></tr>
                <tr><td style="text-align:left;">Avertissement</td><td class="val-bg"><?php echo ($dn < 10 && $dn > 7) ? 'X' : ''; ?></td></tr>
                <tr><td style="text-align:left;">Blâme</td><td class="val-bg"><?php echo ($dn < 7) ? 'X' : ''; ?></td></tr>
            </table>

            <table class="stats-table recap-table-right">
                <tr>
                    <td colspan="2" style="text-align:<?php echo ($this->config->item('language') == 'Arabic') ? 'right' : 'center'; ?>;" width="50%"><?php echo $this->lang->line('average'); ?></td>
                    <td colspan="2" class="val-bg"><?php echo number_format($dn, 2); ?> / 20</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:<?php echo ($this->config->item('language') == 'Arabic') ? 'right' : 'center'; ?>;"><?php echo $this->lang->line('rank'); ?></td>
                    <td colspan="2" class="val-bg">
                        <?php 
                            $r = isset($rank_map[$student['admission_no']]) ? $rank_map[$student['admission_no']] : '-';
                            echo $r . (($r == 1) ? "er" : "ème");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:<?php echo ($this->config->item('language') == 'Arabic') ? 'right' : 'center'; ?>;"><?php echo $this->lang->line('class_average'); ?></td>
                    <td colspan="2" class="val-bg"><?php echo number_format($class_average, 2); ?></td> </tr>
                <tr>
                    <td width="25%"><?php echo $this->lang->line('max_marks'); ?></td>
                    <td width="25%" class="val-bg"><?php echo number_format($max_score, 2); ?></td> 
                    <td width="25%"><?php echo $this->lang->line('min_marks'); ?></td>
                    <td width="25%" class="val-bg"><?php echo number_format($min_score, 2); ?></td> 
                </tr>
            </table>
        </div>

        <div class="recap-flex" style="margin-top: 10px;">
            <div class="obs-box">
                <div style="font-weight:bold; text-decoration:underline; font-style:italic; font-size:18px;">
                    Observations du conseil de classe
                </div>
                <div class="writing-lines"></div>
            </div>

            <div style="width: 40%; text-align: center;">
                <div style="font-weight: bold; text-decoration: underline; margin-top: 5px; font-size: 18px;">
                    Le Directeur
                </div>
            </div>
        </div>
    </div>

    <div class="footer-fixed">
        <div class="footer-info">
            <span>Tout raturage annule la validité du présent bulletin</span>
            <strong>Généré le : <?php echo date('d/m/Y'); ?></strong>
        </div>
        <img src="<?php echo base_url('uploads/marksheet/footer_image.png'); ?>" class="footer-img">
    </div>
</div>

<?php } } ?>

<div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
    <button onclick="downloadPDF()" style="background: #d9534f; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
        <i class="fa fa-file-pdf-o"></i> Télécharger PDF
    </button>
</div>

<script>
function downloadPDF() {
    const element = document.body; 
    const opt = {
        margin: 0,
        filename: 'Bulletins_College.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        pagebreak: { mode: ['css', 'legacy'], before: '.student-page' },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>
</body>
</html>