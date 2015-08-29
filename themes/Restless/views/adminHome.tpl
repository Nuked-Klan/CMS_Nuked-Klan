<form action="index.php?file=Admin&page=theme&op=saveHome" method="post">
    <table>
        <tr>
            <td style="width:15%;">
                <label>Charger un profil : </label>
            </td>
            <td>
                <select name="profileName">
                    @foreach(profilesList as profile)
                        <option value="{{profile.value}}" {{profile.selected}}>{{profile.text}}</option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input class="button" type="submit" value="{{*SUBMIT}}" />
            </td>
        </tr>
    </table>
</form>