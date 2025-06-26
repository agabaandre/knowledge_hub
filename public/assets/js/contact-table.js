 $(function() {
 	$(document).on('click', '#contact-table', 'button.editbutton', function(ele) {
 		//the <tr> variable is use to set the parentNode from "ele"
 		var tr = ele.target.parentNode.parentNode;
 		// get the value from the cells (td) using the parentNode (var tr)
 		var id = tr.cells[0].textContent;
 		var check = $(tr.cells[1]).find("input[type=checkbox]").prop('checked', check);
 		var img = $(tr.cells[2]).find('img').prop('src');
 		var firstName = tr.cells[3].textContent;
 		var email = tr.cells[4].textContent;
 		var phone = tr.cells[5].textContent;
 		var favcontact = tr.cells[6].innerHTML;
 		var action = tr.cells[7].textContent;
 		//Prefill the fields with the gathered information
 		$('.modal-details').html('' + favcontact + '');
 		$('#editName').val(firstName);
 		$('#editEmail').val(email);
 		$('#editPhone').val(phone);
 		$('#editId').val(id);
 		$('#editcheckbox').val(check);
 		$('#editimage').prop('src', img);
 		$('#editfavicon').val(favcontact);
 		$('#editaction').val(action);
 		//If you need to update the form data and change the button link
 		$("form#ContactForm").attr('action', window.location.href + '/update/' + id);
 		$("a#saveDetails").attr('href', window.location.href + '/update/' + id);
 	});
 	//Click Event listener waiting for the click from the modal save button
 	$(document).on('click', 'div.modal-footer', 'button#saveDetails', function() {
 		//Get all information from the form field
 		var id = $('#editId').attr('value');
 		var firstName = $('#editName').val();
 		var email = $('#editEmail').val();
 		var phone = $('#editPhone').val();
 		var check = $('#editcheckbox').attr('checked');
 		var img = $('#editimage').attr('src');
 		var favcontact = $('#editfavicon').val();
 		//Set back the information on the table
 		$("#tr-" + id + " td:nth-child(1)").text(id);
 		$("#tr-" + id + " td:nth-child(2)").prop('checked', check);
 		$("#tr-" + id + " td:nth-child(3)").attr('src', img);
 		$("#tr-" + id + " td:nth-child(4)").text(firstName).css("font-weight", "bold");
 		$("#tr-" + id + " td:nth-child(5)").text(email);
 		$("#tr-" + id + " td:nth-child(6)").text(phone);
 	});
	
 	//Favorite contact
 	$(document).on('click', '.contact-icons .fav-contact', function() {
 		if ($(this).hasClass('fill-warning')) {
 			$(this).removeClass('fill-warning');
 		} else {
 			$('.contact-icons.fill-warning').removeClass('fill-warning');
 			$(this).addClass('fill-warning');
 		}
 	});
	
	
 	//Add contact
 	var taskCounter = 0;
 	$(function() {
 		$(document).on("click", "#btn-add-todo", function(event) {
 			if (!doValidate()) {
 				return;
 			}
 			var tokenCounter = 22;
 			var htmlContent = ' <tr id="tr-1"><td class="serial-num text-center">' + tokenCounter + '</td><td class="wd-5p"><div class="main-mail-checkbox"> <label class="ckbox"><input type="checkbox"><span></span></label></div></td><td class="wd-5p"><img alt="" src="../assets/img/faces/6.jpg" class="avatar avatar-md"></td><td><b>' + $("#add_name").val() + '</b></td><td>' + $("#add_email").val() + '</td><td class="contact-num"><i class="las la-phone mr-2"></i>' + $("#add_phone").val() + '</td><td class="text-center"><div class="contact-icons"><svg xmlns="http://www.w3.org/2000/svg" class="svg-size fav-contact fill-default" data-placement="top" data-toggle="tooltip" href="#" data-original-title="Favorite" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z"></path></svg></div></td><td id="td-1" class="d-flex text-center"> <button class="btn btn-sm editbutton fe fe-edit-2 tx-18-f text-primary" data-target="#contactmodal" data-toggle="modal" type="button"></button> <button class="btn btn-sm btn-icon fe fe-trash tx-18-f text-danger" type="button" id="delete-row"></button></td></tr>';
 			$("#addTskClose").click();
 			event.preventDefault();
 			$("#contact-table").append(htmlContent);
 			$("#add_name").val('');
 			$("#add_email").val('');
 			$("#add_phone").val('');
 			taskCounter++;
 			//initializationsAb();
 		});
 	});
	
	
 	//Delete contact  
 	$(document).on('click', '.delete-row', function() {
 		$(this).closest('tr').fadeTo(400, 0, function() {
 			$(this).remove();
 		});
 		return false;
 	});
    addSearchTrClass();
    
 	//Search contact  
 	$('#search-contact').keyup(function() {
		
 		var searchTerm = $(this).val().toLowerCase();
         var chkMatchCount = 0;
 		$('.contact-table tr').each(function() {
 			var lineStr = $(this).text().toLowerCase();
 			if (lineStr.indexOf(searchTerm) === -1) {
 				$(this).hide();
                 $(this).removeClass("search-tr"); 				
 			} else {
 				$(this).show();
                 chkMatchCount++;
                 $(this).addClass("search-tr");
 				$("#errmsg").hide()
 			}
 		});
        if(chkMatchCount == 0 && searchTerm != ""){
             $("#errmsg").html("No Results Found");
            $("#errmsg").show();
        }
        else if(searchTerm != ""){
        	
          initializePagination();
        }
		if(searchTerm == ""){
         	$("#errmsg").hide();           
             addSearchTrClass();
             initializePagination();
         }
         
 	});
	initializePagination();
	

 	//Calling pagination initialization once during page render
 	
 });

