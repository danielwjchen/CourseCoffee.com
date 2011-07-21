var body = $(".body");
var header = $(".header");
var updatePageHeight = function() {
	var newHeight = body.outerHeight(true) + header.outerHeight(true) + 150;
	newHeight = (newHeight > 800) ? newHeight : 800;
	if ($('.container').height() < newHeight) {
		$('.container').height(newHeight);
	}
};

$(document).ready(function() {
	$(".login .action").click(function() {
		body.removeClass('welcome');
		body.addClass('home');
		navigation.load('navigation.php');
		body.load('home.php', function() {
			updatePageHeight();
		});
	});

	$('input').focus(function(e) {
		$(this).attr('temp', $(this).val());
		$(this).val('');
	});
	$('.input').blur(function(e) {
		if ($(this).attr('temp')) {
			$(this).val($(this).attr('temp'));
			$(this).removeAttr('temp');
		}
	});
	$('.input').change(function(e) {
		if ($(this).attr('temp')) {
			$(this).removeAttr('temp');
		}
	});


	/*--Agenda--*/
	$(".tabs").delegate(".now", "click", function(){
		$(".now").addClass("active");
		$(".today").removeClass("active");
		$(".tomorrow").removeClass("active");
		$(".panel-tasks").load("tasks-agenda-01.php");
		$(".panel-details").load("details-iss-231.php");
	});
	$(".tabs").delegate(".today", "click", function(){
		$(".now").removeClass("active");
		$(".today").addClass("active");
		$(".tomorrow").removeClass("active");
		$(".panel-tasks").load("tasks-agenda-02.php");
		$(".panel-details").load("details-tc-201.php");
	});
	$(".tabs").delegate(".tomorrow", "click", function(){
		$(".now").removeClass("active");
		$(".today").removeClass("active");
		$(".tomorrow").addClass("active");
		$(".panel-tasks").load("tasks-agenda-03.php");
		$(".panel-details").load("details-ent-445.php");
	});
	$(".content").delegate(".tc-201-link", "click", function(){
		$(".task-tc-201").addClass("active");
		$(".task-iss-213").removeClass("active");
		$(".task-ent-445").removeClass("active");
		$(".task-cse-323").removeClass("active");
		$(".task-iha-101").removeClass("active");
		$(".task-details-friends").remove();
		$("<dd class='task-details-friends'></dd>").insertAfter(".task-text-tc-201");
		$(".task-details-friends").load("details-friends-01.php");
		$(".panel-details").load("details-tc-201.php");
	});
	$(".content").delegate(".iss-231-link", "click", function(){
		$(".task-tc-201").removeClass("active");
		$(".task-iss-213").addClass("active");
		$(".task-ent-445").removeClass("active");
		$(".task-cse-323").removeClass("active");
		$(".task-iha-101").removeClass("active");
		$(".task-details-friends").remove();
		$("<dd class='task-details-friends'></dd>").insertAfter(".task-text-iss-213");
		$(".task-details-friends").load("details-friends-02.php");
		$(".panel-details").load("details-iss-231.php");
	});
	$(".content").delegate(".cse-322-link", "click", function(){
		$(".task-tc-201").removeClass("active");
		$(".task-iss-213").removeClass("active");
		$(".task-ent-445").removeClass("active");
		$(".task-cse-323").addClass("active");
		$(".task-iha-101").removeClass("active");
		$(".task-details-friends").remove();
		$("<dd class='task-details-friends'></dd>").insertAfter(".task-text-cse-322");
		$(".task-details-friends").load("details-friends-03.php");
		$(".panel-details").load("details-cse-322.php");
	});
	$(".content").delegate(".ent-445-link", "click", function(){
		$(".task-tc-201").removeClass("active");
		$(".task-iss-213").removeClass("active");
		$(".task-ent-445").addClass("active");
		$(".task-cse-323").removeClass("active");
		$(".task-iha-101").removeClass("active");
		$(".task-details-friends").remove();
		$("<dd class='task-details-friends'></dd>").insertAfter(".task-text-ent-445");
		$(".task-details-friends").load("details-friends-04.php");
		$(".panel-details").load("details-ent-445.php");
	});
	$(".content").delegate(".iha-101-link", "click", function(){
		$(".task-tc-201").removeClass("active");
		$(".task-iss-213").removeClass("active");
		$(".task-ent-445").removeClass("active");
		$(".task-cse-323").removeClass("active");
		$(".task-iha-101").addClass("active");
		$(".task-details-friends").remove();
		$("<dd class='task-details-friends'></dd>").insertAfter(".task-text-iha-101");
		$(".task-details-friends").load("details-friends-05.php");
		$(".panel-details").load("details-iha-101.php");
	});

	/*--Calendar--*/
	$(".tabs").delegate(".this-month", "click", function(){
		$(".this-month").addClass("active");
		$(".next-month").removeClass("active");
		$(".semester").removeClass("active");
		$(".panel-calendar").load("calendar-month-01.php");
		if($("#panel-01").css('marginLeft') == '-950px'){
			$("#panel-01").animate({marginLeft: "0px"});
			$("#panel-01").animate({marginRight: "-475px"});
			$("#panel-02").animate({marginLeft: "475px"});
			$("#panel-02").animate({marginRight: "-950px"});
			$("#panel-03").animate({marginLeft: "950px"});
			$("#panel-03").animate({marginRight: "-1425px"});
		}
	});
	$(".tabs").delegate(".next-month", "click", function(){
		$(".this-month").removeClass("active");
		$(".next-month").addClass("active");
		$(".semester").removeClass("active");
		$(".panel-calendar").load("calendar-month-02.php");
		if($("#panel-01").css('marginLeft') == '-950px'){
			$("#panel-01").animate({marginLeft: "0px"});
			$("#panel-01").animate({marginRight: "-475px"});
			$("#panel-02").animate({marginLeft: "475px"});
			$("#panel-02").animate({marginRight: "-950px"});
			$("#panel-03").animate({marginLeft: "950px"});
			$("#panel-03").animate({marginRight: "-1425px"});
		}
	});
	$(".tabs").delegate(".semester", "click", function(){
		$(".this-month").removeClass("active");
		$(".next-month").removeClass("active");
		$(".semester").addClass("active");
		$(".panel-calendar").load("calendar-month-03.php");
		if($("#panel-01").css('marginLeft') == '-950px'){
			$("#panel-01").animate({marginLeft: "0px"});
			$("#panel-01").animate({marginRight: "-475px"});
			$("#panel-02").animate({marginLeft: "475px"});
			$("#panel-02").animate({marginRight: "-950px"});
			$("#panel-03").animate({marginLeft: "950px"});
			$("#panel-03").animate({marginRight: "-1425px"});
		}
	});
	$(".content").delegate(".calendar-details", "click", function(){
		$("#panel-01").animate({marginLeft: "-950px"});
		$("#panel-02").animate({marginLeft: "0px"});
		$("#panel-02").animate({marginRight: "-475px"});
		$("#panel-03").animate({marginLeft: "475px"});
		$("#panel-03").animate({marginRight: "-950px"});
	});
	$(".content").delegate(".calendar-date-01", "click", function(){
		$(".panel-tasks").load("tasks-calendar-01.php");
	});
	$(".content").delegate(".calendar-date-02", "click", function(){
		$(".panel-tasks").load("tasks-calendar-02.php");
	});
	$(".content").delegate(".calendar-date-03", "click", function(){
		$(".panel-tasks").load("tasks-calendar-03.php");
	});
	$(".content").delegate(".calendar-date-04", "click", function(){
		$(".panel-tasks").load("tasks-calendar-05.php");
	});
	$(".content").delegate(".calendar-date-05", "click", function(){
		$(".panel-tasks").load("tasks-calendar-05.php");
	});

	/*--Courses--*/
	$(".body").delegate(".tabs-cse-322", "click", function(){
		$(".tabs-cse-322").addClass("active");
		$(".tabs-ent-445").removeClass("active");
		$(".tabs-iha-101").removeClass("active");
		$(".tabs-iss-231").removeClass("active");
		$(".tabs-tc-201").removeClass("active");
		$(".tabs-more-course").removeClass("active");
		$("#panel-01").load("course-cse-322.php");
		$("#panel-02").load("tasks-cse-322.php");
		if($("#panel-01").css('marginLeft') == '-950px'){
			$("#panel-01").animate({marginLeft: "0px"});
			$("#panel-01").animate({marginRight: "-475px"});
			$("#panel-02").animate({marginLeft: "475px"});
			$("#panel-02").animate({marginRight: "-950px"});
			$("#panel-03").animate({marginLeft: "950px"});
			$("#panel-03").animate({marginRight: "-1425px"});
		}
	});
	$(".body").delegate(".tabs-ent-445", "click", function(){
		$(".tabs-cse-322").removeClass("active");
		$(".tabs-ent-445").addClass("active");
		$(".tabs-iha-101").removeClass("active");
		$(".tabs-iss-231").removeClass("active");
		$(".tabs-tc-201").removeClass("active");
		$(".tabs-more-course").removeClass("active");
		$("#panel-01").load("course-ent-445.php");
		$("#panel-02").load("tasks-ent-445.php");
		if($("#panel-01").css('marginLeft') == '-950px'){
			$("#panel-01").animate({marginLeft: "0px"});
			$("#panel-01").animate({marginRight: "-475px"});
			$("#panel-02").animate({marginLeft: "475px"});
			$("#panel-02").animate({marginRight: "-950px"});
			$("#panel-03").animate({marginLeft: "950px"});
			$("#panel-03").animate({marginRight: "-1425px"});
		}
	});
	$(".body").delegate(".tabs-iha-101", "click", function(){
		$(".tabs-cse-322").removeClass("active");
		$(".tabs-ent-445").removeClass("active");
		$(".tabs-iha-101").addClass("active");
		$(".tabs-iss-231").removeClass("active");
		$(".tabs-tc-201").removeClass("active");
		$(".tabs-more-course").removeClass("active");
		$("#panel-01").load("course-iha-101.php");
		$("#panel-02").load("tasks-iha-101.php");
		if($("#panel-01").css('marginLeft') == '-950px'){
			$("#panel-01").animate({marginLeft: "0px"});
			$("#panel-01").animate({marginRight: "-475px"});
			$("#panel-02").animate({marginLeft: "475px"});
			$("#panel-02").animate({marginRight: "-950px"});
			$("#panel-03").animate({marginLeft: "950px"});
			$("#panel-03").animate({marginRight: "-1425px"});
		}
	});
	$(".body").delegate(".tabs-iss-231", "click", function(){
		$(".tabs-cse-322").removeClass("active");
		$(".tabs-ent-445").removeClass("active");
		$(".tabs-iha-101").removeClass("active");
		$(".tabs-iss-231").addClass("active");
		$(".tabs-tc-201").removeClass("active");
		$(".tabs-more-course").removeClass("active");
		$("#panel-01").load("course-iss-231.php");
		$("#panel-02").load("tasks-iss-231.php");
		if($("#panel-01").css('marginLeft') == '-950px'){
			$("#panel-01").animate({marginLeft: "0px"});
			$("#panel-01").animate({marginRight: "-475px"});
			$("#panel-02").animate({marginLeft: "475px"});
			$("#panel-02").animate({marginRight: "-950px"});
			$("#panel-03").animate({marginLeft: "950px"});
			$("#panel-03").animate({marginRight: "-1425px"});
		}
	});
	$(".body").delegate(".tabs-tc-201", "click", function(){
		$(".tabs-cse-322").removeClass("active");
		$(".tabs-ent-445").removeClass("active");
		$(".tabs-iha-101").removeClass("active");
		$(".tabs-iss-231").removeClass("active");
		$(".tabs-tc-201").addClass("active");
		$(".tabs-more-course").removeClass("active");
		$("#panel-01").load("course-tc-201.php");
		$("#panel-02").load("tasks-tc-201.php");
		if($("#panel-01").css('marginLeft') == '-950px'){
			$("#panel-01").animate({marginLeft: "0px"});
			$("#panel-01").animate({marginRight: "-475px"});
			$("#panel-02").animate({marginLeft: "475px"});
			$("#panel-02").animate({marginRight: "-950px"});
			$("#panel-03").animate({marginLeft: "950px"});
			$("#panel-03").animate({marginRight: "-1425px"});
		}
	});
	$(".body").delegate(".tabs-more-course", "click", function(){
		$(".new-course").load("course-add.php");
		$(".tabs-cse-322").removeClass("active");
		$(".tabs-ent-445").removeClass("active");
		$(".tabs-iha-101").removeClass("active");
		$(".tabs-iss-231").removeClass("active");
		$(".tabs-tc-201").removeClass("active");
		$(".tabs-more-course").addClass("active");
		$("<div class='new-course'></div>").insertBefore(".body");
		$(".new-course").load("course-add.php");
	});
	$(".content").delegate(".course-task-detail", "click", function(){
		$("#panel-01").animate({marginLeft: "-950px"});
		$("#panel-02").animate({marginLeft: "0px"});
		$("#panel-02").animate({marginRight: "-475px"});
		$("#panel-03").animate({marginLeft: "475px"});
		$("#panel-03").animate({marginRight: "-950px"});
	});


	/*--Details--*/
	$(".content").delegate(".comments", "click", function(){
		$(".comments").addClass("active");
		$(".recordings").removeClass("active");
		$(".downloads").removeClass("active");
	});
	$(".content").delegate(".recordings", "click", function(){
		$(".comments").removeClass("active");
		$(".recordings").addClass("active");
		$(".downloads").removeClass("active");
	});
	$(".content").delegate(".downloads", "click", function(){
		$(".comments").removeClass("active");
		$(".recordings").removeClass("active");
		$(".downloads").addClass("active");
	});
});
