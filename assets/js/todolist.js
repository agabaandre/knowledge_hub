$(function(){
	
	
	/////__________CHECH-ALL
	
	$(document).on("click", "#checkAll", function(){
		if(this.checked){
			  $('input:checkbox').prop('checked', this.checked); 
			 $('.label-text').css("text-decoration", "line-through");  
		  }
		  else{
			  $('input:checkbox').prop('checked', this.checked);
			  $('.label-text').css("text-decoration", "inherit");  
		  }
	});
	
	$(".main-mail-checkbox  :checkbox").each(
		function(index, elem){
		$(elem).on("click", function(){
			 if(elem.checked){
			  $(elem).closest(".task-task-item").find(".label-text").css("text-decoration", "line-through");      
		  }
		  else{
			  $(elem).closest(".task-task-item").find(".label-text").css("text-decoration", "none");
		  }
		});
	});
	
	
	/////__________ADD Task
	
	 var taskCounter = 1;
		 $(function(){
			 $(document).on("click", "#btn-add-todo", function(event){
				event.preventDefault();
                var htmlContent = ' <div class="task-task-item edit edit_number_link" id="edit_number_link'+taskCounter +'"    > <div class="main-mail-checkbox"> <label class="ckbox"><input type="checkbox"> <span></span></label> </div> <div class="main-mail-body ml-1"> <div class="label-text"> <div class="main-mail-subject"> <div class="d-lg-flex d-md-block"> <strong class="mb-0 float-left task-title" data-toggle="modal" data-target="#edit-task-modal" id="textvalue'+taskCounter+'">'+$("#add_title").val()+'</strong> <ul class="task-actions float-right"> <li><a href="#" class="task-action-items"><i class="bx bx-star important-task text-warning mr-1 pr-1"></i></a></li> <li><a href="#" class="task-action-items"><i class="bx bx-error-circle error-task  mr-1 pr-1"></i></a></li> <li><a href="#" class="task-action-items"><i class="bx bx-badge-check  completed-task  mr-1 pr-1"></i></a></li> <li><a href="#" class="task-action-items"><i class="bx bx-trash delete-task mr-0"></i></a></li> <li class="action-dropdown custom-dropdown-icon task-action-items" ><div class="dropdown-menu" aria-labelledby="dropdown-list"> <a class="edit dropdown-item" href="javascript:void(0);">Front-end</a> <a class="important dropdown-item" href="javascript:void(0);">Back-end</a> <a class="dropdown-item delete" href="javascript:void(0);">Issues</a> </div> </li> </ul> </div> </div> <div class="d-block"> <p class="task_due_date  tx-11 mt-1 mb-0 text-muted">'+$("#add_due_date").val()+'</p> <span data-toggle="modal" data-target="#edit-task-modal" class="task_descr">'+$("#add_descr").val()+'</span> </div> </div> </div> </div>';
                 $("#add_close").click();
				 
				 $("#tab-1").append(htmlContent);
				  $("#add_title").val('');
				  $("#add_descr").val('');
				  $("#add_due_date").val('');
				  taskCounter++;
				  initializationsAb();
			 });
			$(document).on("click", "#edit_update_btn", function(event){
				event.preventDefault();
				if($("#edit_task_title").val().trim()==""){
					$("#edit_task_title").focus();
					return;
				}
				
				var target_div_id = $("#edit_clicked_id").val();
				var element = $("#"+target_div_id);
				var title_task = $("#edit_task_title").val();
                $(element).find('.task-title').html(title_task);
                
                var task_descr = $("#edit_task_descr").val();
                
                $(element).find('.task_descr').html(task_descr);
    
                var task_due_date = $("#edit_due_date").val();
                $(element).find('.task_due_date').html(task_due_date);

                $("#edit_task_title").val('');
                
                $("#edit_task_descr").val('');
                $("#edit_due_date").val('');
                $("#edit_close").click();
            });
            
            $(document).on("click", "#edit_cancel_btn", function(event){
				event.preventDefault();
				$("#edit_task_title").val('');
       
                $("#edit_task_descr").val('');
                $("#edit_close").click();
			});
		});
		
		

	$(".main-mail-checkbox  :checkbox").each(
		function(index, elem){
		  if(elem.checked){
				$(elem).closest(".task-task-item").find(".label-text").css("text-decoration", "line-through");      
		  }
		  else{
			  $(elem).closest(".task-task-item").find(".label-text").css("text-decoration", "none");
		  }
		  $(elem).on("click", function(){
			 if(elem.checked){
				$(elem).closest(".task-task-item").find(".label-text").css("text-decoration", "line-through");      
		  }
		  else{
			  $(elem).closest(".task-task-item").find(".label-text").css("text-decoration", "none");
		  }
		  });
	});
	
	
	/////_________EDIT TASK
	
	var editTask=function(){
	console.log("Edit Task...");
	console.log("Change 'edit' to 'save'");


	var listItem=this.parentNode;

	var editInput=listItem.querySelector('input[type=text]');
	var label=listItem.querySelector("label");
	var containsClass=listItem.classList.contains("editMode");
			//If class of the parent is .editmode
			if(containsClass){

			//switch to .editmode
			//label becomes the inputs value.
				label.innerText=editInput.value;
			}else{
				editInput.value=label.innerText;
			}

			//toggle .editmode on the parent.
			listItem.classList.toggle("editMode");
	}
});


