(function ($) {
    $(function () {
        $('div.phpdebugbar-header').click(function () {
            $('a.phpdebugbar-close-btn').click();
        });

        $('div.phpdebugbar-header > .phpdebugbar-header-left').click(function (e) {
            e.stopPropagation();
        });

        $('div.phpdebugbar-header > .phpdebugbar-header-right').click(function (e) {
            e.stopPropagation();
        });
    })
})(PhpDebugBar.$);
