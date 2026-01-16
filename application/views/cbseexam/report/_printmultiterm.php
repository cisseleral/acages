<?php

			if (!empty($result)) {
				?>
				
<div class="btn-group  pb10" role="group" aria-label="First group">
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
				foreach ($terms as $term_key => $term_value) {
				?>
					<th rowspan="2" class="text-center"><?php print_r($term_value->name . " (" . $term_value->term_code . ")"); ?></th>
				<?php
				}
				?>
				<?php
				foreach ($terms as $assess_key => $assess_value) {
					$terms_weight_array[] = $assess_value->name . " (" . $assess_value->weightage . ")";
				}

				?>
				<th valign="middle" class="text-center" colspan="4">
					<?php
					//term merge array              
					echo implode(" + ", $terms_weight_array);
					?>
				</th>
				<?php
				?>
			</tr>
			<tr>
				<td valign="middle" class="text-center bolds"><?php echo $this->lang->line('grand_total'); ?> <br /> <?php echo $this->lang->line('out_of'); ?> (100)</td>
				<td valign="middle" class="text-center bolds"><?php echo $this->lang->line('percentage'); ?> (%)</td>
				<td valign="middle" class="text-center bolds"><?php echo $this->lang->line('grade'); ?></td>
				<td valign="middle" class="text-center bolds"><?php echo $this->lang->line('rank'); ?></td>
			</tr>
		</thead>
		<tbody>
			<?php

			if (!empty($result)) {

				foreach ($result as $student_rank_wise_key => $student_value) {
					 
					$grand_total_term_percentage = 0;
					$subject_total_term_percentage = 0;
					$terms_subject_percentage = getFinalTerms($exam_term_exam_assessment,$student_value,$subject_array,$exam_grades);
			?>
					<tr>
						<td><?php echo $student_value['firstname'] . " " . $student_value['middlename'] . " " . $student_value['lastname']; ?></td>
						<td><?php echo $student_value['admission_no']; ?></td>
						<td><?php echo $student_value['class'] . " (" . $student_value['section'] . ")"; ?></td>
						<td><?php echo $this->customlib->dateformat($student_value['dob']); ?></td>
						<?php
						
						foreach ($terms as $term_key => $term_value) {
						?>
							<td class="text-center">
								<?php
				
								$res = getTermMarks($term_value->cbse_term_id, $student_value['terms'], $term_value->weightage,$exam_term_exam_assessment,$subject_array,$terms);
						    

								
						
								
								if(!$res['term_status']){								
									echo "<span class='text text-danger'>". $this->lang->line('term_not_assigned')."</span>";

								}else{
									echo $res['get_marks'] . "/" . $res['maximum_marks'];
								}
								if ($res['maximum_marks'] > 0) {
									$total_term_ = (($res['get_marks'] * 100) / $res['maximum_marks']);
									$subject_total_term_percentage += ($total_term_ * ($term_value->weightage / 100));
								}

								?>
							</td>
						<?php
						}
						?>
						<td class="text-center bolds"><?php echo $terms_subject_percentage['overall_marks'];	?></td>
						<td class="text-center bolds"><?php echo $terms_subject_percentage['percentage']; ?></td>
						<td class="text-center bolds"><?php echo $terms_subject_percentage['grade'];  ?></td>
						<td class="text-center bolds"><?php echo $terms_subject_percentage['rank'];  ?></td>

					</tr>
			<?php

				}
			}

			?>
		</tbody>
	</table>
</div>
				<?php 
			}else{
				?>
				<div class="alert alert-danger"><?php echo $this->lang->line('no_record_found');?></div>
				<?php 
			}
?>

<?php

