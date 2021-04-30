<?php require_once( '../admin/cms.php' ); ?>
<cms:template clonable='1' title='Users' hidden='1'>	<cms:config_list_view searchable='1' orderby='weight' order='desc' limit='25' exclude='default-page'>		<cms:field 'k_selector_checkbox' />		<cms:field 'k_page_title' header='Name'/>		<cms:field 'k_page_date' />		<cms:field 'k_actions' class='actions'/>			<cms:style>			.col-actions{				width: 10% !important;			}			</cms:style>	</cms:config_list_view>

</cms:template>
    <!-- 
        If additional fields are required for users, they can be defined here in the usual manner.
		<cms:redirect url=''/>
    -->  
<cms:redirect url="<cms:show k_site_link/>users/login"/>
<?php KConn::invoke(); ?>