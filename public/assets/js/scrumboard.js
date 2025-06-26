//Init
init();

//Button Functions------------------------------------------
function init() {
  $(document).on("click", ".button-backlog", function() {
    if (!($(this).closest(".backlog").length > 0)) {
      $(this).parents(".input-group").appendTo(".backlog").css({
        "background-color": "#",
        "border": ""
      });
    }
  });
  $(document).on("click", ".button-progress", function() {
    if (!($(this).closest(".in-progress").length > 0)) {
      $(this).parents(".input-group").appendTo(".in-progress")
    }
  });
  $(document).on("click", ".button-onhold", function() {
    if (!($(this).closest(".onhold").length > 0)) {
      $(this).parents(".input-group").appendTo(".onhold")
    }
  });
  
  $(document).on("click", ".button-done", function() {
    if (!($(this).closest(".done").length > 0)) {
      $(this).parents(".input-group").appendTo(".done")
    }
  });
  $(document).on("click", ".button-delete", function() {
    $(this).parents(".input-group").remove();
  });

  var placeholderDiv = document.createElement("div");
  var placeholderAtt = document.createAttribute("class");
  var taskDivAttVal = placeholderAtt.value = "placeholder";
  placeholderDiv.setAttributeNode(placeholderAtt);

  
   $("#dragable-1, #dragable-2, #dragable-3,#dragable-4").sortable(
        {
	  connectWith: "#dragable-1, #dragable-2, #dragable-3,#dragable-4",
	  items: ".dragable-scrumb"
	  
	});
}
	
	
const ps111 = new PerfectScrollbar('.scrumb-backlog', {
	useBothWheelAxes:true,
	suppressScrollX:true,
});
const ps112 = new PerfectScrollbar('.scrumb-progress', {
	useBothWheelAxes:true,
	suppressScrollX:true,
});

const ps113 = new PerfectScrollbar('.scrumb-onhold', {
	useBothWheelAxes:true,
	suppressScrollX:true,
});

const ps114 = new PerfectScrollbar('.scrumb-done', {
	useBothWheelAxes:true,
	suppressScrollX:true,
});

function showHideDiv(ele) {
	
	var srcElement = document.getElementById(ele);
	if (srcElement != null) {
		if (srcElement.style.display == "block") {
			srcElement.style.display = 'none';
		}
		else {
			srcElement.style.display = 'block';
		}
		return false;
	}
}

//Create Task------------------------------------------
$(".add_btn").each(function(index, ele){
  $(ele).on("click", function() {
  
  var taskDiv = document.createElement("div");
  var taskSpan = document.createElement("span");
  var taskdropdown = document.createElement("span");
  var dropdownDiv = document.createElement("div");
  
 

  var taskDivAtt = document.createAttribute("class");
  var dropdownDivAtt = document.createAttribute("class");

  var taskDivAttVal = taskDivAtt.value = "input-group overflow scrum-board-item portlet-card ui-sortable-handle";
  var dropdownDivAttVal = dropdownDivAtt.value = "dropdown dropleft float-right text-right ml-auto";

  taskDiv.setAttributeNode(taskDivAtt);
  dropdownDiv.setAttributeNode(dropdownDivAtt);
  
  var textVal = $(ele).parent().parent().find("input[type=text]").val();
  
  if(textVal == "" || textVal.trim() == ""){
  	alert("Please enter some text");
  	return;
  }
  
  $(ele).parent().parent().find("input[type=text]").val('');
  var div_class = $(ele).prop('title');
  
  var parentDivId = $(ele).parent().parent().parent().prop('id');
  
  showHideDiv(''+parentDivId);
  
  var taskText = document.createTextNode(textVal);

  taskSpan.appendChild(taskText);
  taskDiv.appendChild(taskSpan);
  taskDiv.appendChild(dropdownDiv);

  $('.'+ div_class ).append(taskDiv);
  
  init();

});


});







   
 
   
       
  
