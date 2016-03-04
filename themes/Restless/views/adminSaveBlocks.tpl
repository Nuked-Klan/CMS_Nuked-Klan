@if({{adminBlocksError}} === false)
        %printNotification({{*SUCCESS_BLOCK_EDIT}}, 'success')
        %redirect('index.php?file=Admin&page=theme&op=blocks_management', 2)
@else
        %printNotification({{errorMessage}}, 'error')
        %redirect('index.php?file=Admin&page=theme&op=blocks_management', 2)
@endif