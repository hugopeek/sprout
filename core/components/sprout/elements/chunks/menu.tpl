<nav class="container-fluid">
    <ul>
        <li><a class="logo" href="[[~[[++site_start]]]]"><strong>[[++site_name]]</strong></a></li>
    </ul>
    [[pdoMenu?
        &startId=`0`
        &level=`1`
        &tplOuter=`@INLINE <ul>[[+wrapper]]</ul>`
        &tpl=`@INLINE <li><a href="[[+link]]" [[+attributes]]>[[+menutitle]]</a></li>`
    ]]
</nav>