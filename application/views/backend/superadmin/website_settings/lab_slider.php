<?php
$lab_images_json = get_frontend_settings('lab_images');
$lab_images = json_decode($lab_images_json);
?>
<div class="card">
  <div class="card-body">
    <h4 class="header-title">School Laboratories</h4>
    <form method="POST" class="col-12 labSliderSettings" action="<?php echo route('lab_slider/update') ;?>" id = "lab_slider_settings" enctype="multipart/form-data">
      <div class="row justify-content-left">
        <div class="col-12">
          <?php for ($i = 0; $i <5; $i++): ?>
            
            <div class="form-group row mb-3">
              <label class="col-md-3 col-form-label" for="example-fileinput"><?php echo get_phrase('slider_image'); ?> <?php echo $i+1; ?></label>
              <div class="col-md-9 custom-file-upload">
                <div class="wrapper-image-preview" style="margin-left: -6px;">
                  <div class="box" style="width: 250px;">
                    <div class="js--image-preview" style="background-image: url(<?php echo $this->frontend_model->get_slider_image($lab_images[$i]->image); ?>); background-color: #F5F5F5;"></div>
                    <div class="upload-options">
                      <label for="slider_image_<?php echo $i; ?>" class="btn"> <i class="mdi mdi-camera"></i> <?php echo get_phrase('upload_slider'); ?> <?php echo $i+1; ?> <small>(1900 X 1260)</small></label>
                      <input id="slider_image_<?php echo $i; ?>" style="visibility:hidden;" type="file" class="image-upload" name="slider_image_<?php echo $i; ?>" accept="image/*">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endfor; ?>
          <div class="text-center">
            <button type="submit" class="btn btn-secondary col-xl-4 col-lg-4 col-md-12 col-sm-12" onclick="updateLabSliderSettings()"><?php echo get_phrase('update_settings') ;?></button>
          </div>
        </div>
      </div>
    </form>

  </div> <!-- end card body-->
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#description_0').summernote();
    $('#description_1').summernote();
    $('#description_2').summernote();
});
</script>
