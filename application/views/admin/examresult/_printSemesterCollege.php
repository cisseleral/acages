<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @page { size: A4; margin: 0; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Times New Roman', Times, serif; background: #eee; color: #000; }

        /* --- RTL SUPPORT --- */
        <?php if($this->config->item('language') == 'Arabic') { ?>
            body { direction: rtl; text-align: right; }
            .identity-table td { text-align: right !important; }
            .marks-table td:nth-child(2) { text-align: right !important; padding-right: 10px !important; }
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
        
        .header-img { width: 100%; height: auto; margin-bottom: 8px; }
        .exam-banner { 
            background: #05AD05 !important; color: #000 !important; 
            text-align: center; padding: 2px; font-size: 20px; 
            font-weight: bold; text-transform: uppercase; margin-bottom: 10px;
        }

        .identity-table { width: 100%; margin-bottom: 5px; border-collapse: collapse; font-size: 14px; line-height: 1.6; }
        .photo-box { width: 90px; height: 90px; border: 1px solid #05AD05; object-fit: cover; }

        .marks-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .marks-table th { border: 2px solid #000; padding: 4px; background: #05AD05 !important; font-size: 13px; }
        .marks-table td { border: 2px solid #000; padding: 3px; text-align: center; font-size: 12px; font-weight: bold; }
        
        /* SUBJECT ALIGNMENT */
        .marks-table td:nth-child(2) { text-align: left !important; padding-left: 8px !important; }

        .total-row td { font-size: 14px !important; padding: 5px !important; background: #eee !important; }
        
        /* BOTTOM SECTION */
        .recap-flex { display: flex; justify-content: space-between; align-items: stretch; margin-bottom: 10px; }
        .stats-table { width: 49%; border-collapse: collapse; }
        .stats-table th { border: 2px solid #000; padding: 4px; background: #eee; font-size: 14px; text-align: center; }
        .stats-table td { border: 2px solid #000; padding: 4px; font-weight: bold; font-size: 14px; text-align: center; }
        
        /* Independent Height for Recap Table */
        .recap-table-right td { height: 38px; vertical-align: middle; }
        .val-bg { background: rgba(249, 249, 249, 0.5) !important; }

        .obs-box { width: 55%; border-left: 4px solid #42b80bff; padding-left: 10px; }
        .writing-lines {
            margin-top: 8px; height: 60px; width: 95%;
            background: repeating-linear-gradient(to bottom, transparent, transparent 19px, #ccc 19px, #ccc 20px);
        }

        .footer-fixed { margin-top: auto; }
        .footer-info { display: flex; justify-content: space-between; font-size: 10px; font-style: italic; }
        .footer-img { width: 100%; height: auto; display: block; margin-top: 2px; }
        
        @media print { .no-print { display: none !important; } .watermark-container { position: fixed !important; } }
    </style>
</head>
<body>

<?php 
// 1. STATS CALCULATION FOR RANKING
$all_averages = [];
foreach ($student_list as $s) {
    $st_weighted = 0; $st_coef = 0;
    foreach ($s['exam_result'] as $r) {
        $st_weighted += $r->m_coef;
        $st_coef += $r->coef;
    }
    if ($st_coef > 0) { $all_averages[$s['admission_no']] = $st_weighted / $st_coef; }
}
arsort($all_averages);
$rank_map = []; $pos = 1; $prev = -1; $rank = 1;
foreach ($all_averages as $adm => $score) {
    if ($score != $prev) { $rank = $pos; }
    $rank_map[$adm] = $rank; $prev = $score; $pos++;
}
$class_avg = (count($all_averages) > 0) ? array_sum($all_averages) / count($all_averages) : 0;
$max_v = (count($all_averages) > 0) ? max($all_averages) : 0;
$min_v = (count($all_averages) > 0) ? min($all_averages) : 0;

foreach ($student_list as $student) { 
    $total_m_coef = 0; $total_coef = 0; $row_count = 1;
?>

<div class="a4-container student-page">
    <div class="watermark-container"><img src="<?php echo base_url('uploads/marksheet/watermark.png'); ?>"></div>
    <img src="<?php echo base_url('uploads/marksheet/header_image.png'); ?>" class="header-img">
    <div class="exam-banner"><?php echo $exam_description; ?></div>

    <table class="identity-table">
        <tr>
            <td width="110">
                <img src="<?php echo base_url(rawurlencode($student['image'])); ?>" class="photo-box" onerror="this.src='<?php echo base_url('uploads/student_images/no_image.png'); ?>'">
            </td>
            <td valign="top" style="padding: 0 15px;">
                <strong><?php echo $this->lang->line('name'); ?>:</strong> <?php echo $student['firstname'].' '.$student['lastname']; ?><br>
                <strong><?php echo $this->lang->line('admission_no'); ?>:</strong> <?php echo $student['admission_no']; ?><br>
                <strong><?php echo $this->lang->line('date_of_birth'); ?>:</strong> <?php echo ($student['dob'] != "0000-00-00") ? date($sch_setting->date_format, strtotime($student['dob'])) : ""; ?>
            </td>
            <td valign="top">
                <strong><?php echo $this->lang->line('class'); ?>:</strong> <?php echo $student['class'].' ('.$student['section'].')'; ?><br>
                <strong><?php echo $this->lang->line('session'); ?>:</strong> <?php echo $sch_setting->session; ?><br>
                <strong>Effectif:</strong> <?php echo count($all_averages); ?>
            </td>
        </tr>
    </table>

    <table class="marks-table">
        <thead>
            <tr>
                <th width="5%">N°</th>
                <th width="25%"><?php echo $this->lang->line('subject'); ?></th>
                <th width="10%">Devoir</th>
                <th width="10%">Comp</th>
                <th width="12%">Moy / 20</th>
                <th width="8%">Coef</th>
                <th width="10%">M. Coef</th>
                <th width="20%">Appréciation</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($student['exam_result'] as $res) { 
                $total_m_coef += $res->m_coef; 
                $total_coef += $res->coef;
            ?>
            <tr>
                <td><?php echo sprintf("%02d", $row_count++); ?></td>
                <td><?php echo $res->subject_name; ?></td>
                <td><?php echo number_format($res->devoir, 2); ?></td>
                <td><?php echo number_format($res->comp, 2); ?></td>
                <td style="background:#f9f9f9;"><?php echo number_format($res->moy_20, 2); ?></td>
                <td><?php echo number_format($res->coef, 1); ?></td>
                <td><?php echo number_format($res->m_coef, 2); ?></td>
                <td style="font-size:10px; font-style:italic;">
                    <?php 
                        if($res->moy_20 >= 16) echo "Excellent";
                        elseif($res->moy_20 >= 14) echo "Bien";
                        elseif($res->moy_20 >= 12) echo "Assez Bien";
                        elseif($res->moy_20 >= 10) echo "Passable";
                        else echo "Insuffisant";
                    ?>
                </td>
            </tr>
            <?php } ?>
            <tr class="total-row">
                <td colspan="5" align="right">TOTAL</td>
                <td><?php echo number_format($total_coef, 1); ?></td>
                <td><?php echo number_format($total_m_coef, 2); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="bottom-section">
        <div class="recap-flex">
            <table class="stats-table">
                <thead><tr><th colspan="2">Distinctions</th></tr></thead>
                <?php $sem_avg = ($total_coef > 0) ? $total_m_coef / $total_coef : 0; ?>
                <tr><td align="left">Félicitations</td><td width="40"><?php echo ($sem_avg >= 16) ? 'X' : ''; ?></td></tr>
                <tr><td align="left">Encouragements</td><td width="40"><?php echo ($sem_avg >= 14 && $sem_avg < 16) ? 'X' : ''; ?></td></tr>
                <tr><td align="left">Tableau d'Honneur</td><td width="40"><?php echo ($sem_avg >= 12 && $sem_avg < 14) ? 'X' : ''; ?></td></tr>
                <tr><td align="left">Avertissement</td><td width="40"><?php echo ($sem_avg < 10) ? 'X' : ''; ?></td></tr>
                <tr><td align="left">Blâme</td><td width="40"><?php echo ($sem_avg < 8) ? 'X' : ''; ?></td></tr>
            </table>

            <table class="stats-table recap-table-right">
                <tr><td colspan="2">Moyenne du Semestre</td><td colspan="2" class="val-bg"><?php echo number_format($sem_avg, 2); ?> / 20</td></tr>
                <tr><td colspan="2">Rang</td><td colspan="2" class="val-bg"><?php $r = $rank_map[$student['admission_no']]; echo $r.($r==1?'er':'ème'); ?></td></tr>
                <tr><td colspan="2">Moyenne de la Classe</td><td colspan="2" class="val-bg"><?php echo number_format($class_avg, 2); ?></td></tr>
                <tr>
                    <td width="15%">MAX</td><td class="val-bg"><?php echo number_format($max_v, 2); ?></td>
                    <td width="15%">MIN</td><td class="val-bg"><?php echo number_format($min_v, 2); ?></td>
                </tr>
            </table>
        </div>

        <div class="recap-flex" style="margin-top:10px;">
            <div class="obs-box">
                <div style="font-weight:bold; text-decoration:underline;">Observations / Conseil de Classe</div>
                <div class="writing-lines"></div>
            </div>
            <div style="width:40%; text-align:center; font-weight:bold;">
                <br/><br/>Le Directeur
            </div>
        </div>
    </div>

    <div class="footer-fixed">
        <div class="footer-info">
            <span>Bulletin à conserver précieusement</span>
            <strong>Fait le : <?php echo date('d/m/Y'); ?></strong>
        </div>
        <img src="<?php echo base_url('uploads/marksheet/footer_image.png'); ?>" class="footer-img">
    </div>
</div>
<?php } ?>

<div class="no-print" style="position:fixed; top:20px; right:20px;"><button onclick="downloadPDF()" style="background:#d9534f; color:#fff; padding:10px 20px; border:none; cursor:pointer;">Download PDF</button></div>
<script>
function downloadPDF() {
    const element = document.body;
    html2pdf().set({ margin: 0, filename: 'Bulletin_Semestre.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' } }).from(element).save();
}
</script>
</body>
</html>