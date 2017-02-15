(function($){
	var method = {};
	method.checkError = function(element)
	{
		var $pane = $(element).closest(".tab-pane");
		if (!$pane.length) return;
		var errors = $('.has-error', $pane);
		var $a = $('.nav a[href="#' + $pane.attr('id') + '"]');
		var $sup = $('sup.badge', $a);
		if (!$sup.length) $sup = $('<sup class="badge label-danger">0</sup>').appendTo($a);
		if (errors.length > 0)
			$sup.text(errors.length).show();
		else
			$sup.text(errors.length).hide();
	};
	//给validator设置默认值
	if ($.validator)
	$.validator.setDefaults({
		errorClass: "help-block validate animated slideInDown",
		errorElement: "span",
		highlight:function(element, errorClass, validClass) {
			var $parent = $(element).closest('div');
			if ($parent.hasClass('input-group')) $parent = $parent.closest('div');
			$parent.removeClass('has-success').addClass('has-error');
			method.checkError(element);
		},
		unhighlight: function(element, errorClass, validClass) {
			var $parent = $(element).closest('div');
			if ($parent.hasClass('input-group')) $parent = $parent.closest('div');
			$parent.removeClass('has-error').addClass('has-success');
			method.checkError(element);
		},
		errorPlacement: function(error, element) {
			var $parent = $(element).closest('div');
			if ($parent.hasClass('input-group')) $parent = $parent.closest('div');
			$('.help-block.validate', $parent).remove();
			error.appendTo($parent);
		},
		submitHandler:function(form){
			var $form = $(form);
			LP.query($form.attr('method'), $form.attr('action'), $form.serializeArray()).done(function(json) {
				$form.triggerHandler('query', [json, form]);
			});
			return false;
			//form.submit();
		},
		invalidHandler: function(e, validator){
			if(validator.errorList.length)
				$('.nav a[href="#' + $(validator.errorList[0].element).closest(".tab-pane").attr('id') + '"]').tab('show');
		}
	});

	$.fn.extend({trigger_error_bags : function(errors) {
		return this.each(function(){
			var $this = $(this);
			for(var i in errors) {
				var $parent = $('[name="'+ i +'"]', $this).closest('div');
				$('.help-block.validate', $parent).remove();
				for(var n = 0;n < errors[i].length;n++)
					$parent.addClass('has-error').append($('<span id="'+i+'-error" class="help-block validate">'+ errors[i][n] +'</span>'));
			}
		});	
	}
	});
})(jQuery);