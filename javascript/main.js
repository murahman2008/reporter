var WEB_ROOT = 'http://localhost/reporter';

$(function() {
	
	if($('#login_form').length)
	{
		$('#login_form').submit(function() {
			userLogin(this);
			return false;
		});
	}
	
	if($('.action_button').length) 
	{
		$('.action_button').each(function(item) {
			$(this).on('click', function(e) {
				e.preventDefault();
				updateReportCounter($(this));
				return false;
			});
		});
	}
	
});

function updateReportCounter(button) {
	var tmp = {};
	tmp.operator = $.trim($(button).attr('operator'));
	tmp.reportRow = $(button).closest('.report_main_row');
	
	tmp.counterValue = $.trim(tmp.reportRow.find('.counter_box').val());
	tmp.userId = $.trim(tmp.reportRow.attr('user_id'));
	tmp.workingDate = $.trim(tmp.reportRow.attr('working_date'));
	tmp.reportTypeId = $.trim(tmp.reportRow.attr('report_type_id'));
	
	if(isNaN(tmp.counterValue))
		tmp.counterValue = 0;
	
	if(tmp.counterValue <= 0)
	{
		alert('The Counter is invalid. It can only accept positivie number');
		return false;
	}	
	
	$.ajax({
		url: WEB_ROOT + '/ajax/update_report_counter',
		dataType: 'json',
		method: 'post',
		data: {
			'userId': tmp.userId,
			'workingDate': tmp.workingDate,
			'reportTypeId': tmp.reportTypeId,
			'counter': tmp.counterValue,
			'operator': tmp.operator
		},
		success: function(data) {
			console.debug(data);
			
			if(!data.status)
				alert(data.message);
			else
			{	
				tmp.finalCounterBox = tmp.reportRow.find('#current_counter_' + data.data.report_type_id)
				tmp.finalCounterBox.val(data.data.report_counter_counter);
				tmp.finalCounterBox.css('background-color:lime');
			}	
			
			return false;
		}
	});	
	
	return false
}


function userLogin(form) {
	var tmp = {};
	
	tmp.username = $.trim($('#username').val());
	tmp.password = $.trim($('#password').val());
	
	if(tmp.username === '' || tmp.password === '')
	{
		alert('Both Username & Password are required');
		return false;
	}	
	
	$.ajax({
		url: WEB_ROOT + '/ajax/login',
		dataType: 'json',
		method: 'post',
		data: {
			'username': tmp.username,
			'password': tmp.password
		},
		success: function(data) {
			//return false;
			if(!data.status)
				alert(data.message);
			else
				window.location.href = WEB_ROOT;
			
			return false;
		}
	});
	
	return false;
}