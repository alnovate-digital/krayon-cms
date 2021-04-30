<?php require_once('../admin/cms.php');?>
<cms:template parent='_posts_' icon='tags' title='Tag' nested_pages='1' clonable='1' order='4' >	
	<cms:config_list_view orderby='weight' order='desc' limit='25' exclude='default-page'>
		<cms:field 'k_selector_checkbox' />
		<cms:field 'k_page_title' header='Tag'/>
		<cms:field 'k_actions' class='actions'/>
			<cms:style>
			.col-actions{
				width: 5% !important;
			}
			</cms:style>
	</cms:config_list_view> 
	<cms:editable 		
		name='feature_image'		
		label='Feature Image'		
		type='image'		
		show_preview='1'		
		preview_width='150'	
	/>
</cms:template>
<cms:trim "<cms:embed 'blog/blog_header.html'/>"/>
<cms:trim "<cms:embed 'blog/blog_list_banner.html'/>"/>
<cms:set my_custom_field_str="tag=<cms:show k_page_name />" />
<cms:trim "<cms:embed 'blog/tag.html'/>"/>
<cms:trim "<cms:embed 'footer.html'/>"/>
<?php KConn::invoke();?>