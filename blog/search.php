<?php require_once('../admin/cms.php')?>
<cms:template title="Search" hidden='1'/>

<!--Header starts here-->
<cms:trim "<cms:embed 'search_header.html'/>"/>
<!--Header end here--><cms:pages masterpage='includes/setting.php'>    <!-- Page Header Section Start -->		
<section class="page--header--section banner--item pt--130 pb--60 bg--overlay" data-bg-img="<cms:show site_open_graph/>">			
<div class="container h--100">				
	<!-- Page Header Title Start -->				
	<div class="page--header-title text-capitalize">					
		<h2 class="h1">Search</h2>					
		<hr>					
		<h2 class="h3">Find Any Posts You Like</h2>				
	</div>				
	<!-- Page Header Title End -->			
	</div>		
</section>    
<!-- Page Header Section End --></cms:pages>
<!-- Blog Section Start -->
    <section class="blog--section pt--80 pb--20">
        <div class="container">
             <div class="row">
				<div class="col-md-8 pb--60">
				<cms:search masterpage="blog/index.php" limit="5" keywords="<cms:gpc 's' />">
				        <!-- Post Items Start -->
                        <div class="post--items">
						<cms:if k_paginated_top >
							<div class="search--widget post--item">
								<cms:search_form form="searchform" msg="Searching for..." button="Search" processor="<cms:show k_site_link/>blog/search.php"></cms:search_form>
								<h4>Search Results:</h4>
								<cms:if k_paginator_required >
								Page <cms:show k_current_page /> of <cms:show k_total_pages /><br>
								</cms:if>
								<h5><cms:show k_total_records /> Pages Found - Displaying: <cms:show k_record_from /> - <cms:show k_record_to /></h5>
							</div>	
						</cms:if>
                            <!-- Post Item Start -->
                            <div class="post--item">
                                <div class="post--img">
                                    <img src="<cms:show blog_image_figure/>" alt="<cms:show k_page_title/>">

                                    <p class="date"><a href="#"><cms:date k_page_date format='jS M, y'/></a></p>
                                </div>

                                <div class="post--inner">
                                    <ul class="nav meta">
                                        <li><a href="#"><i class="fa fa-user-o"></i><cms:show author/></a></li>
                                        <li><a href="#"><i class="fa fa-heart-o"></i><cms:show page_hits/></a></li>
                                    </ul>

                                    <div class="title">
                                        <h2 class="h4"><a href="<cms:show k_page_link/>" class="btn-link"><cms:show k_search_title /></a></h2>
                                    </div>

                                    <div class="content">
										<cms:show k_search_excerpt />
                                    </div>

                                    <div class="action">
                                        <a href="<cms:show k_page_link/>" class="btn btn-lg btn-default">Continue Reading<i class="ml--8 fa fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <!-- Post Item End -->
						</div>
	
						<!-- Post Items Start -->
                        <div class="post--items">
							<cms:no_results>
							<div class="search--widget">
								<cms:search_form form="searchform" msg="Searching for..." button="Search" processor="<cms:show k_site_link/>blog/search.php"></cms:search_form>
							</div>	
								<cms:pages masterpage='includes/setting.php'><cms:show search_error/></cms:pages>
								<div class="action">								
									<button class="btn btn-lg btn-default"><a href="<cms:show k_site_link/>blog/" style="color:inherit">Back to Blog</a></button>
								</div>			
							</cms:no_results>
                        </div>
                        <!-- Post Items End -->
						
                        <!-- Pagination Start -->
                        <nav class="pagination--nav text-center">
                            <ul class="pagination">
							<cms:paginator adjacents='1'>
								<cms:if k_crumb_type='prev' ><li><a href="<cms:show k_crumb_link />" class="btn btn-default active"><i class="fa fa-angle-double-left" aria-hidden="true"></i></a></li></cms:if>
								<cms:if k_crumb_type='page' ><li <cms:if k_crumb_current >class="active"</cms:if> ><a href="<cms:show k_crumb_link />" class="btn btn-default"><cms:show k_crumb_text /></a></li></cms:if>
								<cms:if k_crumb_type='next' ><li><a href="<cms:show k_crumb_link />" class="btn btn-default active"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a></li></cms:if>
							</cms:paginator>
                            </ul>
                        </nav>
                        <!-- Pagination End -->
						
				</cms:search>
                </div>
				
                <div class="col-md-4 pb--60">
                    <!-- Sidebar Start -->
					<cms:embed 'blog/blog_aside.html'/>
                    <!-- Sidebar End -->
                </div>
            </div>
        </div>
    </section>
<!-- Blog Section End -->
<cms:embed "footer.html"/>
<?php KConn::invoke();?>