<?php require_once( '../admin/cms.php' ); ?>
<cms:template title='Login' hidden='1'/>
<cms:pages masterpage='includes/setting.php'>

<!DOCTYPE HTML>
<html lang="en">
<head>
<!-- Meta tag Keywords -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<cms:show meta_desc/>">
<meta name="author" content="<cms:show meta_author/>">
<meta name="theme-color" content="<cms:show meta_theme_color/>" />
<link rel="shortcut icon" href="<cms:show site_favicon/>">
<title><cms:show meta_title/></title>
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- Meta tag Keywords -->
<!-- css files -->
<link rel="stylesheet" href="<cms:show k_site_link/>users/css/style.css?v=2.1" type="text/css" media="all" /> <!-- Style-CSS --> 
<link rel="stylesheet" href="<cms:show k_site_link/>users/css/font-awesome.css"> <!-- Font-Awesome-Icons-CSS -->
<link rel="stylesheet" type="text/css" href="<cms:show k_site_link/>users/fonts/icons.css">
<!-- //css files -->
<!-- online-fonts -->
<link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
<link href="//fonts.googleapis.com/css?family=Dosis:200,300,400,500,600,700,800&amp;subset=latin-ext" rel="stylesheet">
<!-- //online-fonts -->
</head>
<body>

    <!-- now the real work -->
    <cms:if k_logged_in >

        <!-- this 'login' template also handles 'logout' requests. Check if this is so -->
        <cms:set action="<cms:gpc 'act' method='get'/>" />
    
        <cms:if action='logout' >
            <cms:process_logout />
        <cms:else />  
            <!-- what is an already logged-in member doing on the login page? Send back to homepage. -->
            <cms:redirect k_site_link />
        </cms:if>
	 <cms:else />

<!-- main -->
<div class="center-container">
	<!--header-->
	<div class="header-w3l">
		<a href="<cms:show k_site_link/>"><img src="<cms:show site_logox1/>" srcset="<cms:show site_logox1/> 1x, <cms:show site_logox2/> 2x" alt="<cms:show logo_alt/>" style="width: 143px;"/></a>
	</div>
	<!--//header-->
	<div class="main-content-agile">
		<div class="sub-main-w3">	
			<div class="wthree-pro">
				<h2>Admin Login</h2>
			</div>
			<cms:form action="#" method="post" anchor="0">
			<cms:if k_success >
                <!-- 
                    The 'process_login' tag below expects fields named 
                    'k_user_name', 'k_user_pwd' and (optionally) 'k_user_remember', 'k_cookie_test'
                    in the form
                -->
            <cms:process_login />  
            </cms:if>
            <cms:if k_error >
                <h3 style='padding:10px'><font color='#d50000'><cms:show k_error /></font></h3>
            </cms:if>
			
				<div class="pom-agile">
					<cms:input type="text" name="k_user_name" placeholder="Username" required=""/>
					<span class="icon1"><i class="fa fa-user" aria-hidden="true"></i></span>
				</div>
				<div class="pom-agile">
					<cms:input type="password" name="k_user_pwd" placeholder="Password" required=""/>
					<span class="icon2"><i class="fa fa-unlock" aria-hidden="true"></i></span>
				</div>
				<div class="sub-w3l">
						<h6><a href="<cms:link 'users/lost-password.php'/>">Forgot Password?</a></h6>
					<div class="right-w3l">
						<input type="hidden" name="k_cookie_test" value="1" />
						<cms:input name="submit" type="submit" value="Login"/>
					</div>
				</div>
			</cms:form>	
			
		</div>
	</div>
	<!--//main-->
	<!--footer-->
	<div class="footer">
		<p><cms:show copyrights/></p>
	</div>
	<!--//footer-->
</div>

	</cms:if>

</body>
</html>
</cms:pages>
<?php KConn::invoke(); ?>