function addSearchTrClass(){
	$("#contactTable tr").each(function(){
    	$(this).addClass("search-tr");
    });
}

 function doValidate() {
 	if (document.appointment.requiredname.value == "") {
 		alert("Please enter your name");
 		document.appointment.requiredname.focus();
 		return false;
 	}
 	var readmail = document.appointment.requiredemail;
 	if (readmail.value == "" || readmail.value.indexOf("@") < 1) {
 		alert("Please enter the correct email address");
 		document.appointment.requiredemail.focus();
 		return false;
 	}
 	if (document.appointment.requiredphone.value == "") {
 		alert("Please enter your phone");
 		document.appointment.requiredphone.focus();
 		return false;
 	}
 	return true;
 }

 function initializePagination() {
 	//Paginaton
 	function getPageList(totalPages, page, maxLength) {
 		if (maxLength < 5) throw "maxLength must be at least 5";

 		function range(start, end) {
 			return Array.from(Array(end - start + 1), (_, i) => i + start);
 		}
 		var sideWidth = maxLength < 9 ? 1 : 2;
 		var leftWidth = (maxLength - sideWidth * 2 - 3) >> 1;
 		var rightWidth = (maxLength - sideWidth * 2 - 2) >> 1;
 		if (totalPages <= maxLength) {
 			// no breaks in list
 			return range(1, totalPages);
 		}
 		if (page <= maxLength - sideWidth - 1 - rightWidth) {
 			// no break on left of page
 			return range(1, maxLength - sideWidth - 1).concat([0]).concat(range(totalPages - sideWidth + 1, totalPages));
 		}
 		if (page >= totalPages - sideWidth - 1 - rightWidth) {
 			// no break on right of page
 			return range(1, sideWidth).concat([0]).concat(range(totalPages - sideWidth - 1 - rightWidth - leftWidth, totalPages));
 		}
 		// Breaks on both sides
 		return range(1, sideWidth).concat([0]).concat(range(page - leftWidth, page + rightWidth)).concat([0]).concat(range(totalPages - sideWidth + 1, totalPages));
 	}
 
 		// Number of items and limits the number of items per page
 		var numberOfItems = $(".search-tr").length; 
 		var limitPerPage = 9;
 		// Total pages rounded upwards
 		var totalPages = Math.ceil(numberOfItems / limitPerPage);
 		// Number of buttons at the top, not counting prev/next,
 		// but including the dotted buttons.
 		// Must be at least 5:
 		var paginationSize = 7;
 		var currentPage;

 		function showPage(whichPage) {
 			if (whichPage < 1 || whichPage > totalPages) return false;
 			currentPage = whichPage;
             var sliceFrom = (currentPage - 1) * limitPerPage;
             var sliceTill = currentPage * limitPerPage;
 			//below Original code was commented for the sake of search oriented pagination
              //$("#contactTable tr").hide().slice( sliceFrom, sliceTill).show();
 		     $(".search-tr").hide().slice( sliceFrom, sliceTill).show();

         	// Replace the navigation items (not prev/next):
 			$(".pagination li").slice(1, -1).remove();
 			getPageList(totalPages, currentPage, paginationSize).forEach(item => {
 				$("<li>").addClass("page-item " + (item ? "current-page " : "") + (item === currentPage ? "active " : "")).append($("<a>").addClass("page-link").attr({
 					href: "javascript:void(0)"
 				}).text(item || "...")).insertBefore("#next-page");
 			});
 			return true;
 		}
 		// Include the prev/next buttons:
 		$(".pagination").append($("<li>").addClass("page-item").attr({
 			id: "previous-page"
 		}).append($("<a>").addClass("page-link").attr({
 			href: "javascript:void(0)"
 		}).text("Prev")), $("<li>").addClass("page-item").attr({
 			id: "next-page"
 		}).append($("<a>").addClass("page-link").attr({
 			href: "javascript:void(0)"
 		}).text("Next")));
 		// Show the page links
 		$("#contactTable").show();
 		showPage(1);
 		// Use event delegation, as these items are recreated later
 		$(document).on("click", ".pagination li.current-page:not(.active)", function() {
 			return showPage(+$(this).text());
 		});
 		$(document).on("click", '#next-page', function() {
 			return showPage(currentPage + 1);
 		});
 		$(document).on("click", '#previous-page', function() {
 			return showPage(currentPage - 1);
 		});
 		$(document).on("click", ".pagination" , function() {
 			$("html,body").animate({
 				scrollTop: 0
 			}, 0);
 		});
 }