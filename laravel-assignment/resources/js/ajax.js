
$(document).ready(function() {
    // init datatable.
  $(function () {
    var table = $('.data-table').DataTable({
      processing: true,
      serverSide: true,
      //ajax: "{{ route('posts.index') }}",
      //ajax: "{{ url('posts') }}",
      ajax: "/posts",
      columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
        {data: 'title', name: 'title'},
        {data: 'image', name: 'image'},
        {data: 'action', name: 'action', orderable: false, searchable: false},
      ]
    });
  });

  // Create post Ajax request.
  $('#SubmitCreatePostForm').click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    var title = $('#title').val();
    formData.append('title', title);
    formData.append('image', document.getElementById("image").files[0]);
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
        url: "/posts",
        method: 'post',
        data: formData,
        processData: false,
        contentType: false,
        success: function(result) {
            if(result.errors) {
              $('.alert-danger').html('');
              $.each(result.errors, function(key, value) {
                $('.alert-danger').show();
                $('.alert-danger').append('<strong><li>'+value+'</li></strong>');
              });
            } else {
              $('.alert-danger').hide();
              $('.alert-success').show();
              $('.dataTable').DataTable().ajax.reload();
              $('.alert-success').hide();
              $('.modal-backdrop').remove();
              $('#CreatePostModal').modal('hide');
              document.getElementById('formPost').reset();
            }
        }
      });
  });

  // Get single post in EditModel
  $('.modelClose').on('click', function(){
    $('#EditPostModal').hide();
  });
  var id;
  $('body').on('click', '#getEditPostData', function(e) {
      // e.preventDefault();
      $('.alert-danger').html('');
      $('.alert-danger').hide();
      id = $(this).data('id');
      $.ajax({
          url: "posts/"+id+"/edit",
          method: 'GET',
          // data: {
          //     id: id,
          // },
          success: function(result) {
            //console.log(result);
            $('#EditPostModalBody').html(result.html);
            $('#EditPostModal').show();
          }
      });
  });

  // Update post Ajax request.
  $('#SubmitEditPostForm').click(function(e) {
      e.preventDefault();
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
          url: "posts/"+id,
          method: 'PUT',
          data: {
            title: $('#editTitle').val(),
            description: $('#editDescription').val(),
          },
          success: function(result) {
              if(result.errors) {
                $('.alert-danger').html('');
                $.each(result.errors, function(key, value) {
                  $('.alert-danger').show();
                  $('.alert-danger').append('<strong><li>'+value+'</li></strong>');
                });
              } else {
                $('.alert-danger').hide();
                $('.alert-success').show();
                $('.dataTable').DataTable().ajax.reload();
                $('#EditPostModal').hide();
                document.getElementById('formPost').reset();
              }
          }
      });
  });

  // Delete post Ajax request.
  var deleteID;
  $('body').on('click', '#getDeleteId', function(){
    deleteID = $(this).data('id');
  })
  $('#SubmitDeletePostForm').click(function(e) {
    e.preventDefault();
    var id = deleteID;
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      url: "posts/"+id,
      method: 'DELETE',
      success: function(result) {
        $('.dataTable').DataTable().ajax.reload();
        $('.modal-backdrop').remove();
        $('#DeletePostModal').modal('hide');
        document.getElementById('formPost').reset();
      }
    });
  });
});