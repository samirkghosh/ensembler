$('#create_disposition').on('click',function(e){
	var dispostion_type = $('#dispostion_type').val();
	var remarks= $('#email_remark').val();
	var channel_id= $('#channel_id').val();
	var send_from = $('#send_from').val();
	console.log(send_from);
	var channel_type = $('#channel_type').val();
	e.preventDefault();
	$.ajax({
		url: 'servicable_emailqueue_popup.php',
		method: 'POST',
		data: {
			dispostion_type: dispostion_type,action :'dispostion_channel_insert',
			channel_id:channel_id,remarks:remarks,channel_type:channel_type,send_from:send_from
		},
		success: function(data) {
			location.reload();
		}
	});	
});

$(document).ready(function() {
		$('#serviceable').on('change',function(e){
			var value = $(this).val();
			e.preventDefault();
			var id= $('#channel_id').val();
			if( value >= 1){
				if (confirm("Are you sure you want to classify this?")) {
					$.ajax({
						url: 'servicable_emailqueue_popup.php',
						method: 'POST',
						data: {
							id: id,value :value
						},
						success: function(data) {
							location.reload();
						},
						error:function(data){
							console.log('error');
						}
					});
				} else {
					return false;
				}
			}
		});
	});
	
	$('#submit_sentiment').on('click', function (e) {
		var sentiment = $('#sentiment').val();
		var channel_type = $('#channel_type').val();
		var channel_id = $('#channel_id').val();
	
		e.preventDefault();
		$.ajax({
			url: 'servicable_emailqueue_popup.php',
			method: 'POST',
			data: {
				sentiment: sentiment,
				channel_type: channel_type,
				channel_id: channel_id,
				action: 'sentiment'
			},
			success: function (data) {
				location.reload();
			}
		});
	});