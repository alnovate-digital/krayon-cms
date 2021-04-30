<?php require_once( 'admin/cms.php' ); ?>
<cms:template title='Login' hidden='1'>
<!--If additional fields are required for users, they can be defined here in the usual manner.-->
</cms:template><cms:redirect url="<cms:show k_site_link/>users/login"/>

<?php KConn::invoke(); ?>
