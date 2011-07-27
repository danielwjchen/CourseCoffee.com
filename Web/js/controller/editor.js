/**
 * @file
 * Provide an interactive document editor
 */
$(document).ready(function(){
/*    global variables    */
	history_stack = [];
	history_pointer = 0;
	curr_date = new Date()
	curr_year = curr_date.getFullYear();
	curr_year = 2010;
	month=["january","february","march","april","may","june","july","august","september","october","november","december","jan","feb","mar",  "apr",  "may","jun", "jul", "aug","sep","sept","oct",       "nov",     "dec"];
	reg=[];
	reg[0]=/((0?[1-9])|(1[0-2])){1}\/((0?[1-9])|(1[0-9])|(2[0-9])|(3[0-1])){1}\/(\d{4}|\d{2})/gi;	// mm/dd/yy
	reg[1]=/(0?[1-9]|1[0-2]){1}\/(1[0-9]|2[0-9]|3[0-1]|0?[1-9]){1}/g;	// mm/dd(/2011 2012)
	reg[2] = /(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}\W+((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}\W*(\d{4})?/gi;
	reg[4] = /((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}\W{1,2}(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}\W*(\d{4})?/gi;
	//reg[1]=/((0?[1-9])|(1[0-9])|(2[0-9])|(3[0-1])){1}(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}.{1,3}\W*(2010|2011|$|\W{1,}\d{1,2}$)/gi;
	reg_idx = 0;
	schedule_list = [];
	content = "";
	word = [];
	delim = [];
	orig_html="";
/*       flags            */
	editing_flag = 0;
/*    data stuctures      */
	function sch(start_pos, end_pos, content){
		id_temp = start_pos.toString() + "," + end_pos.toString();
		date_temp = "";
		for (j=start_pos; j<end_pos-1; j++){
			date_temp = date_temp + word[j] + delim[j+1]
		}
		date_temp = date_temp + word[j];
		ret = parse_date(date_temp);
		match_pos=ret[0]; match_str=ret[1]; str_before=ret[2]; str_after=ret[3];
		if(reg_idx!=0 && reg_idx!=1){
			date_out = new Date(match_str+" 2011")
		} else {
			date_out = new Date(match_str)
		}
		year_temp = date_out.getFullYear()
		year_temp =2011
		if(year_temp<curr_year){
			return null;
		}
		var temp_sch = { 
			'id' : id_temp, 
			'start_pos' : start_pos, 
			'end_pos': end_pos, 
			'match_str' : match_str, 
			'match_pos' : match_pos,
			'str_before': str_before, 
			'str_after': str_after,
			'content': content , 
			'modified': 0, 
			'catergory': '', 
			date: date_out, 
			'next': false, 
			'deleted': false
		};
		return temp_sch;
	};
/*    functions           */				  
	function init(){
		$("#parsed_data").css("left" , $("#table_syl").position().left + $("#table_syl").width() + 50  );
		$("#tool_box").css("left",  $("#parsed_data").position().left + 50);
		$("#orig_syl").text("Loading Syllabus...");  
	}
	function parse_date(str){
		//alert(str)
		match_pos = str.search(reg[reg_idx])
		match_str = str.match(reg[reg_idx])
		//alert(str+"  -   "+match_str)
		if (match_str == null){
			str_before = "";
			str_after = str;
			match_str = "";
		} else {
			str_before = str.substring(0, match_pos-1)
			str_after = str.substring( (match_pos+match_str.toString().length), str.length);
			match_str = match_str.toString()
		}
		return [match_pos, match_str, str_before, str_after]
	}
	$(".del_date_label").live("click",function(){
		id_temp = $(this).attr("sid")
		for(i=0; i<schedule_list.length;i++){
			if(schedule_list[i].id==id_temp) {
				break;
			}
		}
		schedule_list[i].deleted = !(schedule_list[i].deleted);
		if (i>0){
			schedule_list[i-1].next = schedule_list[i].deleted
		}
		history_stack.push( $.extend(true, [], schedule_list) );
		show_schedule();
		adjust_spacing();
		update_date_label();
	});
	function update_date_label(){
		for(i=0;i<schedule_list.length;i++){
			if(schedule_list[i].deleted == false){ //NOT deleted
				$(".date_label[sid='"+schedule_list[i].id+"'] > span[sid='"+ schedule_list[i].id +"']").text("x");
				$(".date_label[sid='"+schedule_list[i].id+"']").fadeTo("fast", 1);
			} else { // deleted
				$(".date_label[sid='"+schedule_list[i].id+"'] > span[sid='"+ schedule_list[i].id +"']").text("O")
				$(".date_label[sid='"+schedule_list[i].id+"']").fadeTo("fast", 0.5)
			}
		}
	}
	$(".date_label").live("mouseenter",function(){
		temp_id = $(this).attr("sid")
		$(".schedule_elem[sid='"+temp_id+"']").css("background-color","yellow");
	});
	$(".date_label").live("mouseleave",function(){
		temp_id = $(this).attr("sid")
		$(".schedule_elem[sid='"+temp_id+"']").css("background-color","");
	});
	$(".sch_content").live("click",function(){
		/**
		 *
		if(editing_flag == 1) return;
		orig_sid=$(this).attr("sid")
		orig_html = $(this).text();
		orig_height = $(this).height()*0.8;
		orig_width = $(this).width();
		$(this).html("<textarea sid='"+orig_sid+"' style='width:"+orig_width+"px; height:"+orig_height+"px'>"+orig_html+"</textarea>\
		<input type='button' id='save_sch' sid='"+orig_sid+"' value='save'/><input type='button' id='cancel_sch' sid='"+orig_sid+"' value='cancel'/>");
		editing_flag=1;
		adjust_spacing();
		*/
	tt=$(this).html()//.search(/.{1}/gi);
	//alert(tt)
	});
	$(".btn[id='edit_sch']").live("click",function(){
		if(editing_flag == 1) return;
		orig_sid=$(this).attr("sid")
		orig_html = $(".sch_content[sid='"+orig_sid+"']").html().replace(/<br>/gi,"\n").replace(/&nbsp;/g, " ");
		orig_height = $(".sch_content[sid='"+orig_sid+"']").height()*0.8;
		orig_width = $(".sch_content[sid='"+orig_sid+"']").width();
		$(".sch_content[sid='"+orig_sid+"']").html("<textarea sid='"+orig_sid+"' style='width:"+"500"+"px; height:"+orig_height+"px'>"+orig_html+"</textarea>\<input type='button' id='save_sch' sid='"+orig_sid+"' value='save'/><input type='button' id='cancel_sch' sid='"+orig_sid+"' value='cancel'/>");
		editing_flag=1
		adjust_spacing();
	});
	$(".schedule_elem").live("mouseenter",function(){
		id_temp = $(this).attr("sid")
		$("td[id]", this).html("<input type='button' value='edit' class='btn' sid='"+id_temp+"' id='edit_sch' /> <input type='button' class='btn' sid='"+id_temp+"' value='delete' id='del_sch' />")
		adjust_spacing();
		adjust_parsed_data_pos();
	});
	$(".schedule_elem").live("mouseleave",function(){					
		$("td[id]", this).html("")
		adjust_spacing();
		adjust_parsed_data_pos();
	});
	$("#save_sch").live("click",function(){
		id_temp = $(this).attr("sid");
		content_temp = $("textarea[sid='"+id_temp+"']").val().replace(/(\n|\r)/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
		$(".sch_content[sid='"+$(this).attr("sid")+"']").html(content_temp);
		for(i=0;i<schedule_list.length;i++){
			if(schedule_list[i].id==id_temp){
				schedule_list[i].content = content_temp
				break;
			}
		}
		editing_flag=0
		history_stack.push($.extend(true, [], schedule_list));
	});
	$("#cancel_sch").live("click",function(){
		id_temp = $(this).attr("sid")
		content_temp = $("textarea[sid='"+id_temp+"']").text().replace(/(\n|\r)/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
		$(".sch_content[sid='"+$(this).attr("sid")+"']").html(content_temp);
		editing_flag=0;
	});
	function get_date_label_html(date_id, date_content){
		return "<span class='date_label' sid='"+date_id+"'>" + "<span>" + date_content + "</span><span sid='"+date_id+"' class='del_date_label'>x</span></span>";
	}
	function get_sch_html(date_id, date_t, content){	
		return '<div sid="'+date_id+'" class="schedule_elem" >' + 
			'<table width="500" border="0" cellpadding="0" cellspacing="0">' + 
				'<tr>' + 
					'<td width="135" height="40">Date:'+(date_t.getMonth()+1)+'/'+date_t.getDate()+'</td>' +
					'<td width="200" id="editing_btn">&nbsp;</td>' + 
					'<td width="149">&nbsp;</td>' + 
				'</tr>' + 
				'<tr>' +
					'<td colspan="3" class="sch_content" sid="'+date_id+'" >'+content+'</td>' + 
				'</tr>' + 
			'</table>' + 
		'</div>';
	}
	function create_schedule(){
		for(i=0;i<schedule_list.length-1;i++){
			sch_curr = schedule_list[i];
			sch_next = schedule_list[i+1];
			content_temp = sch_curr.str_after;
			for(j=sch_curr.end_pos; j<sch_next.start_pos; j++){
				content_temp = content_temp + word[j] +delim[j+1];
			}
			content_temp = content_temp + sch_next.str_before;
			sch_curr.content = content_temp.replace(/(\n|\r)/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
		}
	}
	function show_schedule(){
		schedule_lc = ""; //create schedule html
		i_idx=0;
		for(i=0; i<schedule_list.length-1; i++){
			if(schedule_list[i].deleted==false){
				i_idx=i;
				content_temp=schedule_list[i].content;
				if(schedule_list[i].next==true){
					do{
						content_temp = content_temp + schedule_list[i+1].content;
						i=i+1;
					}while(schedule_list[i].next == true)
				}
				schedule_lc = schedule_lc + get_sch_html(schedule_list[i_idx].id, schedule_list[i_idx].date, content_temp);
			}
		}	
		$("#parsed_data").html(schedule_lc);
	}
	function adjust_spacing(){
		sch_div = $(".schedule_elem[sid]");
		curr_sid = $(sch_div[0]).attr("sid")
		curr_pos = $(".date_label[sid='"+curr_sid+"']").position().top
		$(sch_div[0]).css("position", "absolute")
		$(sch_div[0]).css("top", curr_pos)
		for(i=1; i<sch_div.length; i++){
			curr_sid = $(sch_div[i]).attr("sid")
			curr_pos = $(".date_label[sid='"+curr_sid+"']").position().top
			prev_sid = $(sch_div[i-1]).attr("sid")
			prev_pos = $(".schedule_elem[sid='"+prev_sid+"']").position().top + $(".schedule_elem[sid='"+prev_sid+"']").height()
			$(sch_div[i]).css("position", "absolute")
			$(sch_div[i]).css("top", Math.max(curr_pos, prev_pos))
			//$(sch_div[i]).animate({top: Math.max(curr_pos, prev_pos)},"fast")
		}
	}
	$(window).scroll(function(){
		adjust_parsed_data_pos();
		/*   tool box   */
		//$("#tool_box").animate({top: $(window).scrollTop() +"px"},{ duration:100 , queue:false })
		$("#tool_box").css("top", $(window).scrollTop() +"px")
	});
	$("#undo").click(function (){
		if(history_stack.length >1)
		//alert(history_stack.length)
		history_stack.pop()
		schedule_temp = history_stack[history_stack.length-1]									
		schedule_list = $.extend(true, [], schedule_temp);
		show_schedule();
		adjust_spacing();
		adjust_parsed_data_pos();
		update_date_label();
	});
	function adjust_parsed_data_pos(){
		sch_div = $(".schedule_elem[sid]")
		date_label_span = $(".date_label[sid]")
		min_dist = 99999999;
		min_idx = ""
		scorll_val = 0
		for(i=0; i<sch_div.length; i++){
			temp_id = $(sch_div[i]).attr("sid");
			temp_dist = $(".date_label[sid='"+temp_id+"']").position().top - $(window).scrollTop() ;
			if (temp_dist>0  && (Math.abs(temp_dist) < min_dist) ){
				min_idx = temp_id
				scroll_val = $(".date_label[sid='"+temp_id+"']").position().top
				min_dist = Math.abs(temp_dist)
			}
		}
		if($(".schedule_elem[sid='"+min_idx+"']").position() != null){
			dist = - $(".schedule_elem[sid='"+min_idx+"']").position().top +  $(".date_label[sid='"+min_idx+"']").position().top// - $(".date_label[sid='"+min_idx+"']").position().top 
			$("div#parsed_data").animate({top: dist+"px"},{ duration:600 , queue:false })
		}
	}
	/************   main **************/
	init();
	result_g = "";
	var processorData = $('#processor-dorm').serialize();
	$.ajax({
		url: '?q=doc-process',
		type: 'Post',
		cache: false,
		data: processorData,
		success: function(response){	 
		result = response.content;
		result = $.trim(result) // result: raw syllabus
		result_g=result
		max_hit_number=0
		max_hit_idx=0
		for(m=0;m<reg.length;m++){
			hits = result_g.match(reg[m]);
			if(hits != null){
				if(hits.length>max_hit_number){
					max_hit_number = hits.length
					max_hit_idx = m
				}
			}
		}
		reg_idx = max_hit_idx;
		word=result.split(/\W+/)
		delim=result.split(/\w+/)	
		prev_idx=-1
		debug_info=""
		for(i=0;i<word.length-1;i++){									
			if (month.indexOf(word[i].toLowerCase().replace(/\W/gi,"")) != -1 || parseInt(word[i]) > 1900 || parseInt(word[i],10) > 0 && parseInt(word[i])< 32  ) {
				if (prev_idx==-1){ // if prev_idx not set, set it to current idx i
					prev_idx=i;
				}
			// non-date token
			} else {

					// consider token n-gram with n>1
					if (prev_idx != -1 && (i - prev_idx) >0 ) {
						date_temp = "";
						for (j=prev_idx; j<i-1; j++){
							date_temp = date_temp + word[j] + delim[j+1]
						}
						date_temp = date_temp + word[j];
						debug_info = debug_info + date_temp + "\n"
						ret = parse_date(date_temp);
						match_pos=ret[0]; match_str=ret[1]; str_before=ret[2]; str_after=ret[3];

					//not a valid date
					if(match_str == ""){  
						content += date_temp.replace(/(\n|\r)/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;");

					// Is a valid dat
					} else {
						sch_temp = sch(prev_idx, i, "");
						if (sch_temp !=null) {
							schedule_list.push(sch_temp);
							content += str_before +get_date_label_html(sch_temp.id, match_str) + str_after + delim[j+1].replace(/(\n|\r)/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
						}
					}
				}
				content += word[i] + delim[i+1].replace(/(\n|\r)/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;");
				prev_idx=-1;
			}
		}// finish processing raw syllabus
		$("#orig_syl").html(content.replace(/(\n|\r)/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;"));
		create_schedule();
		show_schedule();
		adjust_spacing();
		t=$.extend(true, [], schedule_list);
		history_stack.push(t);
		update_date_label();
	}}); //$.get("syl.txt", function(result)
}); // $document.ready
