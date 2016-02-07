<?php
    if ($autocomplete) :
?>
<form id="nkListAutocomplete" method="post" action="<?php echo $autocomplete['formUrl'] ?>">
    <p>
        <label for="search"><?php echo _SEARCH ?> : </label>
        <input type="text" name="search" value="<?php echo htmlspecialchars($autocomplete['value']) ?>" />
        <input type="submit" name="submit" value="<?php echo _OK ?>" />
    </p>
</form>
<?php
        if ($autocomplete['value'] != '' && $nbData > 0) :
?>
<p class="text-center">
    <b><?php echo _RESULTSEARCHFOR ?> : <i><?php echo htmlspecialchars($autocomplete['value']) ?></i></b>
</p>
<br />
<?php
        endif;
    endif;

    echo $pagination;

    if ($nbData > 0 && isset($checkbox)) :
?>
<form id="nkListCheckbox" method="post" action="<?php echo $checkbox['formAction'] ?>">
<?php
    endif
?>
<table id="<?php echo $css['tablePrefix'] ?>List" class="nkList" border="0" cellspacing="1" cellpadding="2">
    <tr>
<?php
    foreach ($fields as $field => $fieldData) :
?>
        <th class="<?php echo $css['fieldsPrefix'] . ucfirst($field) ?>">
<?php
        if ($fieldData['sort']) :
?>
            <a href="<?php echo $fieldData['sortUrl'] ?>" title="<?php echo $fieldData['sortTitle'] ?>">
<?php
        endif;

        if ($fieldData['type'] == 'checkbox') :
?>
            <a href="#" id="checkboxListSelector" title="<?php echo _CHECKALL ?>"><?php echo $fieldData['label'] ?></a>
<?php
        else :
?>
            <b><?php echo $fieldData['label'] ?></b>

<?php
        endif;

        if ($fieldData['sort']) :
?>
            </a>
<?php
        endif
?>
        </th>
<?php
    endforeach;

    if (isset($edit)) :
?>
        <th class="nkListEdit"><b><?php echo _EDIT ?></b></th>
<?php
    endif;

    if (isset($delete)) :
?>
        <th class="nkListDelete"><b><?php echo (isset($delete['label'])) ? $delete['label'] : _DELETE ?></b></th>
<?php
    endif
?>
    </tr>
<?php
    $r = 0;

    if ($nbData > 0) :
        foreach ($dataList as $row) :
?>
    <tr>
<?php
            foreach ($fields as $field => $fieldData) :
?>
        <td class="<?php echo $css['fieldsPrefix'] . ucfirst($field) ?>">
<?php
                if ($fieldData['type'] == 'checkbox') :
?>
            <input class="checkbox" type="checkbox" name="<?php echo $fieldData['checkboxName'] ?>[<?php echo $r ?>]" value="<?php echo $fieldData['checkboxValue'] ?>" />
<?php
                elseif ($fieldData['type'] == 'positionLink') :
                    if (isset($fieldData['urlDown'])) :
?>
            <a href="<?php echo $fieldData['urlDown'] ?>" title="<?php echo $fieldData['labelDown'] ?>">&lt;</a>
<?php
                    endif
?>
                &nbsp;<?php echo $row[$field] ?>&nbsp;
<?php
                    if (isset($fieldData['urlUp'])) :
?>
            <a href="<?php echo $fieldData['urlUp'] ?>" title="<?php echo $fieldData['labelUp'] ?>">&gt;</a>
<?php
                    endif;
                else :
                    if (isset($fieldData[$field .'Link']) && $fieldData[$field .'Link'] != '') : ?>
            <a href="<?php echo $fieldData[$field .'Link'] ?>">
<?php
                    endif;

                    if ($fieldData['type'] == 'string') :
?>
            <?php echo $row[$field] ?>
<?php
                    elseif ($fieldData['type'] == 'image') :
?>
            <img style="border: 0;" src="<?php echo $fieldData['src'] ?>" alt="" title="<?php echo $fieldData['title'] ?>" />
<?php
                    endif;

                    if (isset($fieldData[$field .'Link']) && $fieldData[$field .'Link'] != '') : ?>
            </a>
<?php
                    endif;
                endif;
?>
        </td>
<?php
                if (isset($row['noDeleteRow'])) $noDeleteRow = true;
            endforeach;

            if (isset($edit)) :
?>
        <td class="nkListEdit">
            <a href="<?php echo $baseUrl ?>&amp;op=<?php echo $edit['op'] ?>&amp;<?php echo $rowId ?>=<?php echo $row[$rowId] ?>">
                <img style="border: 0;" src="images/edit.gif" alt="<?php echo _EDIT ?>" title="<?php echo $edit['imgTitle'] ?>" />
            </a>
        </td>
<?php
            endif;

            if (isset($delete)) :
                if (isset($noDeleteRow) || (array_key_exists('notDeletableId', $delete) && $delete['notDeletableId'] != '' && $delete['notDeletableId'] == $row[$rowId])) :
?>
        <td class="nkListDelete">-</td>
<?php
                    if (isset($noDeleteRow)) : unset($noDeleteRow); endif;
                else :
?>
        <td class="nkListDelete">
            <a href="javascript:confirmToDeleteInList('<?php echo addslashes(strip_tags($row[$delete['confirmField']])) ?>', '<?php echo htmlspecialchars($row[$rowId]) ?>');">
                <img style="border: 0;" src="images/del.gif" alt="<?php echo _DELETE ?>" title="<?php echo $delete['imgTitle'] ?>" />
            </a>
        </td>
<?php
                endif;
            endif
?>
    </tr>
<?php
            $r++;
        endforeach;
    else :
?>
    <tr>
<?php
        $coledit = (isset($edit)) ? 1 : 0;
        $coldel = (isset($delete)) ? 1 : 0;
?>
        <td style="text-align:center;" colspan="<?php echo count($fields) + $coledit + $coldel ?>">
<?php
        if ($autocomplete && $autocomplete['value'] != '') :
?>
            <?php echo _NORESULTFOR ?> <b><i><?php echo addslashes($autocomplete['value']) ?></i></b>
<?php
        else :
?>
            <?php echo $noDataText ?>
<?php
        endif
?>
        </td>
    </tr>
<?php
    endif
?>
</table>
<?php
    if (isset($delete)) :
?>
    <p style="display:none;"><input type="hidden" name="token" value="<?php echo $delete['token'] ?>" /></p>
<?php
    endif;

    if ($nbData > 0 && isset($checkbox)) :
?>
    <p style="text-align:center;"><input type="submit" value="<?php echo $checkbox['submitTxt'] ?>" /></p>
</form>
<?php
    endif;

    echo $pagination;
?>
