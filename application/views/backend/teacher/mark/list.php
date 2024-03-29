<div class="row mb-3">
  <div class="col-md-4"></div>
  <div class="col-md-4 toll-free-box text-center text-white pb-2" style="background-color: #6c757d; border-radius: 10px;">
    <h4><?php echo get_phrase('manage_marks'); ?></h4>
    <span><?php echo get_phrase('class'); ?> : <?php echo $this->db->get_where('classes', array('id' => $class_id))->row('name'); ?></span><br>
    <span><?php echo get_phrase('section'); ?> : <?php echo $this->db->get_where('sections', array('id' => $section_id))->row('name'); ?></span><br>
    <span><?php echo get_phrase('subject'); ?> : <?php echo $this->db->get_where('subjects', array('id' => $subject_id))->row('name'); ?></span>
  </div>
</div>
<?php
$school_id = school_id();
$marks = $this->crud_model->get_marks($class_id, $section_id, $subject_id, $exam_id)->result_array();
$exam_ending_date = $this->db->get_where('exams', array('id' => $exam_id))->row('ending_date');
$formatted_date = date('D, d-M-Y', $exam_ending_date);


$check_permission = has_permission($class_id, $section_id, 'marks', $subject_id);
?>
<div>Ending Date of Exam: <?php echo $formatted_date; ?></div>

<?php if ($check_permission): ?>
  <?php if (count($marks) > 0): ?>
    <table class="table table-bordered table-responsive-sm" width="100%">
      <thead class="thead-dark">
        <tr>
          <th><?php echo get_phrase('student_name'); ?></td>
            <th><?php echo get_phrase('mark'); ?></td>
              
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($marks as $mark):
                    $student = $this->db->get_where('students', array('id' => $mark['student_id']))->row_array(); ?>
                    <tr>
                      <td><?php echo $this->user_model->get_user_details($student['user_id'], 'name'); ?></td>
                     <td>
             <?php 
                $current_date = strtotime(date('Y-m-d')); // Get current date without time
                $readonly = $exam_ending_date < $current_date ? 'readonly' : ''; // Check if exam ending date has passed
              ?>
              <input class="form-control" type="number" id="mark-<?php echo $mark['student_id']; ?>" max="20" name="mark" placeholder="mark" min="0" value="<?php echo $mark['mark_obtained']; ?>" required onchange="get_grade(this.value, this.id)" <?php echo $readonly; ?>>
            </td>
                      
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <?php
$current_date = strtotime(date('Y-m-d')); 
$submit = $exam_ending_date < $current_date;
?>

<?php if (!$submit): ?>
    <div class="text-center">
        <button class="btn btn-success" onclick="submit_marks()"><?php echo get_phrase('submit'); ?></button>
    </div>
<?php endif; ?>

            <?php else: ?>
              <?php include APPPATH.'views/backend/empty.php'; ?>
            <?php endif; ?>
          <?php else: ?>
            <div class="col-md-12 text-center">
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading"><?php echo get_phrase('access_denied'); ?>!</h4>
                    <hr>
                    <p class="mb-0"><?php echo get_phrase('sorry_you_are_not_permitted_to_access_this_view').'. <br/>'.get_phrase('admin_handles_it'); ?>.</p>
                </div>
            </div>
          <?php endif; ?>


<script>
    function mark_update(student_id){
        var class_id = '<?php echo $class_id; ?>';
        var section_id = '<?php echo $section_id; ?>';
        var subject_id = '<?php echo $subject_id; ?>';
        var exam_id = '<?php echo $exam_id; ?>';
        var mark = $('#mark-' + student_id).val();
        var comment = $('#comment-' + student_id).val();
        if(subject_id != ""){
            $.ajax({
                type : 'POST',
                url : '<?php echo route('mark/mark_update'); ?>',
                data : {student_id : student_id, class_id : class_id, section_id : section_id, subject_id : subject_id, exam_id : exam_id, mark : mark, comment : comment},
                success : function(response){
                    success_notify('<?php echo get_phrase('mark_hass_been_updated_successfully'); ?>');
                }
            });
        }else{
            toastr.error('<?php echo get_phrase('required_mark_field'); ?>');
        }
    }

    function get_grade(exam_mark, id){
        $.ajax({
            url : '<?php echo route('get_grade'); ?>/'+exam_mark,
            success : function(response){
                $('#grade-for-'+id).text(response);
            }
        });
    }

    function submit_marks() {
        // Loop through each row and call mark_update function
        $('input[name="mark"]').each(function(index, element) {
            var student_id = $(element).attr('id').replace('mark-', '');
            mark_update(student_id);
        });
    }
</script>
