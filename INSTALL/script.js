var busy = false;
var errors = 0;
var install = false;
var ajaxBusy = false;
var arrayTablesInstall = new Array(
                            "action", "banned", "block", "calendar", "comment", "comment_mod", "config", "contact", "defie", "discussion", "downloads",
                            "downloads_cat", "erreursql", "forums", "forums_cat", "forums_messages", "forums_threads", "forums_rank",
                            "forums_reads", "forums_poll", "forums_options", "forums_vote", "gallery", "gallery_cat", "games", "games_prefs", "guestbook",
                            "irc_awards", "liens", "liens_cat", "match", "match_files", "modules", "nbconnecte", "news", "news_cat", "notification", "recrute", "sections",
                            "sections_cat", "serveur", "serveur_cat", "sessions", "smilies", "sondage", "sondage_check", "sondage_data", "staff", "staff_cat", "staff_rang", "staff_status", "stats", "stats_visitor",
                            "suggest", "shoutbox", "team", "team_rank", "tmpses", "userbox", "users", "users_detail", "vote"
                        );
var arrayTablesUpdate = new Array(
                            "table_action", "table_banned", "table_comment_mod", "table_contact", "table_discussion", "table_erreursql", "table_games", "table_match", "table_match_files",
                            "table_notification", "table_tmpses", "table_users", "table_smilies", "update_bbcode", "update_config", "update_pass", "remove_style", "remove_editeur", "remove_package_manager",
                            "remove_turkish.lang.php", "table_forums_read"
                        );
var nbTablesInstall = arrayTablesInstall.length;
var nbTablesUpdate = arrayTablesUpdate.length;
var progress_install = 100/nbTablesInstall;
var progress_update = 100/nbTablesUpdate;
var i = 1;
var j = 0;  

function writeInfo(type, prefix, table, txt, status){
    regTable = new RegExp('table');
    regUpdate = new RegExp('update');
    regRemove = new RegExp('remove');
    regConfig = new RegExp('config');
    regTurkish = new RegExp('turkish');
    $("#loading_img").remove();
    br = document.createElement("br");
    $("#log_install").append(br);
    if(status == "OK"){
        if(type == 'update'){
            if(regTable.test(table)){
                table = table.substr(6);
                txt_info = "Table "+prefix+"_"+table+" "+txt[0];
            }
            else if(regUpdate.test(table)){
                table = table.substr(7);
                if(regConfig.test(table)){
                    txt_info = table+" "+txt[2];
                }
                else{
                    txt_info = table+" "+txt[1];
                }
            }
            else if(regRemove.test(table)){
                table = table.substr(7);
                if(regTurkish.test(table)){
                    txt_info = table+" "+txt[4];
                }
                else{
                    txt_info = "Table "+prefix+"_"+table+" "+txt[3];
                }
            }
        }
        else{
            txt_info = "Table "+prefix+"_"+table+" "+txt;
        }
    }
    else{
        txt_info = txt+" "+prefix+"_"+table;
    }                                
    $("#log_install").append(txt_info);
    $("#log_install").append("<img src=\"images/loading.gif\" alt=\"\" id=\"loading_img\" />");  
}

function writeError(text, errorMsg){
    $("#loading_img").remove();
    br = document.createElement("br");
    $("#log_install").append(br);
    txt = text+" "+errorMsg;
    $("#log_install").append("<strong>"+txt+"</strong>");
    $("#log_install").append("<img src=\"images/loading.gif\" alt=\"\" id=\"loading_img\" />");  
}

function writeComplete(txt_end){
    $("#loading_img").remove();
    br = document.createElement("br");
    $("#log_install").append(br);
    
    $("#log_install").append(txt_end);
    $("#log_install").scrollTop(1000);
    busy = false;
    i = 1;
    j = 0;
    errors = 0;
}

function queue_install(type){
    if(type == 'install'){
        nb = nbTablesInstall;
        array = arrayTablesInstall;
    }
    else if(type == 'update'){
        nb = nbTablesUpdate;
        array = arrayTablesUpdate;
    }
    if(j < nb){
        if(ajaxBusy === false){
            ajaxBusy = true;
            ajaxTable(array[j]);            
            j++;
        }
        setTimeout("queue_install('"+install+"')", 300);
    }
    else{
        if(ajaxBusy === false){
            viewEnd();
        }
        else{
            setTimeout("queue_install('"+install+"')", 300);
        }
    }
}

