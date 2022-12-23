<div class="gray py-4">
<div class="container">
	<div class="row">

     <div class="col-lg-12">
     	<div class="row">
     		<h4 class="ml-3">Search Results</h4>
     	</div>
		
		 <div class="row text-success ml-2 mb-2">
			  <?php echo (isset($_GET['tag']))?" Tag: ".$_GET['tag']:""; ?>
		</div> 

     	<?php 
	     	include 'partials/publications.php';
     	 ?>
       
	</div>
</div>
</div>
</div>