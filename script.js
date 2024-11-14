// Confirmation dialog for delete action
document.addEventListener("DOMContentLoaded", function() {
    const deleteLinks = document.querySelectorAll('a[href*="view_donated_items.php?delete="]');
    
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            const confirmed = confirm("Are you sure you want to delete this item?");
            if (!confirmed) {
                event.preventDefault(); // Prevent the link from being followed
            }
        });
    });
});
