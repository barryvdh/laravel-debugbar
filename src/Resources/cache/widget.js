(function ($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying cache events
     *
     * Options:
     *  - data
     */
    var LaravelCacheWidget = PhpDebugBar.Widgets.LaravelCacheWidget = PhpDebugBar.Widgets.TimelineWidget.extend({

        tagName: 'ul',

        className: csscls('timeline cache'),

        onForgetClick: function (e, el) {
            e.stopPropagation();

            $.ajax({
                url: $(el).attr("data-url"),
                type: 'DELETE',
                success: function (result) {
                    $(el).fadeOut(200);
                }
            });
        },

        render: function () {
            LaravelCacheWidget.__super__.render.apply(this);

            this.bindAttr('data', function (data) {

                if (data.measures) {
                    var self = this;
                    var lines = this.$el.find('.' + csscls('measure'));

                    for (var i = 0; i < data.measures.length; i++) {
                        var measure = data.measures[i];
                        var m = lines[i];

                        if (measure.params && !$.isEmptyObject(measure.params)) {
                            if (measure.params.delete && measure.params.key) {
                                $('<a />')
                                    .addClass(csscls('forget'))
                                    .text('forget')
                                    .attr('data-url', measure.params.delete)
                                    .one('click', function (e) {
                                        self.onForgetClick(e, this); })
                                    .appendTo(m);
                            }
                        }
                    }
                }
            });
        }
    });

    // ============= Handling Dragging Behavior ============
    var storedPosX = localStorage.getItem('phpdebugbarPositionX') || 0;
    var screenWidth = $(window).width();

    $(document).on('click', '.phpdebugbar', function () {
        let isClosed = $('.phpdebugbar').hasClass('phpdebugbar-closed');
        if (!isClosed) {
            // Save current position to localStorage when closing the debugbar
            storedPosX = localStorage.getItem('phpdebugbarPositionX');
            $('.phpdebugbar').css('left', '0px');
        } else {
            // Restore saved position from localStorage when opening the debugbar
            localStorage.setItem('phpdebugbarPositionX', storedPosX);
            $('.phpdebugbar').css('left', storedPosX + 'px');
        }
    });

    $(document).ready(function () {
        // Check local storage for saved position
        var savedPosX = localStorage.getItem('phpdebugbarPositionX');
        var phpdebugbarIsOpen = localStorage.getItem('phpdebugbar-open');

        if (savedPosX !== null && phpdebugbarIsOpen == 0 && screenWidth > savedPosX) {
            $('.phpdebugbar').css('left', savedPosX + 'px');
        }

        // Make the phpdebugbar visible after setting the position
        $('.phpdebugbar').css('visibility', 'visible');

        function startDragging(e) {
            // Prevent text selection while dragging
            e.preventDefault();

            // Track initial mouse position and element position
            var initialMouseX = e.type === 'mousedown' ? e.clientX : e.touches[0].clientX;
            var initialPosX = $('.phpdebugbar').position().left;

            // Get the width of the element and the screen
            var elementWidth = $('.phpdebugbar').outerWidth();

            function doDrag(e) {
                // Calculate the change in mouse position
                var clientX = e.type === 'mousemove' ? e.clientX : e.touches[0].clientX;
                var deltaX = clientX - initialMouseX;

                // Update the position of the element
                var newPosX = initialPosX + deltaX;

                // Ensure the new position is within screen boundaries
                if (newPosX < 0) {
                    newPosX = 0;
                } else if (newPosX + elementWidth > screenWidth) {
                    newPosX = screenWidth - elementWidth;
                }

                let isClosed = $('.phpdebugbar').hasClass('phpdebugbar-closed');
                if (!isClosed) {
                    $('.phpdebugbar').css('left', '0px');
                    return;
                }

                $('.phpdebugbar').css('left', newPosX + 'px');
            }

            function stopDragging() {
                // Unbind the move and up/end events
                $(document).off('mousemove.drag touchmove.drag');
                $(document).off('mouseup.drag touchend.drag');

                // Save the new position to local storage
                var finalPosX = $('.phpdebugbar').position().left;
                localStorage.setItem('phpdebugbarPositionX', finalPosX);
            }

            // Bind the move and up/end events
            $(document).on('mousemove.drag touchmove.drag', doDrag);
            $(document).on('mouseup.drag touchend.drag', stopDragging);
        }

        // Bind the down/start events
        $(document).on('mousedown touchstart', '.phpdebugbar', startDragging);
    });
    // ============= End Handling Dragging Behavior ============

})(PhpDebugBar.$);
