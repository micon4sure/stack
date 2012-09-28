<table>
    <tr>
        <td>uname</td>
        <td>
            <input type="text" id="uName" value="root"/>
        </td>
    </tr>
    <tr>
        <td>password</td>
        <td>
            <input type="text" id="uPass" value="<?= $this->rootpw ?>"/>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="button" value="login" id="login"/>
        </td>
    </tr>
</table>

<script type="text/javascript">
    jQuery('#login').click(function() {
        login($('#uName').val(), SHA1($('#uPass').val()));
    });
</script>
