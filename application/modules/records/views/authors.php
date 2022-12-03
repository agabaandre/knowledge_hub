<div class="py-3 gray">
<div class="container">
<div class="row align-items-center">
					
<?php foreach ($authors as $author): ?>
		<!-- Single -->
			<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
				<div class="jbr-wrap text-left border rounded">
					<div class="cats-box mlb-res rounded bg-white d-flex align-items-center justify-content-between px-3 py-3">
						<div class="cats-box rounded bg-white d-flex align-items-center">
							<div class="text-center"><img src="<?php echo base_url();?>assets/images/icons/author.png" class="img-fluid" width="55" alt=""></div>
							<div class="cats-box-caption px-2">
								<h4 class="fs-md mb-0 ft-medium"><?php echo truncate($author->name,40); ?></h4>
								<div class="d-block mb-2 position-relative">
									<span class="text-muted medium"><i class="fa fa-bank mr-1"></i><?php echo $author->address; ?></span>
									<span class="muted medium ml-2 theme-cl">
									<?php echo count($author->publications); ?> Resource<?php echo (count($author->publications)!==1)?'s':''; ?> <br> <?php echo $author->email; ?></span>
								</div>
							</div>
						</div>
						<div class="text-center mlb-last"><a href="<?php echo base_url();?>records/author_pubs/<?php echo $author->id ?>" class="btn gray ft-medium apply-btn fs-sm rounded">View Publications<i class="lni lni-arrow-right-circle ml-1"></i></a></div>
					</div>
				</div>
			</div>

<?php endforeach; ?>
</div>
</div>
</div>