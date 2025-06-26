<div>
<div class="container">
	<div class="header">
		@include('tools.excel.ribbon')
	</div>
	<div class="content">
		@include('tools.excel.spreadWrapper')
	</div>
</div>
<div class="footer">
	<div class="statusBar">
		<button class="statusbar-edit-status" data-bind="text: res.statusBar.ready">READY</button>
		<button class="statusbar-zoom-value">100%</button>
	</div>
</div>
</div>