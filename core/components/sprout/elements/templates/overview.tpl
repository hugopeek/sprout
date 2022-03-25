<!DOCTYPE html>
<html lang="[[++cultureKey]]">

<head>
    [[$sproutHead]]
</head>

<body id="[[*alias]]" class="overview">

[[$sproutMenu]]

<main class="container">
    [[*content:sproutProcessMarkdown]]

    [[pdoResources?
        &parents=`[[*id]]`
        &depth=`0`
        &limit=`0`
        &tpl=`sproutOverviewRow`
        &showHidden=`0`
        &tvPrefix=``
    ]]
</main>

[[$sproutFooter]]

</body>
</html>