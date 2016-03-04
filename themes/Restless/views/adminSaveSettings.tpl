@if({{adminSettingsError}} === false)
        %printNotification({{*SUCCESS_SETTINGS_EDIT}}, 'success')
        %redirect('index.php?file=Admin&page=theme&op=settings', 2)
@else
        %printNotification({{errorMessage}}, 'error')
        %redirect('index.php?file=Admin&page=theme&op=settings', 2)
@endif