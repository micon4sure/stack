<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8 />
    <title>stack - please log in</title>
    <script type="text/javascript" src="/mootools/mootools.js"></script>
    <script type="text/javascript" src="/jquery/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/jquery/jquery-ui-1.8.20.custom.min.js"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="/jquery/ui/jquery-ui-1.8.20.custom.css" />

    <script type="text/javascript" src="/js/sha1.js"></script>
    <script type="text/javascript" src="/js/login.js"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/global.css" />
</head>
<body>
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

<div id="debug">

</div>
</body>
</html>