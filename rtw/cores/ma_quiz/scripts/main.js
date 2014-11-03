function snapToMiddle(dragger, target) {
    var topMove = target.position().top - dragger.data('position').top
	+ (target.outerHeight(true) - dragger.outerHeight(true)) / 2;
    var leftMove = target.position().left - dragger.data('position').left
	+ (target.outerWidth(true) - dragger.outerWidth(true)) / 2;
    dragger.animate({top: topMove, left: leftMove}, {duration:600, easing:'easeOutBack'});
}

$(document).ready(function() {    
    $('.items').sortable({
	connectWith: '.dock',
	placeholder: 'item',
	forcePlaceholderSize: true
    });
    
    $('.dock').sortable({
	connectWith: '.items, .dock',
	placeholder: 'dock',
	forcePlaceholderSize: true,
	receive: function(event, ui) {
	    if ($(this).children().length > 1) {
		$(ui.sender).sortable('cancel');
	    }
	}
    });
});

function htmlEncode(value) {
    if (value) {
	return $('<div />').text(value).html();
    }
    return value;
}


// Check if answered is checked?
function getResult() {    
    result = {};
    
    $('.docks ul li').each(function(e) {
	dock = $(this).parent().attr('data-dock');	
	result[dock] = htmlEncode($(this).html());
    });

    return result;
}
