$(document).ready(function() {
    let sortItems = $('.sort-button');
    sortItems.on('click', function() {
      let column = $(this).data('column');
      let url = 'index.php?category=' + column;
      window.location.href = url;
    });
  

    $('#editProfilePic').on('click', function(event) {
      event.preventDefault(); // Prevent the link's default action
      $('#editModalProfile').modal('show'); // Show the modal with the specified ID
    });
  });
          

      

