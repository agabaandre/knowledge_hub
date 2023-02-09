<?php 
        if(!is_guest()){
            if(!$row->is_favourite):
            ?>
            <span class="mr-1">
            <a href="<?php echo base_url(); ?>publications/add_to_favourites/<?php echo $row->id; ?>">
                <span class=" mr-2"><i class="fa fa-star mr-1"></i> Add to Favourites </span>
            </a>
            </span>
        <?php else: ?>
            <span class="mr-1">
            <a href="<?php echo base_url(); ?>publications/remove_favourite/<?php echo $row->id; ?>">
                <span class=" mr-2"><i class="fa fa-star mr-1 text-warning"></i> Remove from favourites </span>
            </a>
            </span>
        <?php 
        endif;
        }
    
?>