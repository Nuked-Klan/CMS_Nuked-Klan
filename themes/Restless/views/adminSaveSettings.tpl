@if({{adminSettingsError}} === false)
        %printNotification({{*SUCCESS_SETTINGS_EDIT}}, 'index.php?file=Admin&page=theme&op=settings', 'success', false, true)
@else
        %printNotification({{errorMessage}}, 'index.php?file=Admin&page=theme&op=settings', 'error', true, false)
@endif