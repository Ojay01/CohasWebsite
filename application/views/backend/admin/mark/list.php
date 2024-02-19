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
$count = 1; 
?>
<?php if (count($marks) > 0): ?>
    <table id="marks-table" class="table table-bordered table-responsive-sm" width="100%">
        <thead class="thead-dark">
            <tr>
            <th><?php echo get_phrase('S/N'); ?></th>
                <th><?php echo get_phrase('student_name'); ?></td>
                <th><?php echo get_phrase('mark'); ?></td>
                
            </tr>
        </thead>
        <tbody>
        <?php foreach($marks as $mark):
                $student = $this->db->get_where('students', array('id' => $mark['student_id']))->row_array(); ?>
                <tr>
                <td><?php echo $count++; ?></td> 
                    <td><?php echo $this->user_model->get_user_details($student['user_id'], 'name'); ?></td>
                    <td><?php echo $mark['mark_obtained']; ?> </td>
                </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-center">
       
        <button class="btn btn-primary" onclick="exportToExcel()">Export to Excel</button>
    </div>
<?php else: ?>
<?php include APPPATH.'views/backend/empty.php'; ?>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
    function exportToExcel() {
        /* Table to Excel */
        var tab_text = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        tab_text = tab_text + '<head><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
        tab_text = tab_text + '<x:Name>Worksheet</x:Name>';
        tab_text = tab_text + '<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
        tab_text = tab_text + '</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>';
        tab_text = tab_text + "<table border='1px'>";
        var table = document.getElementById('marks-table');

        // Create a new Excel workbook
        var wb = XLSX.utils.book_new();

        // Convert the table to a worksheet
        var ws = XLSX.utils.table_to_sheet(table);

        
        // Add the worksheet to the workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Worksheet');

        // Save the workbook as a binary Excel file
        var wbout = XLSX.write(wb, { bookType: 'xlsx', bookSST: true, type: 'binary' });

         var className = "<?php echo $this->db->get_where('classes', array('id' => $class_id))->row('name'); ?>";
        var sectionName = "<?php echo $this->db->get_where('sections', array('id' => $section_id))->row('name'); ?>";
        var subjectName = "<?php echo $this->db->get_where('subjects', array('id' => $subject_id))->row('name'); ?>";
        var fileName = className + '_' + sectionName + '_' + subjectName + '_marks.xlsx';

        saveAs(new Blob([s2ab(wbout)], { type: 'application/octet-stream' }), fileName);
    }

    // Function to convert string to ArrayBuffer
    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
    }
</script>

