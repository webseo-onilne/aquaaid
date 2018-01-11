jQuery(document).ready(function($) {
  console.log('admin area script');
  $('progress').hide();

  $('.upload').on('click', function(e) {
    $('progress').show();

    $.ajax({
      // process the upload
      url: 'admin-ajax.php',
      type: 'POST',

      // Form data
      data: new FormData($('.upform')[0]),

      // Tell jQuery not to process data or worry about content-type
      cache: false,
      contentType: false,
      processData: false,

      // Custom XMLHttpRequest
      xhr: function() {
        var myXhr = $.ajaxSettings.xhr();
        if (myXhr.upload) {
            // For handling the progress of the upload
            myXhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    $('progress').attr({
                        value: e.loaded,
                        max: e.total,
                    });
                }
            } , false);
        }
        return myXhr;
      },
      success: function(data) {
        console.log(data);
        $('progress').hide();
      }
    });

    return false;
  })

  $('.aa_file_upload').on('change', function() {
    var file = this.files[0];
    if (file.size > 1024) {
        //alert('max upload size is 1k')
    }
  });

});