
<?php
if (!empty($result)) {
?>

	<div class="btn-group pb10" role="group" aria-label="First group">
		<button type="button" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('print'); ?>" onclick="printDiv('headerTable')"><i class="fa fa-print"></i></button>
		<button type="button" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('download_excel'); ?>" onclick="fnExcelReport()"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
	</div>
	<div class="table-responsive" id="headerTable">
		<h4 id="print_title"><?php echo $this->lang->line('template_wise_report'); ?></h4>
		<table class="table table-bordered table-b vertical-middle">
			<thead>
				<tr>
					<th rowspan="2" class=""><?php echo $this->lang->line('student'); ?></th>
					<th rowspan="2" class=""><?php echo $this->lang->line('admission_no'); ?></th>
					<th rowspan="2" class=""><?php echo $this->lang->line('class'); ?></th>
					<th rowspan="2" class=""><?php echo $this->lang->line('date_of_birth'); ?></th>

					<?php

					if (!empty($template->exams) && property_exists($template,'exams')) {
						foreach ($template->exams as $exam_key => $exam_value) {

					?>
							<th rowspan="2" class="text-center"><?php echo $exam_value->name; ?>
						
						</th>
					<?php
					
						}
					}
					?>
					<?php

					if (!empty($template->exams) && property_exists($template,'exams')) {
						$exam_weight_array = [];
						foreach ($template->exams as $exam_key => $exam_value) {
							$exam_weight_array[] = $exam_value->name . " (" . $exam_value->weightage . ")";
						}
					?>
						<th colspan="4" class="text-center"><?php echo implode(" + ", $exam_weight_array); ?></th>
					<?php
					}
					?>
				</tr>
				<tr>
					<th class="text-center"><?php echo $this->lang->line('overall_marks'); ?></th>
					<th class="text-center"><?php echo $this->lang->line('percentage'); ?> (%)</th>
					<th class="text-center"><?php echo $this->lang->line('grade'); ?></th>
					<th class="text-center"><?php echo $this->lang->line('rank'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php

				foreach ($result as $student_rank_key => $student_value) {

					$result_value = $result[$student_value['student_session_id']];

					$grand_total_term_percentage = 0;
					$subject_total_term_percentage = 0;
					$exams_subject_percentage = getFinalTerms($exam_term_exam_assessment,$student_value,$subject_array,$exam_grades);
					
					
				?>
					<tr>
						<td><?php echo $result_value['firstname'] . " " . $result_value['middlename'] . " " . $result_value['lastname']; ?></td>
						<td><?php echo $result_value['admission_no']; ?></td>
						<td><?php echo $result_value['class'] . " (" . $result_value['section'] . ")"; ?></td>

						<td>
							<?php echo $this->customlib->dateformat($result_value['dob']); ?>
						</td>
						<?php
						
						if (!empty($template->exams) && property_exists($template,'exams')) {
							foreach ($template->exams as $exam_key => $exam_value) {

						?>
								<td class="text-center bolds">
									<?php
									$res = getExamMarks($exam_value->cbse_exam_id, $result_value['exams'], $exam_value->weightage);
								
							
									echo $res['get_marks'] . "/" . $res['maximum_marks'];
									if ($res['maximum_marks'] > 0) {
										$total_term_ = (($res['get_marks'] * 100) / $res['maximum_marks']);
										$subject_total_term_percentage += ($total_term_ * ($exam_value->weightage / 100));
									}
									?>
								</td>
						<?php
							}
						}
						?>

						<td class="text-center bolds">
							<?php
							echo 	$exams_subject_percentage['overall_marks'];
								   
							// $subject_term = 0;

							// if (!empty($exams_subject_percentage)) {
							// 	foreach ($exams_subject_percentage as $subject_key => $subject_value) {

							// 		$subject_term += $subject_value['terms_subject_percentage'];
							// 	}
							// }
							// echo two_digit_float($subject_term) . "/" . (count($subject_array) * 100) 
							?>
							</td>
						<td class="text-center bolds"><?php echo 	$exams_subject_percentage['percentage']; ?></td>
						<td class="text-center bolds"><?php  echo 	$exams_subject_percentage['grade'];  ?></td>
						<td class="text-center bolds"><?php  echo 	$exams_subject_percentage['rank'];  ?></td>

					</tr>
				<?php
				}

				?>
			</tbody>
		</table>
	</div>
<?php
}
?>
<?php

// function searcharray($value, $key, $array) {
// foreach ($array as $k => $val) {  
// if ($val[$key] == $value) {
// return $val['rank'];
// }
// }
// return null;
// }

function getGrade($grade_array, $Percentage)
{

	if (!empty($grade_array)) {
		foreach ($grade_array as $grade_key => $grade_value) {
			if ($grade_value->minimum_percentage <= $Percentage) {
				return $grade_value->name;
				break;
			} elseif ($grade_value->maximum_percentage <= $Percentage && $grade_value->minimum_percentage >= $Percentage) {

				return $grade_value->name;
				break;
			}
		}
	}
	return "-";
}

function getFinalTerms($exam_term_exam_assessment,$student_value,$subject_array,$exam_grades)
{
	$ci =& get_instance();
	$grand_total_term_percentage = 0;
	$grand_total_exam_weight_percentage = 0;
	$return_array=[
		'overall_marks'=>'',
	'percentage'=>'',
	'grade'=>'',
	'rank'=>'',
	];
	
	foreach ($subject_array as $subject_array_key => $subject_array_value) {

		$subject_grand_total = 0;

		$subject_total_weight_percentage = 0;
	  ?>
	
		  <?php
		  foreach ($exam_term_exam_assessment as $exam_key => $exam_value) {

			$exam_subject_total = 0;
			$exam_subject_maximum_total = 0;
			foreach ($exam_value['exam_total_assessments'] as $exam_assessment_key => $exam_assessment_value) {
		

				$subject_marks_array = getSubjectData($student_value, $exam_value['exam_id'], $subject_array_key, $exam_assessment_value['assesment_type_id']);
				

				
				if ($subject_marks_array['cbse_exam_timetable_assessment_type_id']) {
				  if (!$subject_marks_array['marks'] <= 0 ||  $subject_marks_array['marks'] == "N/A") {
					 ($subject_marks_array['is_absent']) ? $ci->lang->line('abs') : $subject_marks_array['marks'];

					$exam_subject_total += ($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'];
					$exam_subject_maximum_total += $subject_marks_array['maximum_marks'];
				  } else {
					"-";
					$exam_subject_total += 0;
					$exam_subject_maximum_total += 0;
				  }
				} else {
			 "<b>xx</b>";
				}

			}
			
			$subject_percentage = getPercent($exam_subject_maximum_total, $exam_subject_total);
                      
			// $subject_total_weight_percentage += ($subject_percentage * ($exam_value['weightage'] / 100));

			if($exam_subject_maximum_total > 0){
			  $subject_total_weight_percentage += ($subject_percentage * ($exam_value['weightage'] / 100));
			}else{
			  $subject_total_weight_percentage+=$exam_value['weightage'];
			}


	
		  }
		  
		 $grand_total_exam_weight_percentage += $subject_total_weight_percentage;
	  }
	  $overall_percentage = getPercent((count($subject_array) * 100), $grand_total_exam_weight_percentage);
	  $return_array=[
		'overall_marks'=>two_digit_float($grand_total_exam_weight_percentage, 2) . "/" . count($subject_array) * 100,
		'percentage'=>two_digit_float($overall_percentage, 2),
		'grade'=>getGrade($exam_grades, $overall_percentage),
		'rank'=>$student_value['rank']
	  ];
return $return_array;
	  
	  

}


function getExamMarks($exam_id, $exams, $exam_weight)
{

	$return_array = [
		'maximum_marks' => "",
		'get_marks' => "",
		'subject_weight' => [],
	];

	$get_marks = 0;
	$maximum_marks = 0;
	$subject_weight = [];
	if (!empty($exams)) {

		if (array_key_exists($exam_id, $exams)) {

			foreach ($exams[$exam_id]['subjects'] as $subjects_key => $subjects_value) {

				$subject_max_marks = 0;
				$subject_get_marks = 0;

				foreach ($subjects_value['exam_assessments'] as $subject_key => $subject_value) {
					
					if(!is_null($subject_value['cbse_exam_timetable_assessment_type_id'])){
						$maximum_marks += $subject_value['maximum_marks'];
						$get_marks += $subject_value['marks'];
						$subject_get_marks += is_null($subject_value['marks']) ? 0 : $subject_value['marks'];
						$subject_max_marks += $subject_value['maximum_marks'];
					}
		
					
				}

				if (array_key_exists($subjects_value['subject_id'], $subject_weight)) {
					$subject_weight[$subjects_value['subject_id']]['subject_get_marks'] += $subject_get_marks;
					$subject_weight[$subjects_value['subject_id']]['subject_max_marks'] += $subject_max_marks;
					$subject_weight[$subjects_value['subject_id']]['exam_weightage'] = $exam_weight;
				} else {
					$subject_weight[$subjects_value['subject_id']] = [
						'subject_get_marks' => $subject_get_marks,
						'subject_max_marks' => $subject_max_marks,
						'term_weight' => $exam_weight,
					];
				}
			}
		}
		$return_array = [
			'get_marks' => $get_marks,
			'maximum_marks' => $maximum_marks,
			'subject_weight' => $subject_weight,
		];
	}
	return $return_array;
}

// function unique_array($my_array, $key) { 
// $result = array(); 
// $i = 1; 
// $key_array = array(); 

// foreach($my_array as $val) { 
// if (!in_array($val[$key], $key_array)) { 
// $key_array[$i] = $val[$key]; 
// $result[$i] = $val['rank_percentage']; 
// $i++; 
// } 
// } 
// return $result; 
// }  
?>