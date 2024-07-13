window.addEventListener("scroll", function() {
    var header = document.querySelector("header");
    header.classList.toggle("sticky", window.scrollY > 0);
});

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
    $(document).ready(function() {
    // Handle delete post click
        $(document).on('click', '.delete-post', function(e) {
            e.preventDefault();
            if (confirm("Are you sure you want to delete this post?")) {
                var postId = $(this).data('postid');
                $.ajax({
                    type: 'POST',
                    url: '../backend/forums/delete_post.php', // Adjust the path based on your project structure
                    data: { post_id: postId },
                    success: function(response) {
                        // Reload the page after successful deletion
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error deleting post:", error);
                        alert("Error deleting post. Please try again later.");
                    }
                });
            }
        });
    });

});