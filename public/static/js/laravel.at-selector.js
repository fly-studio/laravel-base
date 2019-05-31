(function($){
$.fn.extend({
	'at_selector': function(atDOM){
		var t = this;

		var $tplDOM = $('#at-selector-tpl');
		if (!$tplDOM.length) return t;

		t.append($tplDOM.html());

		$('a.btn-at-selector', t).on('click', function() {
			var $this = $(this);
			var $dom = [];
			if (typeof atDOM == 'string')
				$dom = [$('#'+atDOM+'-min'), $('#'+atDOM+'-max')];
			else if (atDOM instanceof Array)
				$dom = [$(atDOM[0]), $(atDOM[1])];
			else
				return;

			$dom[0].val($this.data('start'));
			$dom[1].val($this.data('end'));
			$this.closest('form').submit();
		});

		return t;
	}
});

$().ready(function(){
	$('[data-at-selector]').each(function(){
		var $this = $(this);
		$this.at_selector($this.data('at-selector'));
	});
});
})(jQuery);
