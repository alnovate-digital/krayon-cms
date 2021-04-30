<?php require_once('../admin/cms.php');?>
<cms:template icon='cog' title='Setting' order='1' hidden='1'>
	<cms:editable name='site_id' label='Site Identity' type='group' order='1'>
		<cms:editable name='site_logo_light' label='Logo Light' type='image' group='site_id' order='1'/>
		<cms:editable name='site_logo_dark' label='Logo Dark' type='image' group='site_id' order='2'/>
		<cms:editable name='site_favicon' label='Favicon' type='image' group='site_id' order='3'/>
		<cms:editable name='site_name' label='Site Name' type='text' group='site_id' order='4'/>
		<cms:editable name='meta_title' label='Meta Title' type='text' group='site_id' order='5'/>
		<cms:editable name='meta_desc' label='Meta Description' type='text' group='site_id' order='6'/>
		<cms:editable name='meta_keywords' label='Meta Keywords' type='text' group='site_id' order='7'/>
		<cms:editable name='meta_theme_color' label='Theme Color' type='text' group='site_id' order='8'/>
		<cms:editable name='meta_author' label='Site Author' type='text' group='site_id' order='9'/>
		<cms:editable name='meta_google_verification' label='Google Verification' type='text' group='site_id' order='10'/>
		<cms:editable name='meta_facebook_app_id' label='Facebook App ID' type='text' group='site_id' order='11'/>
		<cms:editable name='site_open_graph' label='Open Graph' type='image' group='site_id' order='12'/>
	</cms:editable>
	<cms:editable name='footer' label='Footer' type='group' order='2'>
		<cms:editable name='footer_logo' label='Footer Logo' type='image' order='1'/>
		<cms:editable name='copyrights' label='Copyrights' type='text' order='2'/>
		<cms:repeatable name='site_social' label='Social' order='3'>
			<cms:editable name='site_social_link' label='Social Link' type='text'/>
			<cms:editable
				name='social_fa_icon'
				label='Social Icon'
				type='dropdown'
				dynamic='opt_values'
				opt_values='social_icon.html'
			/>
		</cms:repeatable>		
		<cms:repeatable name='footer_nav' label='Footer Menu' order='3'>
			<cms:editable name='footer_nav_item' label='Menu Item' type='text'/>
			<cms:editable name='footer_nav_link' label='Menu Link' type='text'/>
		</cms:repeatable>
		<cms:editable name="contact_address" label="Contact Address" type="richtext" order='4'/>
		<cms:editable name="search_error" label="Search Error" type="richtext" order='7'/>
		<cms:editable name="p404_error" label="404 Error" type="richtext" order='8'/>
	</cms:editable>
</cms:template>
<?php KConn::invoke();?>
