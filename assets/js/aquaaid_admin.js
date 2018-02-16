jQuery(document).ready(function($) {

  // Opts init
  NProgress.start();
  NProgress.done();
  toastr.options.closeButton = true;

  // Hide the upload progress bar
  $('progress').hide();


  /**
   * Upload CSV doc
   *
   * @return false (prevent default action)
   */
  $('.upload').on('click', function(e) {
    $('progress').show();
    NProgress.start();

    $.ajax({
      // Your server script to process the upload
      url: 'admin-ajax.php',
      type: 'POST',

      // Form data
      data: new FormData($('.upform')[0]),

      // Tell jQuery not to process data or worry about content-type
      // You *must* include these options!
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
          }, false);
        }
        return myXhr;
      },
      success: function(data) {
        $('progress').hide();
        NProgress.done();
      }
    });

    return false;
  });


  /**
   * Gets locations for location drop down
   *
   * @return void
   */  
  $.get('admin-ajax.php', {action: 'aa_get_locations'}, '', 'json')
    .then(function(res) {
      res.forEach(function(v, i) {
        $('.area-select').append('<option>'+ v.postarea +'</option>');
      })
    });


  /**
   * On change of area select, run fetchDataByLocation
   *
   * @return void
   */
  $('.area-select').on('change', function() {
    var location = $(this).find('option:selected').val();
    if (location === 'null') return;
    NProgress.start();
    fetchDataByLocation(location);
  });


  /**
   * Makes an ajax request to the server to fetch the data based on the selected location
   *
   * @return void
   */  
  function fetchDataByLocation(location) {
    $.get('admin-ajax.php', {action: 'aa_get_data_by_location', location: location}, '', 'json')
      .then(function(response) {
        $('.data-table tbody').empty();
        // For each row, add a new row to the table (should only be 1)
        response.results.forEach(function(v, i) {
          $('.data-table tbody')
            .append('<tr><td class="column-columnname a-col">'+ v.postarea +'</td><td class="column-columnname">'+ response.count +'</td><td class="column-columnname e-col"><input style="width:100%;" type="text" value="'+ v.email +'" /></td><td class="column-columnname e-col"><input style="width:100%;" type="text" value="'+ v.email_copy +'" /></td><td class="column-columnname"><textarea style="width:100%;">'+ v.message +'</textarea></td><td><button class="save-aa button button-primary">Save Changes</button></td></tr>');
        });
        NProgress.done();
      });
  }


  /**
   * Saves the modified data to the server
   *
   * @return void
   */  
  $(document).on('click', '.save-aa', function() {
    NProgress.start();
    var email = $('.data-table').find('.e-col input').val();
    var area = $('.data-table').find('.a-col').text();
    var msg = $('.data-table').find('textarea').val();

    // data object (payload) for post request
    var data = {
      action: 'aa_update_data_by_location', 
      email: email,
      msg: msg,
      area: area
    };

    // Post request to server with payload
    $.post('admin-ajax.php', data, '', 'json')
      .then(function(response) {
        NProgress.done();
        toastr.success(response.result + ' Rows updated successfully', 'Success');
      });

  });


  /**
   * Check file upload size
   *
   * @return void
   */  
  $('.aa_file_upload').on('change', function() {
    var file = this.files[0];
    if (file.size > 1024) {
        //alert('max upload size is 1k')
    }
  });

});