// JavaScript Document
function setCat(category_field,cat_level,widget_number)
{
    if (!(category_field == "xselect")) {
        document.getElementById("loadpic").style.height = document.getElementById('wrapallcat').offsetHeight + 'px';
        document.getElementById("loadin").style.display = 'block';

        var mysack = new sack(DACDSettings.requesturl);    
            mysack.execute = 1;
            mysack.method = 'GET';
            mysack.setVar( "category_id", category_field );
            mysack.setVar( "category_level", cat_level);
            mysack.setVar( "widget_number", widget_number);
            mysack.onError = function() { alert('AJAX error occured.' )};
            mysack.runAJAX();    
    }        
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