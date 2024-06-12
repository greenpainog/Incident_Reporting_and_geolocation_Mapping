$(document).ready(function() {
    $('#dataForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = $(this).serialize(); // Serialize form data

        $.ajax({
            type: 'POST',
            url: 'filllocation.php',
            data: formData,
            success: function(response) {
                $('#response').html(response); // Display the response from the server
            },
            error: function() {
                $('#response').html('An error occurred while submitting the data.');
            }
        });
    });
});