<?php

nkTemplate_moduleInit('formTest');


function index() {
?>
<h3 style="text-align: center;">Test formulaire</h3>
<hr style="width:80%;margin:0 auto;" />
<ul>
    <li>
        <a href="index.php?file=formTest&amp;op=commonCheckFieldTest">
            Test formulaire avec vérifications communes aux champs
        </a>
    </li>
    <li>
        <a href="index.php?file=formTest&amp;op=valueCheckFieldTest">
            Test formulaire avec vérifications des valeurs possible pour les champs
        </a>
    </li>
    <li>
        <a href="index.php?file=formTest&amp;op=textFieldMenu">
            Test formulaire avec champs de type <b>Text</b>
        </a>
    </li>
    <li>
        <a href="index.php?file=formTest&amp;op=passwordCheckFieldTest">
            Test formulaire avec vérification de type <b>Password</b> (avec confirmation)
        </a>
    </li>
    <li>
        <a href="index.php?file=formTest&amp;op=fileCheckFieldTest">
            Test formulaire avec champs de type <b>File</b>
        </a>
    </li>
    <li>
        <a href="index.php?file=formTest&amp;op=selectFieldMenu">
            Test formulaire avec champs de type <b>Select</b>
        </a>
    </li>
    <li>
        <a href="index.php?file=formTest&amp;op=checkboxCheckFieldTest">
            Test formulaire avec champs de type <b>Checkbox</b>
        </a>
    </li>
    <li>
        <a href="index.php?file=formTest&amp;op=textareaFieldMenu">
            Test formulaire avec champs de type <b>Textarea</b>
        </a>
    </li>
</ul>
<?php
}

function commonCheckFieldTest() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/formTest/config/commonCheckField.php';
    define('EDITOR_CHECK', 1);

?>
<h3 style="text-align: center;">
    Test formulaire avec vérification communes aux champs
</h3>
<hr style="width:80%;margin:0 auto;" />
<?php

    echo nkForm_generate($form);
}

function valueCheckFieldTest() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/formTest/config/textCheckField.php';
    define('EDITOR_CHECK', 1);

?>
<h3 style="text-align: center;">
    Test formulaire avec vérifications des valeurs possible pour les champs
</h3>
<hr style="width:80%;margin:0 auto;" />
<?php

    echo nkForm_generate($form);
}

function textFieldMenu() {
?>
<h3 style="text-align: center;">Test formulaire avec champs de type <b>Text</b></h3>
<hr style="width:80%;margin:0 auto;" />
<ul>
    <li>
        <a href="index.php?file=formTest&amp;op=emailCheckFieldTest">
            Test formulaire avec vérification de type <b>Email</b>
        </a>
    </li>
    <li>
        <a href="index.php?file=formTest&amp;op=dateCheckFieldTest">
            Test formulaire avec vérification de type <b>Date</b>
        </a>
    </li>
    <li>
        <a href="index.php?file=formTest&amp;op=usernameCheckFieldTest">
            Test formulaire avec vérification de type <b>Username</b>
        </a>
    </li>
</ul>
<?php
}

function emailCheckFieldTest() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/formTest/config/emailCheckField.php';

?>
<h3 style="text-align: center;">
    Test formulaire avec vérification de type <b>email</b>
</h3>
<hr style="width:80%;margin:0 auto;" />
<?php

    echo nkForm_generate($form);
}

function dateCheckFieldTest() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/formTest/config/dateCheckField.php';

?>
<h3 style="text-align: center;">
    Test formulaire avec vérification de type <b>date</b>
</h3>
<hr style="width:80%;margin:0 auto;" />
<?php

    echo nkForm_generate($form);
}

function usernameCheckFieldTest() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/formTest/config/usernameCheckField.php';

?>
<h3 style="text-align: center;">
    Test formulaire avec vérification de type <b>username</b>
</h3>
<hr style="width:80%;margin:0 auto;" />
<?php

    echo nkForm_generate($form);
}

function passwordCheckFieldTest() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/formTest/config/passwordCheckField.php';

?>
<h3 style="text-align: center;">
    Test formulaire avec vérification de type <b>password</b> (avec confirmation)
</h3>
<hr style="width:80%;margin:0 auto;" />
<?php

    echo nkForm_generate($form);
}

function fileCheckFieldTest() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/formTest/config/fileCheckField.php';

?>
<h3 style="text-align: center;">
    Test formulaire avec champs de type <b>file</b>
</h3>
<hr style="width:80%;margin:0 auto;" />
<?php

    echo nkForm_generate($form);
}

