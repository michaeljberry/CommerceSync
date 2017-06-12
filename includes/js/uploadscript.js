$(function(){
	var ul = $('#upload ul');
	
	$('.dropzone a').click(function(){
		$(this).parent().find('input').click();
	});
	var i = 1;
	$('#upload').fileupload({
		dropZone: $('body'),
		add: function (e, data){
			var partnum = $('#part_num').val();
			if(partnum != ''){
			data.formData = {name: partnum};
			var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48" data-fgColor="#0788a5" data-readOnly="1" data-gbColor="#3e4043" /><p></p><span></span></li>');
			tpl.find('p').text(data.formData.name).append(' <i>' + formatFileSize(data.files[0].size) + '</i>');
			/* tpl.find('p').text(data.files[0].name)
                         .append('<i>' + formatFileSize(data.files[0].size) + '</i>'); */
			data.context = tpl.appendTo(ul);
			tpl.find('input').knob();
			tpl.find('span').click(function(){
				if(tpl.hasClass('working')){
					//data.abort();
					jqXHR.abort();
				}
				tpl.fadeOut(function(){
					tpl.remove();
				});
			});
			var jqXHR = data.submit();
			//$('.uploadphoto').click(function(e){ e.preventDefault();data.submit(); });
			i++;
			}else{
				alert('You must insert a part number first.');
			}
		},
		progress: function(e, data){
			var progress = parseInt(data.loaded / data.total * 100, 10);
			data.context.find('input').val(progress).change();
			if(progress == 100){
				data.context.removeClass('working');
			}
		},
        fail: function(e, data){
			data.context.addClass('error');
		}
	});
    
	$(document).on('drop dragover', function(e){
		e.preventDefault();
		var dropZone = $('.dropzone'),
        timeout = window.dropZoneTimeout;
    if (!timeout) {
        dropZone.addClass('in');
    } else {
        clearTimeout(timeout);
    }
    var found = false,
        node = e.target;
    do {
        if (node === dropZone[0]) {
            found = true;
            break;
        }
        node = node.parentNode;
    } while (node != null);
    if (found) {
        dropZone.addClass('hover');
    } else {
        dropZone.removeClass('hover');
    }
    window.dropZoneTimeout = setTimeout(function () {
        window.dropZoneTimeout = null;
        dropZone.removeClass('in hover');
    }, 100);
	});
	function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }
});
