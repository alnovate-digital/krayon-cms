<?php require_once('admin/cms.php');?>
<cms:template parent='_pages_' icon='document' title='Page' nested_pages='1' clonable='1' order='1'>
<cms:config_form_view>
	<cms:field 'k_show_in_menu' group='_advanced_settings_' class='hidden' />
	<cms:field 'k_menu_text' group='_advanced_settings_' class='hidden' />
	<cms:field 'k_menu_link' group='_advanced_settings_' class='hidden' />
		<cms:style>
			.hidden{
				display: none !important;
			}
		</cms:style>
</cms:config_form_view>
<cms:config_list_view searchable='1' orderby='weight' order='asc' limit='25'>
	<cms:field 'k_selector_checkbox' />
	<cms:field 'k_page_title' header='Page' sortable='0' />
	<cms:field 'k_page_date' />
	<cms:field 'k_up_down' class='up_down'/>
	<cms:field 'k_actions' class='actions'/>
		<cms:style>
			.col-actions{
				width: 15% !important;
			}
			.col-up_down{
				width: 5% !important;
			}

		</cms:style>
</cms:config_list_view>
	<cms:mosaic name='header' label='Header' order='1'>
		<cms:tile name='slider' label='Slider'>
			<cms:repeatable name='slider' label='Slider' stacked_layout='1' order='1'>
				<cms:editable
					name='slider_image'
					label='Slider Image'
					type='image'
					order='1'
					col_width='180'
					show_preview='1'
					preview_width='150'
				/>
				<cms:editable
					name='slider_text'
					label='Slider Text'
					type='nicedit'
					order='2'
					col_width='180'
				/>
				<cms:editable
					name='slider_desc'
					label='Slider Description'
					type='nicedit'
					order='3'
					col_width='180'
				/>
				<cms:editable
					name='btn_default'
					label='Default'
					type='text'
					order='4'
					col_width='100'
				/>
				<cms:editable
					name='btn_default_link'
					label='Default Link'
					type='text'
					order='5'
					col_width='100'
				/>
			</cms:repeatable>
		</cms:tile>
		<cms:tile name='image_banner' label='Image Banner'>
			<cms:repeatable name='image_banner' label='Image Banner' stacked_layout='1' order='2'>
				<cms:editable
					name='image_banner_image'
					label='Banner Image'
					type='image'
					order='1'
					col_width='180'
					show_preview='1'
					preview_width='150'
				/>
				<cms:editable
					name='image_banner_title'
					label='Banner Title'
					type='text'
					order='2'
					col_width='180'
				/>
				<cms:editable
					name='image_banner_desc'
					label='Banner Description'
					type='nicedit'
					order='3'
					col_width='180'
				/>
			</cms:repeatable>
		</cms:tile>
		<cms:tile name='text_banner' label='Text Banner'>
			<cms:repeatable name='text_banner' label='Text Banner' stacked_layout='1' order='2'>
				<cms:editable
					name='text_banner_title'
					label='Banner Title'
					type='text'
					order='1'
					preview_width='150'
				/>
				<cms:editable
					name='text_banner_desc'
					label='Banner Description'
					type='nicedit'
					order='2'
					col_width='180'
				/>
			</cms:repeatable>
		</cms:tile>
	</cms:mosaic>
	<cms:mosaic name='content' label='Content' order='2'>
		<cms:tile name='text' label='Text'>
            <cms:editable name='text' label='Text' type='richtext' />
        </cms:tile>
		<cms:tile name='image' label='Image'>
            <cms:editable name='image' label='Image' type='image' />
        </cms:tile>
		<cms:tile name='section' label='Section'>
			<cms:editable
				name='section_style'
				label='Style'
				type='radio'
				opt_values='A-Style=a-style | B-Style=b-style'
			/>
            <cms:editable name='section_title' label='Title' type='text' order='1'/>
			<cms:editable name='section_superscript' label='Superscript' type='text' order='2'/>
			<cms:repeatable name='section_highlight' label='Highlight' order='3'>
				<cms:editable name='highlight_title' label='Title' type='text' order='1'/>
				<cms:editable name='highlight_text' label='Text' type='text' order='2'/>
			</cms:repeatable>
			<cms:repeatable name='section' label='Section' order='4'>
				<cms:editable
					name='section_type'
					label='Section Type'
					type='dropdown'
					opt_values='---Select---=none
					| About=about_section
					| Services=services_section
					| Number=number_section
					| Works=works_section
					| Features=features_section
					| Testimonial=testimonials_section
					| Clients=clients_section
					| Blog=blog_section
					'
				/>
				<cms:func _into='my_cond' section_type=''>
					<cms:if section_type='about_section'>show<cms:else />hide</cms:if>
				</cms:func>
				<cms:editable
					name='intro_image_md-5'
					label='Image'
					type='image'
					show_preview='1'
					preview_width='150'				
					not_active=my_cond
					class='col-sm-4'
					width='163'
				/>
				<cms:editable
					name='intro_image_md-7a'
					label='Image'
					type='image'
					show_preview='1'
					preview_width='150'				
					not_active=my_cond
					class='col-sm-4'
					width='163'
				/>
				<cms:editable
					name='intro_image_md-7b'
					label='Image'
					type='image'
					show_preview='1'
					preview_width='150'				
					not_active=my_cond
					class='col-sm-4'
					width='163'
				/>
			</cms:repeatable>
			<cms:repeatable name='section_content' label='Content' order='5'>
				<cms:editable
					name='section_content_type'
					label='Content Type'
					type='dropdown'
					opt_values='Text-Only
					| With-Icon=content_with_icon
					| With-Image=content_with_image
					'
					order='1'
				/>
				<cms:editable
					name='content_title'
					label='Title'
					type='text'
					order='2'
				/>
				<cms:editable
					name='content_text'
					label='Text'
					type='text'
					order='3'
				/>
				<cms:func _into='my_cond' section_content_type=''>
					<cms:if section_content_type='content_with_icon'>show<cms:else />hide</cms:if>
				</cms:func>
				<cms:editable
					name='with_icon'
					label='Icon'
					type='text'
					desc='Use Pixedon icons'
					not_active=my_cond
					order='4'
				/>
				<cms:func _into='my_cond' section_content_type=''>
					<cms:if section_content_type='content_with_image'>show<cms:else />hide</cms:if>
				</cms:func>
				<cms:editable
					name='with_image'
					label='Image'
					type='image'
					not_active=my_cond
					order='5'
				/>
			</cms:repeatable>
		</cms:tile>
		<cms:tile name='contact_page' label='Contact Page'>
				<cms:editable
					name='contact_form_title'
					label='Form Title'
					type='text'			
					class='col-sm-6'
				/>
				
				<cms:editable
					name='contact_form'
					label='Contact Form'
					type='dropdown'
					opt_values='---Select---=none
						| Form=contact_form
						'
					class='col-sm-6'
				/>

				<cms:editable
					name='contact_info_title'
					label='Info Title'
					type='text'			
					class='col-sm-6'
				/>
				<cms:editable
					name='contact_info_detail'
					label='Info Detail'
					type='richtext'			
					class='col-sm-6'
				/>
			
				<cms:editable
					name='google_map_lat'
					label='Google Map Latitude'
					type='text'			
					class='col-sm-4'
					width='163'
				/>
				<cms:editable
					name='google_map_lng'
					label='Google Map Longitude'
					type='text'			
					class='col-sm-4'
					width='163'
				/>
				<cms:editable
					name='google_map_api'
					label='Google Map API'
					type='text'			
					class='col-sm-4'
					width='163'
				/>
		</cms:tile>
	</cms:mosaic>
