<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Powerise Gateway</title>
</head>
<body>
<form id="form" method="post" action="{$action}">
    {foreach from=$data key=key item=item}
    <input type="hidden" name="{$key}" value="{$item}">
    {/foreach}
</form>
<script>
    document.getElementById('form').submit();
</script>

</body>
</html>