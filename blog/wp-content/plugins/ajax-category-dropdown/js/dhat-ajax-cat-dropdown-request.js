function setCat(category_field,results_div,cat_level)
{
var iReturnValue = 0;
document.getElementById("loadpic").style.height = document.getElementById('wrapform').offsetHeight + 'px';
document.getElementById("loadin").style.display = 'block';

var mysack = new sack( 
       "http://mysite.com/wp-content/plugins/dya_cat_dropdown/dya_cat_dropdown_request.php");    
   	  mysack.execute = 1;
	  mysack.method = 'GET';
	  mysack.setVar( "category_id", category_field );
	  mysack.setVar( "results_div_id", results_div );
	  mysack.setVar( "category_level", cat_level);
	  mysack.onError = function() { alert('AJAX error occured.' )};
	  mysack.runAJAX();
	
	return true;	
}
function setAdminCat(category_field,results_div,cat_level,cat_level2)
{
var mysack = new sack( 
       "http://mysite.com/wp-content/plugins/dya_cat_dropdown/dya_cat_dropdown_request.php");    
    
	  mysack.execute = 1;
	  mysack.method = 'GET';
	  mysack.setVar( "admin", category_field );
	  mysack.setVar( "category_id", category_field );
	  mysack.setVar( "results_div_id", results_div );
	  mysack.setVar( "category_level", cat_level);
	  mysack.setVar( "category_level2", cat_level2);
	  mysack.onError = function() { alert('AJAX error in voting' )};
	  mysack.runAJAX();
	return true;
	
}
function setFiltercatf(category_field,results_div,cat_level,cat_level2)
{
var mysack = new sack( 
       "http://mysite.com/wp-content/plugins/dya_cat_dropdown/dya_cat_dropdown_request.php");    
    
	  mysack.execute = 1;
	  mysack.method = 'GET';
	  mysack.setVar( "filter", category_field );
	  mysack.setVar( "category_id", category_field );
	  mysack.setVar( "results_div_id", results_div );
	  mysack.setVar( "category_level", cat_level);
	  mysack.setVar( "category_level2", cat_level2);
	  mysack.onError = function() { alert('AJAX error in voting' )};
	  mysack.runAJAX();
	return true;
	
}
function saveposttocategory(post_id, main_cat, myselectbox)
{

if (myselectbox) { 
alert ("CHECKED");
var mysack = new sack( 
       "http://mysite.com/wp-content/plugins/dya_cat_dropdown/dya_cat_dropdown_request.php");    
    
	  mysack.execute = 1;
	  mysack.method = 'GET';
	  mysack.setVar( "savepostcat", post_id );
	  mysack.setVar( "post_id", post_id );
	  mysack.setVar( "category_id", main_cat );
	  mysack.onError = function() { alert('AJAX error in voting' )};
	  mysack.runAJAX();
	  alert (post_id+' saved to: '+main_cat);
	return true;
 }
else { 
alert ("NOT CHECKED"); 
var mysack = new sack( 
       "http://mysite.com/wp-content/plugins/dya_cat_dropdown/dya_cat_dropdown_request.php");    
    
	  mysack.execute = 1;
	  mysack.method = 'GET';
	  mysack.setVar( "deletepostcat", post_id );
	  mysack.setVar( "post_id", post_id );
	  mysack.setVar( "category_id", main_cat );
	  mysack.onError = function() { alert('AJAX error in voting' )};
	  mysack.runAJAX();
	  alert (post_id+' deleted cat: '+main_cat);
	return true;
}


	
}
function addOption(selectbox,text,value)
{
var optn = document.createElement("OPTION");

optn.text = text;
optn.value = value;
selectbox.options.add(optn);
return true;
}
function deleteOption(selectbox)
{
var i;

for(i=selectbox.options.length-1;i>=0;i--)
{
selectbox.remove(i);
}
return true;
}
function get_form_cat(main_cat, result_div)
{
var mysack = new sack( 
       "http://mysite.com/wp-content/plugins/dya_cat_dropdown/dya_cat_dropdown_request.php");    
    
	  mysack.execute = 1;
	  mysack.method = 'GET';
	  mysack.setVar( "get_form_countries", 'true' );
	  mysack.setVar( "results_div_id", result_div );
	  mysack.setVar( "cat_id", main_cat );
	  mysack.onError = function() { alert('AJAX error in voting' )};
	  mysack.runAJAX();
	return true;
}
function showmebydivid(idtoshow)
{
//new Effect.toggle(idtoshow,'blind');
//var myFx = new Fx.Slide(idtoshow).toggle();
//var myFx = new Fx.Tween(idtoshow).set('display', 'block');
document.getElementById(idtoshow).style.display = 'block';

	
return false;
}
function shownavlink(clevel)
{
	pgoname = 'pgo' + clevel;
	
	setTimeout("showmebydivid(pgoname)",5000);

}
function querySt(ji) {
hu = window.location.search.substring(1);
gy = hu.split("&");
for (i=0;i<gy.length;i++) {
ft = gy[i].split("=");
if (ft[0] == ji) {
return ft[1];
}
}
}
