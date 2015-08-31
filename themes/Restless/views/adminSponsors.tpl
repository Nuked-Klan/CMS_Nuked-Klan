<form id="RL_formSponsors" action="index.php?file=Admin&amp;page=theme&amp;op=saveSponsors" method="post">
    <table>
        <tr id="RL_sponsor1">
            <td>Sponsor 1</td>
            <td>
                <input type="hidden" name="sponsorItem1" value="ok" />
                <div><label>{{*TITLE}} : </label><input type="text" class="RL_input" name="sponsorTitle1"/></div>
                <div><label>{{*LINK}} : </label><input type="text" class="RL_input" name="sponsorLink1"/></div>
                <div><label>{{*URL_IMAGE}} : </label><input type="text" class="RL_input" name="sponsorImage1"/></div>
            </td>
        </tr>
        <tr id="RL_sponsor">
            <td>Sponsor</td>
            <td>
                <input type="hidden" name="sponsorItem" value="ok" />
                <div><label>{{*TITLE}} : </label><input type="text" class="RL_input" name="sponsorTitle"/></div>
                <div><label>{{*LINK}} : </label><input type="text" class="RL_input" name="sponsorLink"/></div>
                <div><label>{{*URL_IMAGE}} : </label><input type="text" class="RL_input" name="sponsorImage"/></div>
            </td>
        </tr>
        <tr id="RL_submitSponsor">
            <td colspan="4" style="text-align:center;">
                <input class="button" type="submit" value="{{*SUBMIT}}"/>
                <input id="RL_addSponsor" class="button" type="button" value="{{*ADD_SPONSOR}}"/>
            </td>
        </tr>
    </table>
</form>