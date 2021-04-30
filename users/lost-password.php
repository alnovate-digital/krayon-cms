<?php require_once( '../admin/cms.php' ); ?>
<cms:template title='Lost-password' hidden='1'/>


<!DOCTYPE HTML>
<html lang="en">
<head>
<cms:pages masterpage='includes/setting.php'>
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
<link rel="stylesheet" href="<cms:show k_site_link/>users/css/style.css" type="text/css" media="all" /> <!-- Style-CSS --> 
<link rel="stylesheet" href="<cms:show k_site_link/>users/css/font-awesome.css"> <!-- Font-Awesome-Icons-CSS -->
<link rel="stylesheet" type="text/css" href="<cms:show k_site_link/>users/fonts/icons.css">
<!-- //css files -->
<!-- online-fonts -->
<link href="//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
<link href="//fonts.googleapis.com/css?family=Dosis:200,300,400,500,600,700,800&amp;subset=latin-ext" rel="stylesheet">
<!-- //online-fonts -->
</cms:pages>
</head>
<body>
<!-- main -->
<div class="center-container">
	<cms:pages masterpage='includes/setting.php'>
	<!--header-->
	<div class="header-w3l">
		<a href="<cms:show k_site_link/>"><img src="#" srcset="<cms:show site_logox1/> 1x, <cms:show site_logox2/> 2x" alt="<cms:show logo_alt/>" style="width: 143px;"/></a>
	</div>
	<!--//header-->
	</cms:pages>
	<div class="main-content-agile">
		<div class="sub-main-w3">	
			<div class="wthree-pro">
				<h2>Forgot Password</h2>
			</div>
			
			
			<cms:if k_logged_in >
			<!-- what is an already logged-in member doing on this page? Send back to homepage. -->
				<cms:redirect k_site_link />
			</cms:if>
    
			<!-- are there any success messages to show from previous actions? -->
			<cms:set success_msg="<cms:get_flash 'success_msg' />" />
			<cms:if success_msg >
			<div class="notice">
            <cms:if success_msg='1' >
                A confirmation email has been sent to you<br />
                Please check your email.
            <cms:else />
                Your password has been reset<br />
				Please check your email for the new password.
            </cms:if>
			</div>
			<cms:else />
        
        <!-- now the real work -->
        <cms:set action="<cms:gpc 'act' method='get'/>" />
        
        <!-- is the visitor here by clicking the reset-password link we emailed? -->
        <cms:if action='reset' >
            <h1>Reset Password</h1>
        
            <cms:process_reset_password 
				send_mail='0'
			/>
            
            <cms:if k_success >
				<cms:send_mail from='info@satsong.net' to=k_user_email subject='Your new password' debug='0'>
					Your password has been resetted for the following site and username: 
					<cms:show k_site_link />
					Username: <cms:show k_user_name />

					New Password: <cms:show k_user_new_password />

					Once logged in you can change your password.
				</cms:send_mail>
			
                 <cms:set_flash name='success_msg' value='2' />
                 <cms:redirect k_page_link />          
            <cms:else />
                <cms:show k_error />
            </cms:if>
        
        <cms:else />
			
			
			<!-- show the lost-password form -->

            <cms:form method="post" anchor='0'>
                <cms:if k_success>
                
                    <!-- the 'process_forgot_password' tag below expects a field named 'k_user_name' -->
                    <cms:process_forgot_password 
						send_mail='0'
					/>
                    
                    <cms:if k_success>
						<cms:send_mail from='info@satsong.net' to=k_user_email subject='Password reset requested' debug='0'>          
							A request was received to reset your password for the following site and username: 
							<cms:show k_site_link />
							Username: <cms:show k_user_name />

							To confirm that the request was made by you please visit the following address, otherwise just ignore this email.
							<cms:show k_reset_password_link />
						</cms:send_mail> 
					
                        <cms:set_flash name='success_msg' value='1' />
                        <cms:redirect k_page_link /> 
                    </cms:if>    
                </cms:if>
                
                <cms:if k_error >
                    <h3 style='padding:10px'><font color='#d50000'><cms:show k_error /></font></h3>
                </cms:if>
                
                <cms:input type='text' name='k_user_name' placeholder='Your Email'/><br/>

                <input type="submit" name="submit" value="Send Reset Mail"/> 

            </cms:form>
			
			
		</div>
	</div>
	<!--//main-->
	<cms:pages masterpage='includes/setting.php'>
	<!--footer-->
	<div class="footer">
		<p><cms:show copyrights/></p>
	</div>
	<!--//footer-->
	</cms:pages>
</div>

      </cms:if>
    </cms:if>

</body>
</html>

<?php KConn::invoke(); ?>