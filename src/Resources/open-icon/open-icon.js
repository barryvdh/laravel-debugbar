(function ($) {
    $(function () {
        var header = $('div.phpdebugbar-header');
        $('div.phpdebugbar-header').click(function () {
            var maximizeBtn = $('a.phpdebugbar-maximize-btn:visible');
            var minimizeBtn = $('a.phpdebugbar-minimize-btn:visible');

            if (maximizeBtn.length > 0) {
                maximizeBtn.click();
            } else {
                minimizeBtn.click();
            }
        });

        $('div.phpdebugbar-header > .phpdebugbar-header-left').click(function (e) {
            e.stopPropagation();
        });

        $('div.phpdebugbar-header > .phpdebugbar-header-right').click(function (e) {
            e.stopPropagation();
        });
    })
})(PhpDebugBar.$);
