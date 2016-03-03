
<script type="text/javascript">
<!--
function maj_shoutbox() {
    if (document.getElementById("textbox").style.paddingTop != "0px") {
        document.getElementById("textbox").style.textAlign = "center";
        document.getElementById("textbox").innerHTML = '<img src="images/loading.gif" alt="Loading" /><br /><?php echo _LOADINPLSWAIT ?>';
        document.getElementById("textbox").style.paddingTop = "150px";
    }

    var fichier = 'index.php?file=Textbox&op=ajax';
    var requete;

    if (window.XMLHttpRequest) requete = new XMLHttpRequest();
    else if (window.ActiveXObject) requete = new ActiveXObject("Microsoft.XMLHTTP");
    else alert('<?php echo _LOADINGERRORS ?>');
    requete.open('get',fichier,true);

    requete.onreadystatechange = function() {
        if (requete.readyState == 4 && requete.status==200 && requete.responseText != "") {
            document.getElementById("textbox").style.textAlign = "left";
            document.getElementById("textbox").innerHTML = requete.responseText;
            document.getElementById("textbox").style.paddingTop = "0px";
<!--scrollbar inversé-->
            element = document.getElementById("textbox");
            element.scrollTop = element.scrollHeight;
<!--fin-->
            setTimeout('suivant()','25000');
        }
    }

    requete.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=iso-8859-1');
    requete.send(null);
}

function suivant() {
    document.getElementById("affichetextbox").innerHTML = "";
    maj_shoutbox();
}

function trim(string) {
    return string.replace(/(^\s*)|(\s*$)/g,'');
}

function maFonctionAjax(auteur,texte, ctToken, ctScript, ctEmail) {
    <?php
        if ($captcha) {
            echo 'var captchaData = "&ct_token="+ctToken+"&ct_script="+ctScript+"&ct_email="+ctEmail;';
        }
        else {
            echo 'var captchaData = "";';
        }
    ?>

    if (trim(document.getElementById('textbox_auteur').value) == "") {
        alert('<?php echo _NONICKNAME ?>');
        return false;
    }

    if (document.getElementById('textbox_auteur').value == '<?php echo '_NICKNAME' ?>') {
        alert('<?php echo _NONICKNAME ?>');
        return false;
    }

    if (trim(document.getElementById('textbox_texte').value) == "") {
        alert('<?php echo _NOTEXT ?>');
        return false;
    }

    if (document.getElementById('textbox_texte').value == '<?php echo _YOURMESS ?>') {
        alert('<?php echo _NOTEXT ?>');
        return false;
    }

    var OAjax;
    if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
    else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
    OAjax.open('POST',"index.php?file=Textbox&page=submit",true);
    document.getElementById("affichetextbox").innerHTML = "<div style=\"text-align:center;\"><b><?php echo _PLEASEWAITTXTBOX ?></b></div>";
    OAjax.onreadystatechange = function() {
        if (OAjax.readyState == 4 && OAjax.status==200) {
            if (document.getElementById) {
                var message = OAjax.responseText.substr(OAjax.responseText.search(/\<div id\=\"ajaxMessage\"\>/));
                message = message.substr(0, message.search(/<\/div>/) + 6);
                document.getElementById("affichetextbox").innerHTML = "<b>" + message + "</b>";
                document.getElementById("textbox_texte").value = "<?php echo _YOURMESS ?>";
                maj_shoutbox();
            }
        }
    }

    texte = encodeURIComponent(texte);
    OAjax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=iso-8859-1');
    OAjax.send('ajax=1&auteur='+auteur+'&texte='+texte+captchaData);
    return true;
}
<?php
if ($GLOBALS['visiteur'] >= nivo_mod('Textbox')) :
?>

function del_shout(pseudo, id) {
    if (confirm('<?php echo '_DELETETEXT' ?> '+pseudo+' ! <?php echo _CONFIRM ?>')) {
        document.location.href = 'index.php?file=Textbox&page=admin&op=del_shout&mid='+id;
    }
}
<?php
endif
?>
-->
</script>

<table style="margin-left: auto;margin-right: auto;text-align: left;" width="98%" cellspacing="1" cellpadding="2">
    <tr>
        <td>
            <div id="textbox" style="width: <?php echo $width ?>; height: <?php echo $height ?>; overflow: auto;">
                <p>
                    <img src="images/loading.gif" alt="Loading" /><br />
                    <?php echo _LOADINPLSWAIT ?>
                </p>
            </div>
        </td>
    </tr>
</table>
<script type="text/javascript">maj_shoutbox();</script>
<div id="affichetextbox"></div>
<div>
<?php
if ($captcha === true)
    $onsubmit = 'maFonctionAjax(this.textbox_auteur.value,this.textbox_texte.value, this.ct_token.value, this.ct_script.value, this.ct_email.value); return false;';
else
    $onsubmit = 'maFonctionAjax(this.textbox_auteur.value,this.textbox_texte.value); return false;';

if ($GLOBALS['visiteur'] >= nivo_mod('Textbox')) :
    if ($active == 3 || $active == 4) : ?>

    <form class="textboxForm" method="post" onsubmit="<?php echo $onsubmit ?>" action="">
        <div style="text-align: center;">
            <input id="textbox_auteur" type="hidden" name="auteur" value="<?php echo $GLOBALS['user']['name'] ?>" />
            <div class="nkButton-container" style="margin:10px;">
                <div class="nkButton-group">
                    <a class="nkButton icon add alone" href="#" onclick="javascript:window.open('index.php?file=Textbox&amp;op=smilies&amp;textarea=textbox_texte','smilies','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=200,height=350,top=100,left=470');return(false)" title="<?php echo _SMILEY ?>"></a>
                    <a class="nkButton icon log alone" href="index.php?file=Textbox" title="<?php echo _SEEARCHIVES ?>"></a>
                </div>
                <input id="textbox_texte" type="text" name="texte" style="width:70%;" value="<?php echo _YOURMESS ?>" onclick="if(this.value=='<?php echo _YOURMESS ?>'){this.value=''}" />
                <?php if ($captcha) echo create_captcha() ?>
                <input class="nkButton" type="submit" value="<?php echo __('SEND') ?>" />
            </div>
        </div>
    </form>
<?php
    else :
?>
    <form class="textboxForm" method="post" onsubmit="<?php echo $onsubmit ?>" action="">
        <div style="text-align: center;">
            <input id="textbox_auteur" type="hidden" name="auteur" value="<?php echo $GLOBALS['user']['name'] ?>" />
            <input id="textbox_texte" type="text" name="texte" value="<?php echo _YOURMESS ?>" style="width:90%;" onclick="if(this.value=='<?php echo _YOURMESS ?>'){this.value=''}" /><br />
            <?php if ($captcha) echo create_captcha() ?>
            <div class="nkButton-container" style="margin:5px;">
                <input class="nkButton" type="submit" value="<?php echo __('SEND') ?>"/>
                <div class="nkButton-group">
                    <a class="nkButton icon add alone" href="#" onclick="javascript:window.open('index.php?file=Textbox&amp;op=smilies&amp;textarea=textbox_texte','smilies','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=200,height=350,top=100,left=470');return(false)" title="<?php echo _SMILEY ?>"></a>
                    <a class="nkButton icon log alone" href="index.php?file=Textbox" title="<?php echo _SEEARCHIVES ?>"></a>
                </div>
            </div>
        </div>
    </form>
<?php
    endif;
endif;
?>
</div>