function selectFieldMenu() {
?>
<h3 style="text-align: center;">Test formulaire</h3>
<hr style="width:80%;margin:0 auto;" />
<ul>
    <li>
        <a href="index.php?file=formTest&amp;op=selectCheckFieldTest">
            Test formulaire avec champs de type <b>Select</b>
        </a>
    </li>
    <li>
        <a href="index.php?file=formTest&amp;op=selectMultipleCheckFieldTest">
            Test formulaire avec champs de type <b>Select</b> avec option <b>multiple</b>
        </a>
    </li>
</ul>
<?php
}

function selectCheckFieldTest() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/formTest/config/selectCheckField.php';

?>
<h3 style="text-align: center;">
    Test formulaire avec champs de type <b>select</b>
</h3>
<hr style="width:80%;margin:0 auto;" />
<?php

    echo nkForm_generate($form);
}

function selectMultipleCheckFieldTest() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/formTest/config/selectMultipleCheckField.php';

?>
<h3 style="text-align: center;">
    Test formulaire avec champs de type <b>select</b> avec option <b>multiple</b>
</h3>
<hr style="width:80%;margin:0 auto;" />
<?php

    echo nkForm_generate($form);
}

function checkboxCheckFieldTest() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/formTest/config/checkboxCheckField.php';

?>
<h3 style="text-align: center;">
    Test formulaire avec champs de type <b>checkbox</b>
</h3>
<hr style="width:80%;margin:0 auto;" />
<?php

    echo nkForm_generate($form);
}

function textareaFieldMenu() {
?>
<h3 style="text-align: center;">Test formulaire</h3>
<hr style="width:80%;margin:0 auto;" />
<ul>
    <li>
        <a href="index.php?file=formTest&amp;op=textareaCheckFieldTest">
            Test formulaire avec champs de type <b>Textarea</b>
        </a>
    </li>
</ul>
<?php
}

function textareaCheckFieldTest() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/formTest/config/textareaCheckField.php';

?>
<h3 style="text-align: center;">
    Test formulaire avec champs de type <b>textarea</b>
</h3>
<hr style="width:80%;margin:0 auto;" />
<?php

    echo nkForm_generate($form);
}

function doCheckField() {
    require_once 'Includes/nkCheckForm.php';
    require_once 'modules/formTest/config/'. $_GET['form'] .'.php';

    $form['token']['refererData'] = array('index.php?file=formTest&op='. $_GET['form'] .'Test');

    $_POST = array_map_recursive('stripslashes', $_POST);

    $data = array();

    if (! nkCheckForm($form, array_keys($form['items']), $data)) {
?>
<br />
<h3 style="text-align: center;">Formulaire non valide</h3>
<hr />
<?php
        displayDoCheckResult($data);
?>
<hr />
<p style="text-align: center;"><a href="index.php?file=formTest&amp;op=<?php echo $_GET['form'] ?>FieldTest">Retour</a></p>
<?php
        return;
    }

?>
<br />
<h3 style="text-align: center;">Formulaire valide</h3>
<hr />
<?php
    displayDoCheckResult($data);

?>
<hr />
<p style="text-align: center;"><a href="index.php?file=formTest">Retour au menu</a></p>
<?php
}

function displayDoCheckResult($data) {
?>
<br />
Contenu de la superglobale $_POST :
<pre><?php var_dump($_POST) ?></pre>
<hr /><br />
Contenu de la variable des données du formulaire valides :
<pre><?php var_dump($data) ?></pre>
<?php
}


?>
<div id="nkFormTest">
<?php

// Action handle
switch ($GLOBALS['op']) {
    case 'commonCheckFieldTest' :
        commonCheckFieldTest();
        break;

    case 'valueCheckFieldTest' :
        valueCheckFieldTest();
        break;

    case 'textFieldMenu' :
        textFieldMenu();
        break;

    case 'emailCheckFieldTest' :
        emailCheckFieldTest();
        break;

    case 'dateCheckFieldTest' :
        dateCheckFieldTest();
        break;

    case 'usernameCheckFieldTest' :
        usernameCheckFieldTest();
        break;

    case 'passwordCheckFieldTest' :
        passwordCheckFieldTest();
        break;

    case 'fileCheckFieldTest' :
        fileCheckFieldTest();
        break;

    case 'selectFieldMenu' :
        selectFieldMenu();
        break;

    case 'selectCheckFieldTest' :
        selectCheckFieldTest();
        break;

    case 'selectMultipleCheckFieldTest' :
        selectMultipleCheckFieldTest();
        break;

    case 'checkboxCheckFieldTest' :
        checkboxCheckFieldTest();
        break;

    case 'textareaFieldMenu' :
        textareaFieldMenu();
        break;

    case 'textareaCheckFieldTest' :
        textareaCheckFieldTest();
        break;

    case 'doCheckField' :
        doCheckField();
        break;

    default :
        index();
        break;
}

?>
</div>
<?php

?>