<?php require_once('admin/cms.php')?>
<cms:template title='404' hidden='1'/>
<cms:embed 'header.html'/>
<cms:pages masterpage='k-includes/setting.php'>
    <!-- Page Header Section Start -->
		<section class="page--header--section banner--item pt--130 pb--60 bg--overlay" data-bg-img="<cms:show site_open_graph/>">
			<div class="container h--100">
				<!-- Page Header Title Start -->
				<div class="page--header-title text-capitalize">
					<h2 class="h1">404</h2>
					<hr>
					<h2 class="h3">Nothing to Be Found</h2>
				</div>
				<!-- Page Header Title End -->
			</div>
		</section>
    <!-- Page Header Section End -->
    <!-- 404 Section Start -->
        <div class="f0f--section pt--100 pb--100">
            <div class="container">
                <!-- 404 Content Start -->
                <div class="f0f--content pt--10 pb--30 text-center">
                    <div class="title">
                        <h3 class="h1">404</h3>
                    </div>

                        <cms:show p404_error/>

                    <div class="action">
                        <a href="<cms:show k_site_link/>" class="btn btn-lg btn-default">Back to Homepage<i class="ml--8 fa fa-check-circle"></i></a>
                    </div>
                </div>
                <!-- 404 Content End -->
            </div>
        </div>
        <!-- 404 Section End -->
</cms:pages>
<cms:embed 'footer.html'/>
<?php KConn::invoke();?>
