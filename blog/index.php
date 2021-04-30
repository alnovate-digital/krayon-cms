<?php require_once('../admin/cms.php');?>
<cms:template parent='_posts_' icon='pencil' title='Blog' clonable='1' order='1'>
	<cms:config_list_view searchable='1' orderby='weight' order='desc' limit='25' exclude='default-page'>
		<cms:field 'k_selector_checkbox' />
		<cms:field 'k_page_title' header='Article'/>
		<cms:field 'k_comments_count' />
		<cms:field 'k_page_date' />
		<cms:field 'k_actions' class='actions'/>
			<cms:style>
			.col-actions{
				width: 10% !important;
			}
			</cms:style>
	</cms:config_list_view> 
	<cms:editable name='post_row_a' type='row' order='1'>
	<cms:editable
        name='author'
        label='Author'
        opt_values='list_authors.html'
        opt_selected = 'current_author.html'
        dynamic='opt_values | opt_selected'
        type='dropdown'
		class='col-sm-3'
		width='163'	
		order='1'
    />	
	<cms:editable 		
		name='page_hits' 		
		label='Page hits' 		
		type='text' 		
		search_type='integer' 	
		class='col-sm-3'
		width='163'		
		order='2'	
	/>		
	<cms:editable 		
		name='tag' 		
		type='relation' 		
		masterpage='blog/tag.php' 		
		label='Tag' 	
		advanced_gui='1'
		class='col-sm-3'
		width='163'			
		order='4'			
	/>
	</cms:editable>	
	<cms:editable 		
		name='feature_image' 		
		label='Feature Image' 		
		type='image'		
		show_preview='1'		
		preview_width='250'		
		order='5'	
	/>
	<cms:editable 		
		name='post_content' 		
		label='Post Content' 		
		type='richtext' 		
		order='6'	
	/>
</cms:template>
<cms:trim "<cms:embed 'blog/blog_header.html'/>"/>
<cms:if k_is_page>
    <cms:no_cache />
    <cms:php>
        // identify bots
        global $CTX;
        if( isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT']) ){
            $CTX->set( 'is_bot', '1', 'global' );
        }        
    </cms:php>
    <cms:if "<cms:not is_bot />">
        <cms:db_persist 
            _masterpage=k_template_name 
            _page_id=k_page_id 
            _mode='edit'          
            page_hits="<cms:add page_hits '1' />"
        /> 
    </cms:if>
<cms:trim "<cms:embed 'blog/blog_banner.html'/>"/>	
<cms:trim "<cms:embed 'blog/blog.html'/>"/>
<cms:else/>
	<cms:trim "<cms:embed 'blog/blog_list_banner.html'/>"/>	
	<cms:trim "<cms:embed 'blog/blog_list.html'/>"/>
</cms:if>
<cms:trim "<cms:embed 'footer.html'/>"/>
<?php KConn::invoke();?>