function getFinalTerms($exam_term_exam_assessment,$student_value,$subject_array,$exam_grades)
{
	$ci =& get_instance();
	$grand_total_gain_marks = 0;
	$grand_total_term_percentage = 0;
	$grand_total_marks=0;

	$return_array=[
		'overall_marks'=>'',
	'percentage'=>'',
	'grade'=>'',
	'rank'=>'',
	];

	foreach ($subject_array as $subject_array_key => $subject_array_value) {
		$subject_grand_total = 0;
		$subject_total_term_percentage = 0;
	
		  foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {
			$subject_total = 0;
			$subject_maximum_total = 0;
			$exam_total_null = false;

			foreach ($assess_value['exams'] as $exam_key => $exam_value) {

			  foreach ($exam_value['exam_assessments'] as $exam_assement_key => $exam_assement_value) {
		


													  $subject_marks_array = getSubjectData_template($student_value['terms'], $assess_value['cbse_term_id'], $exam_key, $subject_array_key, $exam_assement_value['cbse_exam_assessment_type_id']);

													  if ($subject_marks_array['cbse_exam_timetable_assessment_type_id']) {
														if (!$subject_marks_array['marks'] <= 0 ||  $subject_marks_array['marks'] == "N/A") {

														  ($subject_marks_array['is_absent']) ? $ci->lang->line('abs') : $subject_marks_array['marks'];

														  $subject_total += ($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'];
														  $subject_maximum_total += $subject_marks_array['maximum_marks'];
														} else {
														 
														  $subject_total += 0;
														  $subject_maximum_total += 0;
														}
													  }else {
														
													  }
												   
						
			  }
			}
		

			  if ($subject_maximum_total <= 0 && $subject_total <= 0) {

				$subject_maximum_total = 100;
				$subject_total = 100;
			  }

			  $subject_percentage = getPercent($subject_maximum_total, $subject_total);

			  $total_term_ = (($subject_total * 100) / $subject_maximum_total);
			  $subject_total_term_percentage += ($total_term_ * ($assess_value['cbse_term_weight'] / 100));
			  $grand_total_term_percentage += ($total_term_ * ($assess_value['cbse_term_weight'] / 100));

			

			

			$grand_total_gain_marks += $subject_total;
			$grand_total_marks += $subject_maximum_total;
		  }
		
		
	  }


	  $overall_percentage = getPercent((count($subject_array) * 100), $grand_total_term_percentage);
	



	  $return_array=[
		'overall_marks'=>two_digit_float($grand_total_term_percentage, 2) . "/" . count($subject_array) * 100,
	    'percentage'=>two_digit_float($overall_percentage, 2),
	    'grade'=>getGrade($exam_grades, $overall_percentage),
	    'rank'=>$student_value['rank']
	];
return $return_array;

}

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


function getSubjectData_template($term_array, $find_term, $find_exam, $find_subject_id, $find_cbse_exam_assessment_type_id)
{

  $return_array = [
    'maximum_marks' => "",
    'marks' => "",
    'note' => "",
    'is_absent' => "",
    'cbse_exam_timetable_assessment_type_id' => false,
  ];

  if (!empty($term_array)) {

    if (array_key_exists($find_term, $term_array)) {

      if (array_key_exists($find_exam, $term_array[$find_term]['exams'])) {

        if (array_key_exists($find_subject_id, $term_array[$find_term]['exams'][$find_exam]['subjects'])) {
          $result_array = ($term_array[$find_term]['exams'][$find_exam]['subjects'][$find_subject_id]['exam_assessments'][$find_cbse_exam_assessment_type_id]);

          if (!is_null($result_array['cbse_exam_timetable_assessment_type_id'])) {
            $return_array = [
              'maximum_marks' => $result_array['maximum_marks'],
              'marks' => is_null($result_array['marks']) ? "N/A" : $result_array['marks'],
              'note' => $result_array['note'],
              'is_absent' => $result_array['is_absent'],
              'cbse_exam_timetable_assessment_type_id' => true,
            ];
          }
      
        }
      }
    }
  }

  return $return_array;
}


function getTermMarks($term_id, $terms, $term_weight,$exam_term_exam_assessment,$subject_array,$all_terms)
{

	$return_array = [
		'maximum_marks' => 0,
		'get_marks' => 0,
		'subject_weight' => [],
		'term_status' => true

	];


	$get_marks = 0;
	$maximum_marks = 0;
	$term_status =true;
	$subject_weight = [];
	if (!empty($terms)) {

		if (array_key_exists($term_id, $terms)) {
			foreach ($terms[$term_id]['exams'] as $term_key => $term_value) {

				if (!empty($term_value['subjects'])) {

					foreach ($term_value['subjects'] as $subject_key => $subject_value) {

						$subject_max_marks = 0;
						$subject_get_marks = 0;

						foreach ($subject_value['exam_assessments'] as $assessment_key => $assessment_value) {
							if(!is_null($assessment_value['cbse_exam_timetable_assessment_type_id'])){

							$maximum_marks += $assessment_value['maximum_marks'];
							$get_marks += is_null($assessment_value['marks']) ? 0 : $assessment_value['marks'];
							$subject_get_marks += is_null($assessment_value['marks']) ? 0 : $assessment_value['marks'];
							$subject_max_marks += $assessment_value['maximum_marks'];
							}
						}

						if (array_key_exists($subject_value['subject_id'], $subject_weight)) {
							$subject_weight[$subject_value['subject_id']]['subject_get_marks'] += $subject_get_marks;
							$subject_weight[$subject_value['subject_id']]['subject_max_marks'] += $subject_max_marks;
							$subject_weight[$subject_value['subject_id']]['term_weight'] = $term_weight;
						} else {
							$subject_weight[$subject_value['subject_id']] = [
								'subject_get_marks' => $subject_get_marks,
								'subject_max_marks' => $subject_max_marks,
								'term_weight' => $term_weight,
							];
						}
					}
				}
				$term_status =true;
			}
		}else{
			
			if (array_key_exists($term_id, $exam_term_exam_assessment)) {
				        $subject_max_marks = 0;
						$subject_get_marks = 0;

						// print_r($exam_term_exam_assessment[$term_id]['exams']);
						foreach ($exam_term_exam_assessment[$term_id]['exams'] as $ex_key => $ex_value) {
							foreach ($subject_array as $sub_key => $sub_value) {

									foreach ($exam_term_exam_assessment[$term_id]['term_total_assessments'] as $exam_assement_key => $exam_assement_value) {
				         	$maximum_marks += ($exam_assement_value['assesment_type_maximum_marks']);
							$get_marks += ($exam_assement_value['assesment_type_maximum_marks']);

							$subject_get_marks += is_null($exam_assement_value['assesment_type_maximum_marks']) ? 0 : $exam_assement_value['assesment_type_maximum_marks'];
							$subject_max_marks += $exam_assement_value['assesment_type_maximum_marks'];
				            }
							
							$subject_weight[$sub_key]=[
		                    'subject_get_marks'=> $subject_get_marks,
							'subject_max_marks'=> $subject_max_marks,
							'term_weight'=> getTermWeightage($all_terms,$term_id)
							];		
							}
						
						}
					
			
				$term_status =false;
			}          
		}

		$return_array = [
			'get_marks' => $get_marks,
			'maximum_marks' => $maximum_marks,
			'subject_weight' => $subject_weight,
			'term_status' => $term_status
		];
	}

	return $return_array;
}


function getTermWeightage($terms,$term_id){
	foreach ($terms as $t_key => $t_value) {
		if($t_value->cbse_term_id == $term_id){
			return $t_value->weightage;
		}
	}
	return 100;

}



?>