function submit(type){
    if(install === true && type == 'install'){
        window.location="index.php?action=checkUserAdmin";
    }
    else if(install === true && type == 'update'){
        window.location="index.php?action=updateConfig";
    }
    else{
        if(type == 'install'){
            start_install();
        }
        else if(type == 'update'){
            start_update();
        }
    }
}

function verifFormBDD(type, form, wait, error_host, error_login, error_db, error_prefix){
    var host = $('input[name="db_host"]');
    var user = $('input[name="db_user"]');
    var pass = $('input[name="db_pass"]');
    var dbname = $('input[name="db_name"]');
    var prefix = $('input[name="db_prefix"]');
    var bddError = null;
    var formErrors = 0;
   $('input[id]').each(function(){
        if($(this).attr('type') == 'text' || $(this).attr('type') == 'password'){
            if(($(this).val() == '' && $(this).attr('name') != 'db_pass' && $(this).attr('name') != 'db_prefix') || ($(this).attr('name') == 'db_pass' && user.val() != 'root' && $(this).val() == '')){
                $(this).addClass('error');
                formErrors++;
            }
        }
    });
    if(formErrors != 0){
        return;
    }
    else{
        $("#infos").text(wait+"  ");
        $("#infos").append("<img src=\"images/loading.gif\" alt=\"\" id=\"loading_img\" />");
        if(type == 'update'){
            var typeurl = "index.php?action=testBddConnect&type=update";
        }
        else{
            var typeurl = "index.php?action=testBddConnect";
        }
        password = encodeURIComponent(pass.val());
        $.ajax({
            async: false,
            type: "POST",
            url: typeurl,
            data: "db_host="+host.val()+"&db_user="+user.val()+"&db_pass="+password+"&db_name="+dbname.val()+"&db_prefix="+prefix.val()
        }).done(function(txt) {
            if(txt == "OK"){
                bddError = false;
            }
            else{
                bddError = true;
                if(txt == 'error_host'){
                    $('#infos').html(error_host);
                    $("#loading_img").remove();
                    host.addClass('error');
                }
                else if(txt == 'error_login'){
                    $('#infos').html(error_login);
                    $("#loading_img").remove();
                    if(type == 'install'){
                        user.addClass('error');
                    }
                    pass.addClass('error');
                }
                else if(txt == 'error_db'){
                    $('#infos').html(error_db);
                    $("#loading_img").remove();
                    dbname.addClass('error');
                }
                else if(txt == 'error_prefix'){
                    $('#infos').html(error_prefix);
                    $("#loading_img").remove();
                    prefix.addClass('error');
                }
                else{
                    $('#infos').html(txt);
                }
            }
        });
        if(bddError == false){
            $('#'+form).submit();
        }
    }
}

function verifFormAdmin(form, wait, error_pseudo, error_pass, error_pass2, error_mail){
    var pseudo = $('input[name="pseudo"]');
    var pass = $('input[name="pass"]');
    var pass2 = $('input[name="pass2"]');
    var mail = $('input[name="mail"]');
    var regpseudo = new RegExp('[\$\^\(\)\'"?%#<>,;:]');
    var regmail = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
    $('#infos').html('&nbsp;');
    if(pseudo.val().length < 3 || pseudo.val() == '' || regpseudo.test(pseudo.val())){
        pseudo.addClass('error');
        $('#infos').html(error_pseudo);
        return;
    }
    if(pass.val() == ''){
        pass.addClass('error');
        $('#infos').html(error_pass);
        return;
    }
    if(pass2.val() == '' || pass.val() != pass2.val()){
        pass2.addClass('error');
        $('#infos').html(error_pass2);
        return;
    }
    if(!regmail.test(mail.val()) || mail.val() == ''){
        mail.addClass('error');
        $('#infos').html(error_mail);
        return;
    }
    $("#"+form).submit();
}

function checkInputBDD(input){
    if(($(input).val() == '' && $(input).attr('name') != 'db_pass') || ($(input).attr('name') == 'db_pass' && $('input[name="db_user"]').val() != 'root' && $(input).val() == '')){
        $(input).addClass('error');
    }
    else{
        $(input).removeClass('error');
    }
}

function checkInputAdmin(input){
    if(($(input).val() == '' || $(input).attr('name') == 'pass2' && ($('input[name="pass"]').val() != $('input[name="pass2"]').val()))){
        $(input).addClass('error');
    }
    else{
        $(input).removeClass('error');
    }
}