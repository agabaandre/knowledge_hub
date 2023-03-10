<!-- Data for popover header -->
<div class="pop-head d-none">
<h6 class="text text-success">
Comments
</h6>
</div>
			
<!-- Data for popover content -->
<div class="pop-body d-none" style="max-height: 50%;" onmouseout="$().tooltip('hide')" onblur="">
    <div class="text"> 
    <?php if(count($row->comments)>0): ?>
    <div class="row px-3" id="comments<?php echo $i; ?>">
        <ul class="list-group col-lg-12">
            <?php 
            $com_count=0;
            foreach($row->comments as $comm): 
            $com_count++;
            //if($com_count<6):
            ?>
            <li class="app-comment">
                <h6><small><?php echo $comm->user->name; ?></small></h6>
                <p><?php echo $comm->comment; ?></p>
                <small class="text-success"><?php echo time_ago($comm->created_at); ?></small>
            </li>
            <?php 
            //endif;
            endforeach; ?>
            <a class="py-3" href="<?php echo base_url("records/show/") . $row->id; ?>">View All</a>
        </ul>
    </div>
    <?php endif; ?>
    </div>
</div>