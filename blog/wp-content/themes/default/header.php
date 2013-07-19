<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<link rel="stylesheet" type="text/css" media="screen" href="../styles.css"  />

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<style type="text/css" media="screen">

<?php
// Checks to see whether it needs a sidebar or not
if ( empty($withcomments) && !is_single() ) {
?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbg-<?php bloginfo('text_direction'); ?>.jpg") repeat-y top; border: none; }
<?php } else { // No sidebar ?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbgwide.jpg") repeat-y top; border: none; }
<?php } ?>

</style>

<meta name="keywords" content="Honolulu Culinary Insider, Hawaii’s Top Restaurant Blog" />
<meta name="description" content="Do you love to eat, drink and cook? Visit the Honolulu Culinary Insider and get access to Hawaii’s top Restaurant & Wine blog PLUS new recipes from chef Ryan!" />
<!-- TemplateBeginEditable name="head" -->
<!-- TemplateEndEditable -->
<script type="text/javascript" src="http://covertaffairs.com/Scripts/date.js"></script>
<script type="text/javascript" src="http://covertaffairs.com/Scripts/preload-images.js"></script>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>
<script language="JavaScript" type="text/javascript"><!--
document.write("<style type='text/css'>ul#hiddencats li ul {display:none;} ul#hiddencats li {cursor:pointer;} ul#hiddencats li ul li {cursor:auto;}</style>");
// --></script>
</head>
<body <?php body_class(); ?> >
<script type="text/javascript">
function show(ele) {
var srcElement = document.getElementById(ele);
if(srcElement != null) {
if(srcElement.style.display == "block") {
srcElement.style.display= 'none';
}
else {
srcElement.style.display='block';
}
}
//return false;
}
</script>
<div align="center">
  <br />
  <table width="780" bgcolor="#FFFFFF"  border="0" cellspacing="0" cellpadding="0"><tr>
    <td><table bgcolor="#FFFFFF" width="0" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="780" height="173" background="../template-gfx/header.jpg" style="background-repeat: no-repeat">&nbsp;</td>
    </tr>
    <tr>
      <td height="26"><table width="780" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><div id="MainMenu"><a href="../index.html">HOME</a><a href="../private-chef-hawaii-services.html">SERVICES</a><a href="../personal-chef-hawaii-costs.html">RATES</a><a href="../private-chef-hawaii-clients.html">CLIENTS</a><a href="../personal-chef-testimonials.html">TESTIMONIALS</a><a href="../personal-chef-hawaii-about-us.html">ABOUT</a><a href="../private-chef-oahu-reservations.html">RESERVATIONS</a><a href="../private-chef-oahu-gifts.html">GIFT CARDS</a><a href="http://www.covertaffairs.com/blog/">BLOG</a><a href="../contact.html">CONTACT</a></div></td>
        </tr>
        <tr>
          <td ><div id="SubMenu"><a href="../small-bites.html">SMALL BITES</a><a href="../appetizers.html">STARTERS</a><a href="../entrees.html">ENTREES</a><a href="../desserts.html">DESSERTS</a><a href="../drinks.html">DRINKS</a><a href="../published-recipes-hawaii.html">RECIPES</a><a href="http://www.covertaffairsart.com/" target="_blank">ART</a></div></div></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td bgcolor="#ffffff" valign="top"><br /><br /><!-- TemplateBeginEditable name="Title" --><img src="http://www.covertaffairs.com/pages/titles/blog.jpg" width="780" height="51" border="0" align="center" alt="Top 10 Oahu Hawaii blog about restaurants, wine and the newest and latest from personal chef Ryan Covert" />
      

       <!-- TemplateEndEditable --><br /> <br /> 
        
       
        
        <!-- TemplateBeginEditable name="Content" -->