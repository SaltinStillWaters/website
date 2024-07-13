$(document).ready(function() {
    // Toggle dropdown on ellipsis button click
    $('.ellipsis-dropdown').on('click', function(e) {
        e.stopPropagation();
        $(this).find('.dropdown-menu').toggleClass('show');
    });

    // Close dropdown on click outside
    $(document).click(function(e) {
        if (!$(e.target).closest('.ellipsis-dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });

    // Handle delete post click
    $('.delete-post').on('click', function(e) {
        e.preventDefault();
        if (confirm("Are you sure you want to delete this post?")) {
            var postId = $(this).data('postid');
            $.ajax({
                type: 'POST',
                url: 'delete_post.php', // Create this PHP file to handle deletion
                data: { post_id: postId },
                success: function(response) {
                    // Reload the page or handle success as needed
                    window.location.reload(); // Example: Reload the page after deletion
                },
                error: function(xhr, status, error) {
                    console.error("Error deleting post:", error);
                    // Handle error message display or logging
                }
            });
        }
    });
});