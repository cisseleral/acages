<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Invoices</title>
    <style>
        /* Force the browser to use the full paper area */
        @page { 
            size: A4; 
            margin: 0; 
        }
        
        body { 
            margin: 0; 
            padding: 0; 
            font-family: 'Arial', sans-serif; 
            background: #eee; 
        }

        * { 
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important; 
            box-sizing: border-box; 
        }

        /* 3. YOUR PREVIEW BOX: Locked at 190mm for both Screen and Print */
        .invoice-container { 
            width: 190mm; 
            min-height: 270mm; 
            padding: 10mm; 
            margin: 20px auto; 
            background: #fff; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative; 
            page-break-after: always;
            overflow: hidden;
        }

        @media print {
            body { background: none; }
            /* FORCE the print to stay 190mm */
            .invoice-container { 
                margin: 5mm auto; 
                box-shadow: none; 
                width: 190mm !important; 
                padding: 10mm; 
            }
        }

        .header-img { width: 100%; height: auto; display: block; margin-bottom: 8px; }
        .exam-banner { 
            background: #103153 !important; color: #fff !important; padding: 3px; 
            text-align: center; font-weight: bold; font-size: 20px; margin-bottom: 10px;
        }

        .identity-table { width: 100%; margin-bottom: 2px; border-collapse: collapse; font-size: 14px; line-height: 1.7; }
        .photo-box { width: 80px; height: 70px; border: 1px solid #ddd; object-fit: cover; }

        .fee-table { width: 100%; table-layout: fixed; border-collapse: collapse; margin-bottom: 5px; }
        .fee-table th { background: #f2f2f2 !important; border: 1px solid #ccc; padding: 10px; font-size: 13px; text-align: center; }
        .fee-table td { border: 1px solid #eee; padding: 5px; font-size: 13px; word-wrap: break-word; }
        
        .total-row td { background: #f9f9f9 !important; font-weight: bold; font-size: 15px; border-top: 2px solid #333 !important; }

        .warning-line {
            margin-top: 10px;
            padding: 12px 15px;
            background: #fff5f5 !important;
            color: #b94a48;
            font-size: 14px;
            text-align: left;
            font-weight: bold;
            border-left: 5px solid #d9534f;
            line-height: 1.5;
            white-space: nowrap;
        }
        .warning-line u { color: #000; font-size: 16px; text-decoration: underline; }

        .footer-flexible { border-top: 1px solid #eee; margin-top: 10px; padding-top: 5px; text-align: center; font-size: 11px; color: #777; }
    </style>
</head>
<body onload="window.print();">
    <?php 
    function formatMoney($amount) {
        return number_format((float)$amount, (floor($amount) == $amount ? 0 : 2), '.', ' ');
    }

    if (!empty($student_list)) { 
        foreach ($student_list as $student) { ?>
        <div class="invoice-container">
            <img src="<?php echo base_url('uploads/print_headerfooter/general_purpose/header_image.png'); ?>" class="header-img">
            <div class="exam-banner"><?php echo $this->lang->line('monthly_bills_and_fees_reminder'); ?></div>

            <table class="identity-table">
                <tr>
                    <td width="110">
                        <img src="<?php echo base_url($student['image']); ?>" class="photo-box" onerror="this.src='<?php echo base_url('uploads/student_images/no_image.png'); ?>'">
                    </td>
                    <td valign="top" style="padding-left: 5px;">
                        <strong><?php echo $this->lang->line('name'); ?>:</strong> <?php echo $student['firstname'].' '.$student['lastname']; ?><br>
                        <strong><?php echo $this->lang->line('admission_no'); ?>:</strong> <?php echo $student['admission_no']; ?><br>
                        <strong><?php echo $this->lang->line('class'); ?>:</strong> <?php echo $student['class'].' ('.$student['section'].')'; ?>
                    </td>
                    <td valign="top" style="text-align: right;">
                        <strong><?php echo $this->lang->line('session'); ?>:</strong> <?php echo $sch_setting->session; ?><br>
                        <strong><?php echo $this->lang->line('generation_date'); ?>:</strong> <?php echo date("d-m-Y"); ?>
                    </td>
                </tr>
            </table>

            <table class="fee-table">
                <thead>
                    <tr>
                        <th style="width: 40%;"><?php echo $this->lang->line('fees_code'); ?></th>
                        <th style="width: 20%;"><?php echo $this->lang->line('due_date'); ?></th>
                        <th style="width: 12%;"><?php echo $this->lang->line('status'); ?></th>
                        <th style="width: 12%;"><?php echo $this->lang->line('amount'); ?></th>
                        <th style="width: 10%;"><?php echo $this->lang->line('paid'); ?></th>
                        <th style="width: 10%;"><?php echo $this->lang->line('balance'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($student['fees_list'] as $fee_group) {
                        foreach ($fee_group->fees as $fee) { ?>
                        <tr>
                            <td><?php echo $fee->code; ?></td>
                            <td><?php echo date("d-m-Y", strtotime($fee->due_date)); ?></td>
                            <td>
                                <?php 
                                    if ($fee->calculated_paid > 0 && $fee->calculated_balance > 0) {
                                        echo "<span style='color:orange; font-weight:bold;'>" . $this->lang->line('partial') . "</span>";
                                    } elseif (strtotime($fee->due_date) < strtotime(date('Y-m-d'))) {
                                        echo "<span style='color:red; font-weight:bold;'>" . $this->lang->line('overdue') . "</span>";
                                    } else {
                                        echo $this->lang->line('upcoming');
                                    }
                                ?>
                            </td>
                            <td><?php echo formatMoney($fee->amount); ?></td>
                            <td><?php echo formatMoney($fee->calculated_paid); ?></td>
                            <td><strong><?php echo formatMoney($fee->calculated_balance); ?></strong></td>
                        </tr>
                    <?php } } ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="5" style="text-align: right; padding-right: 15px;"><?php echo strtoupper($this->lang->line('total')); ?> A PAYER:</td>
                        <td><?php echo formatMoney($student['total_balance_to_pay']); ?></td>
                    </tr>
                </tfoot>
            </table>

            <?php if (!empty($student['max_due_date'])) { ?>
            <div class="warning-line">
                <?php echo $this->lang->line('bill_warning_text'); ?> 
                <u><?php echo date("d-m-Y", strtotime($student['max_due_date'])); ?></u> 
                <?php echo ' ' . strtoupper($this->lang->line('due_date_strict')); ?>
            </div>
            <?php } ?>

            <div class="footer-flexible">
                <?php echo $sch_setting->name; ?> | <?php echo $sch_setting->phone; ?> | <?php echo $sch_setting->email; ?> |
                <?php echo $sch_setting->address; ?>
            </div>
        </div>
    <?php } } ?>
</body>
</html>