</cms:template>


<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<!-- SEO -->
	<cms:embed 'seo.html'/>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,500,600,700,800,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Plugins -->
  <link rel="stylesheet" href="includes/assets/css/plugins.css" />
  <!-- Core Style -->
		<!-- ==== Main Stylesheet ==== -->
		<link rel="stylesheet" href="includes/assets/css/style.css?v=20210314" />
		<!-- ==== Custom Stylesheet ==== -->
		<link rel="stylesheet" href="includes/assets/css/custom.css?v=20210314" />
</head>
<body>

<cms:embed 'page.html'/>

<!-- jQuery -->
<script src="includes/assets/js/jquery-3.0.0.min.js"></script>
<script src="includes/assets/js/jquery-migrate-3.0.0.min.js"></script>
<!-- plugins -->
<script src="includes/assets/js/plugins.js"></script>
<cms:pages masterpage="includes/setting.php">
<script>
<!-- custom scripts -->
/* ===============================  Navbar Menu  =============================== */
var wind = $(window);
wind.on("scroll", function () {
		var bodyScroll = wind.scrollTop(),
				navbar = $(".navbar"),
				logo = $(".navbar.change .logo> img");
		if (bodyScroll > 300) {
				navbar.addClass("nav-scroll");
				logo.attr('src', '<cms:show site_logo_dark/>');
		} else {
				navbar.removeClass("nav-scroll");
				logo.attr('src', '<cms:show site_logo_light/>');
		}
});
$('.navbar .search .icon').on('click', function () {
		$(".navbar .search .search-form").fadeIn();
});
$('.navbar .search .search-form .close').on('click', function () {
		$(".navbar .search .search-form").fadeOut();
});
function noScroll() {
		window.scrollTo(0, 0);
}
wind.on("scroll", function () {
		var bodyScroll = wind.scrollTop(),
				navbar = $(".topnav");
		if (bodyScroll > 300) {
				navbar.addClass("nav-scroll");
		} else {
				navbar.removeClass("nav-scroll");
		}
});
var open = false,
		navDark = $(".topnav.dark"),
		logoChan = $(".topnav.dark .logo img");
$('.topnav .menu-icon').on('click', function () {
		open = !open;
		$('.hamenu').toggleClass("open");
		if (open) {
				$('.hamenu').animate({ left: 0 });
				$('.topnav .menu-icon .text').addClass('open');
				navDark.addClass("navlit");
				logoChan.attr('src', '<cms:show site_logo_light/>');
				window.addEventListener('scroll', noScroll);
		} else {
				$('.hamenu').delay(300).animate({ left: "-100%" });
				$('.topnav .menu-icon .text').removeClass('open');
				navDark.removeClass("navlit");
				logoChan.attr('src', '<cms:show site_logo_dark/>');
				window.removeEventListener('scroll', noScroll);
		}
});
$('.hamenu .menu-links .main-menu > li').on('mouseenter', function () {
		$(this).css("opacity", "1").siblings().css("opacity", ".5");
});
$('.hamenu .menu-links .main-menu > li').on('mouseleave', function () {
		$(".hamenu .menu-links .main-menu > li").css("opacity", "1");
});
$('.main-menu > li .dmenu').on('click', function () {
		$(".main-menu").addClass("gosub");
		$(this).parent().parent().find(".sub-menu").addClass("sub-open");
});
$('.main-menu .sub-menu li .sub-link.back').on('click', function () {
		$(".main-menu").removeClass("gosub");
		$(".main-menu .sub-menu").removeClass("sub-open");
});
</script>
</cms:pages>
<script src="includes/assets/js/scripts.js"></script>
</body>
</html>

<?php KConn::invoke();?>
