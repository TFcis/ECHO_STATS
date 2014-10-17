<iframe src="./frag/admin.php" frameborder="1" border="0" cellspacing="0" style="border:none" width="100%" height="100%">
</iframe>

<script>
window.onload = function() {
	$(window).resize(windowSizeChange);
	windowSizeChange();
}
function windowSizeChange(){
	$("#content").height( $(window).height() - $("#content").position().top - 10 );
}
</script>