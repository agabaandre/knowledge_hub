$(function() {
	
	/////__________Favorite-contact
	$(document).on('click', '.contact-actions-icons .fav-contact',function(){
		if ( $(this).hasClass('fill-warning') ) {
			$(this).removeClass('fill-warning');
		} else {
			$('.contact-actions-icons.fill-warning').removeClass('fill-warning');
			$(this).addClass('fill-warning');    
		}
	});
	
	
	/////__________Delete-contact
	
	$(document).on('click', '.delete-contact', function () {
		$(this).closest('.contact-section').fadeTo(400, 0, function () { 
			$(this).remove();
			$(this).closest(".bs-tooltip-bottom").find('.tooltip').css("display", "none")
		});
		return false;
	});
	
	
	/////__________ADD Task
	
	 var taskCounter = 0;
		 $(function(){
			 $(document).on("click", "#btn-add-todo", function(event){
				var htmlContent = ' <div class="task-task-item edit edit_number_link"> <div class="main-mail-checkbox"> <label class="ckbox"><input type="checkbox"> <span></span></label> </div> <div class="main-mail-body ml-1"> <div class="label-text"> <div class="main-mail-subject"> <div class="d-lg-flex d-md-block"> <strong class="mb-0 float-left" data-toggle="modal" data-target="#edit-task-modal" id="textvalue">'+$("#add_title").val()+'</strong> <ul class="task-actions float-right"> <li><a href="#" class="task-action-items"><i class="bx bx-star important-task text-warning mr-1 pr-1"></i></a></li> <li><a href="#" class="task-action-items"><i class="bx bx-error-circle error-task  mr-1 pr-1"></i></a></li> <li><a href="#" class="task-action-items"><i class="bx bx-badge-check  completed-task  mr-1 pr-1"></i></a></li> <li><a href="#" class="task-action-items"><i class="bx bx-trash delete-task mr-0"></i></a></li> <li class="action-dropdown custom-dropdown-icon task-action-items" > <a class="dropdown task-action-items" href="#" role="button" id="task-dropdown-list" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" onchange="task-dropdown-list"> <i class="bx bx-dots-vertical-rounded"></i> </a> <div class="dropdown-menu" aria-labelledby="dropdown-list"> <a class="edit dropdown-item" href="javascript:void(0);">Front-end</a> <a class="important dropdown-item" href="javascript:void(0);">Back-end</a> <a class="dropdown-item delete" href="javascript:void(0);">Issues</a> </div> </li> </ul> </div> </div> <div class="d-block"> <p class=" tx-11 mt-1 mb-0 text-muted">'+$("#add_due_date").val()+'</p> <span data-toggle="modal" data-target="#edit-task-modal">'+$("#add_descr").val()+'</span> </div> </div> </div> </div>';
                 $("#add_close").click();
				 event.preventDefault();
				 $("#tab-1").append(htmlContent);
				  $("#add_title").val('');
				  $("#add_descr").val('');
				  $("#add_due_date").val('');
				  taskCounter++;
			 });
		});
	 
		 $('#search-contact').keyup(function() {
			var searchTerm = $(this).val().toLowerCase();
			 var chkMatchCount = 0;
			$('.tab-content .contact-section').each(function() {
				var lineStr = $(this).text().toLowerCase();
				if (lineStr.indexOf(searchTerm) === -1) {
					$(this).hide();		
				} else {
					$(this).show();
					 chkMatchCount++;
					 $(this).addClass("contact-user-card");
					$("#errmsg").hide()
				}
			});
			if(chkMatchCount == 0 && searchTerm != ""){
				 $("#errmsg").html("No Results Found");
				$("#errmsg").show();
			}
			else if(searchTerm != ""){
			 
			}
			if(searchTerm == ""){
				$("#errmsg").hide(); 
			 }
			 
		});
});