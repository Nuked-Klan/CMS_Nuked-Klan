/**
 * Link input event with form functions.
 */
$(document).ready(function() {
    $('#language').change(function() {
        window.location = 'index.php?language=' + $(this).val();
    });
});