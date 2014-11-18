function getCookie(cname){
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
    }
    return "";
}
function editcolor(c){
	var rgb = $(c).css('color');
	if(rgb=="rgb(0, 204, 0)")return 0;
	if(rgb=="rgb(204, 0, 0)")return 0;
	if(rgb=="rgb(255, 255, 0)")return 0;
	var ds = rgb.split(/\D+/);
	c.style.color="rgb("+parseInt(ds[1]*color_R/255)+", "+parseInt(ds[2]*color_G/255)+", "+parseInt(ds[3]*color_B/255)+")";
	//c.style.background="#000";
} 
function dfs(v){
	//editcolor(v);
	if(cookie_color=="")return 0;
	for(var i=0;true;i++){
		if(v.children[i]==undefined){
			break;
		}else if(v.children[i].children[0]!=undefined){
			dfs(v.children[i]);
		}
		editcolor(v.children[i]);
	}
}
var cookie_color=getCookie("color");
var color_R=255;
var color_G=255;
var color_B=255;
if(cookie_color!=""){
	color_R=parseInt(cookie_color[1]+cookie_color[2],16);
	color_G=parseInt(cookie_color[3]+cookie_color[4],16);
	color_B=parseInt(cookie_color[5]+cookie_color[6],16);
}
//editcolor(document.body,color_R,color_G,color_B);
//dfs(document.body);