/////_________SEARCH

	$('#todo-search').keyup(function() {
		var searchTerm = $(this).val().toLowerCase();
		 var chkMatchCount = 0;
		$('.tasks-list-box .task-task-item').each(function() {
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


/////________TASK_MENU
	
$('.task-menu').on('click', function(event) {
		$('.tab-list-items').addClass('task-menu-show');
		$('.task-overlay').addClass('task-overlay-show');
})
$('.task-overlay').on('click', function(event) {
	$('.tab-list-items').removeClass('task-menu-show');
	$('.task-overlay').removeClass('task-overlay-show');
})

/////________TASK_ACTIONS

function initializationsAb(){
	
	
	/////__________Important task
	$(document).on('click', '.task-actions li .important-task',function(){
		if ( $(this).hasClass('text-warning') ) {
			$(this).removeClass('text-warning');
		} else {
			$('.contact-actions-icons.text-warning').removeClass('text-warning');
			$(this).addClass('text-warning');    
		}
	});
	
	
	/////__________Error-task
	$(document).on('click', '.task-actions li .error-task',function(){
		if ( $(this).hasClass('text-danger') ) {
			$(this).removeClass('text-danger');
		} else {
			$('.contact-actions-icons.text-danger').removeClass('text-danger');
			$(this).addClass('text-danger');    
		}
	});
	
	/////__________Completed task
	$(document).on('click', '.task-actions li .completed-task',function(){
		if ( $(this).hasClass('text-success') ) {
			$(this).removeClass('text-success');
		} else {
			$('.contact-actions-icons.text-success').removeClass('text-success');
			$(this).addClass('text-success');    
		}
	});
	
	$(".delete-task").click(function () {
		$(this).closest('.task-task-item').fadeTo(400, 0, function () { 
			$(this).remove();
		});
		return false;
	});
	
	$(".edit_number_link").each(function(index, element){
		   $(element).on('click', function(){
                var title_task = $(element).find('.task-title').html();
                var due_date_task = $(element).find('.task_due_date').html();
                var task_descr = $(element).find('.task_descr').html();
                $("#edit_task_title").val(title_task);
                $("#edit_task_descr").val(task_descr);
                $("#edit_due_date").val(due_date_task);
                $("#edit_clicked_id").val(element.id);
            });
                
     });
}
$(function(){initializationsAb()});



/////________P_SCROLL

const ps78 = new PerfectScrollbar('.tab-list-items', {
	useBothWheelAxes:true,
	suppressScrollX:true,
});

const ps79 = new PerfectScrollbar('.tasks-list-box', {
	useBothWheelAxes:true,
	suppressScrollX:true,
});