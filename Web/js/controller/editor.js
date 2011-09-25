/**
 *  editor version 2.0
 *  automatically process documents, tag dates and assignments
 */
$P.ready(function(){

	// Interact with user when action is taken on /doc-editor
	// @see js/model/editor-flow.js
	var editorFlow = new EditorFlow('#task-creation-form');


	// Create a editorProcessor object
	// @see js/model/editor-processor.js
	var editorProcessor = new EditorProcessor('#processor-form');


	/*    global variables    */
  var schedule_list = [];
  var history_stack = [];
  var redo_stack = [];
  var history_pointer = 0;
  var syl_html = "";
  var syl_txt = "";
            
  var avg_inc = 0; //average increasement
  var min_var = -1;
  var reg_idx = -1;
  var auto_fill_start_idx = 0; 
                
  var curr_date = new Date();
  var curr_year = curr_date.getFullYear();
  var curr_year = 2000;
	/**
	 * Date patterns
	 */
	var reg = [
		// mm/dd/yy
		/((0?[1-9])|(1[0-2])){1}\/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}(\/(\d{4}|\d{2}))?(?=[^\/0-9])/gi,
		// mm-dd-yy or mm-dd
		/((0?[1-9])|(1[0-2])){1}-((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}(-(\d{4}|\d{2}))?(?=[^\/0-9])/gi,
		/(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep[t]?|oct|nov|dec){1}( *|,|.){0,2}((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}(?=[^0-9])(\W+\d{4})?/gi,
		/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}-(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep[t]?|oct|nov|dec){1}(\W+\d{4})?/gi,
		/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1} (january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep[t]?|oct|nov|dec){1}(\W+\d{4})?/gi,
		/^[^0-9](0?[1-9]|1[0-2]){1}-(1[0-9]|2[0-9]|3[0-1]|0?[1-9]){1}(?=[^0-9])/gi,
		/week\W{0,2}\d{1,2}/gi
	];

  var fil_list = [/mon/gi, /tue/gi, /wed/gi, /thu/gi, /thur/gi, /fri/gi, /sat/gi, /sun/gi,/week/gi, /m/gi , /t/gi, /w/gi, /r/gi, /f/gi];
            
            
  var valid_month_min= 0;
  var valid_month_max = 11;
  //var valid_month_min= 3;
  //var valid_month_max = 8;

	var schedule_list_orig_len = 0
	var orig_syl_content = ""
  var orig_html = "";
	var pre_text = ""; // text before real date

  /**
   *  create schedule element, need to be pushed into schedule_list
   */
  function sch(start_pos, next_start_pos, match_str){
		// assignment created by user
		if(start_pos ==  -1 && next_start_pos == -1) { 
			content = "New assignment"
      id_temp = Math.random()
      date_label_deleted = false

		// assignments automatcally created by script
    } else {
			id_temp=start_pos
      content = syl_txt.slice(start_pos+match_str.length, next_start_pos)
      if (content.lastIndexOf('\n') !=-1 && content.search(/[a-z]/i) !=-1){
				last_oc_idx = content.lastIndexOf('\n')
        first_oc_idx =  content.search(/[a-z]/i)
        last_line = $.trim(content.slice(last_oc_idx, content.length))
        hit_flag = 0
        for (j = 0; j < fil_list.length; j++) {
					if (last_line.match(fil_list[j]) != null && last_line.length < 12) { 
						hit_flag = 1; break;
					}
        }

        if (last_oc_idx > first_oc_idx && hit_flag == 1) {
					content = content.slice(first_oc_idx, last_oc_idx)
        }
      }

      if ($.trim(content).length < 2 || $.trim(content).length > 1500) {
				return null
			}

			date_label_deleted = (content.length < 4);
                    
      content = $.trim(content.replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;"))
    } 

    if (reg_idx == 6) {
			match_str = match_str.replace(/\W/gi, " ")    
    }

    if (parse_date(match_str) == true) { 
			date_out = Date.parse(match_str);
		}

    var temp_sch = { 'id' : id_temp, 'start_pos' : start_pos, 'end_pos': next_start_pos, 'match_str' : match_str,'content': content , 'modified': 0, 'catergory': '', 'date': date_out, 'next': false, 'deleted': false, 'date_label_deleted': date_label_deleted, 'content_being_edited': false, 'date_being_edited': false};

    return temp_sch;

  };
            
  function save_history(){
		t=$.extend(true, [], schedule_list);
    history_stack.push(t)
  }

  /**
   *  get schedule element idx by sid
   */
  function get_sch_idx_by_id(sid){
		for(i = 0; i < schedule_list.length; i++){
			if(schedule_list[i].id == sid) return i;		
    }
  }
            
  function get_sch_by_id(sid){
		for(i=0;i<schedule_list.length;i++){
			if(schedule_list[i].id==sid) return schedule_list[i];		
    }
  }
            
  /**
	 *  html elements e.g. span, tables for dynamically loading
	 */
  function get_date_label_html(sid_str, date_content_str){
		return '<span class="date_label" sid="' + sid_str + '">' + 
			'<span>' + date_content_str + '</span>' +
			'<a href="#" sid="' + sid_str+ '" class="date_label btn del_date_label" >x</a>' +
		'</span>';
  }

	/**
	 * Generate HTML ouput for each tagged task
	 */
  function get_task_html(sid_str, date_str, content_str){
		return '<div class="task" sid="' + sid_str + '">' +
			'<h3 class="task_date" sid="' + sid_str + '">' + date_str + '</h3>' +
				'<div class="options">' +
					'<a href="#" class="button edit_task" sid="' + sid_str + '" >edit</a>' +
          '<a href="#" class="button remove_task" sid="' + sid_str + '">remove</a>' +
        '</div>' +
				'<p class="task_content" sid="' + sid_str + '">' + content_str + '</p>' +
     '</div>';
  }

	/**
	 * Generate HTML fieldt for editting task content
	 */
  function get_task_content_html_editing(sid_str, content_str) {
		return '<textarea sid="' + sid_str  + '" class="task_content_editing">' + content_str + '</textarea>' +
			'<a href="#" sid="' + sid + '" class="button save_task">save</a>' + 
			'<a href="#" sid="' + sid + '" class="button cancel_task">cancel</a>';
  }

	/**
	 * Generate HTML fieldt for editting task date
	 */
  function get_task_date_html_editing(sid_str,  date_str){
		return "<input type='text' class='task_date_editing' sid='" + sid_str + "' size=10 value='" + date_str + "'>";
  }

  /*       flags            */
  var editing_flag = 0;
  var flag_week_format = 0;
  var flag_delete_all = 0
  var debug_flag = 0;

  function debug(info){
		if(debug_flag == 1){
			console.log(info);
		}
  }

  /**
   * filter out dates previous to current year
   */
  function parse_date(str){
		var date_out = Date.parse(str);

		if(str.search(/week/gi) != -1){
			return true
		}
		if (date_out == null) {
			return false;
		}

		var year  = date_out.getFullYear();
		var month = date_out.getMonth();
		if(year < curr_year || ( month < valid_month_min || month > valid_month_max ) ){
			return false;
		}

		return true;
	}

	function sort_function(a, b){
		return a - b;
  }


  /**
   *  find median from a list of numbers
   */
  function get_median(val_list){ 
		val_list.sort(sort_function);
    median_position = Math.floor(val_list.length/2);
    median = val_list[median_position];
    return median
  }


  /**
   *  find variance of a number list
   */
  function get_variance(val_list){
		var mean = 0
    var variance = 0
    for(i=0;i<val_list.length;i++){
			mean += val_list[i];
    }

    mean = mean / val_list.length;
                    
    for(i=0;i<val_list.length;i++){
			variance += (val_list[i]-mean) * (val_list[i]-mean);
    }

    variance = variance/val_list.length;
    return variance
  }
            
  function get_reg_idx(result){
		var max_hit_number = 0;
    var max_hit_idx    = 0;
    var min_var        = 999999999;
    var best_guess_idx = -1
		var hits           = null;

    for (m = 0; m < reg.length; m++) { // loop thru all regexp
			hits = syl_txt.match(reg[m]);
      if (hits != null) {
				filtered_hits=[];
        for (i=0;i<hits.length;i++) {
					if(parse_date(hits[i]) == true){
						filtered_hits.push(hits[i])
          }
        }
        inc_val = [];

        for (n=0;n<filtered_hits.length-2;n++) {
					if (Date.parse(filtered_hits[n+1]) == null || Date.parse(filtered_hits[n]) == null) {
						continue ;
					}
          inc_val.push((Date.parse(filtered_hits[n+1]).getOrdinalNumber()-Date.parse(filtered_hits[n]).getOrdinalNumber()));
        }

				// min number of dates allowed
        if (get_variance(inc_val) < 1 && inc_val.length > 10) {
					best_guess_idx = m;
        }

				// min number of dates allowed
        if (get_variance(inc_val) < min_var && inc_val.length > 2) {
					min_var = get_variance(inc_val);
          reg_idx = m;
          avg_inc = get_median(inc_val);
        }
      }

      debug(min_var);

			if (best_guess_idx != -1) {
				reg_idx = best_guess_idx;
			}
			if (reg_idx == 6) {
				flag_week_format = 1;
			}
		}
		return reg_idx;
  }

	/**
	 * Convert text to html
	 */
  function txt2html(txt){
		return txt.replace(/\n/gi, "<br>").replace(/\s/gi, "&nbsp;");
  }

	/**
	 * Convert html to text
	 */
  function html2txt(html){
		return html.replace(/<br>/gi, "\n").replace(/&nbsp;/gi, " ");
  }

  function parse_text_by_reg(idx){
		var text      = null;
    var match_idx = -1;
    var match_str = "";
    var search_start_idx = 0;
    var search_end_idx = syl_txt.length;
		var candidate_list = []; // elem: ["May 22", 1723] 

    do {
      text = syl_txt.slice(search_start_idx, search_end_idx); 
      match_idx = text.search(reg[idx]);
      if (match_idx == -1) {
				break;
			}
      match_str = text.match(reg[idx])[0];
      if(parse_date(match_str) == true){
				candidate_list.push([search_start_idx + match_idx, match_str])
      }
			search_start_idx += match_idx + match_str.length;
    } while (match_idx != -1);

		for (i = 0; i < candidate_list.length; i++) {
			if(i < candidate_list.length -1) {
				sch_temp = sch(candidate_list[i][0], candidate_list[i+1][0], candidate_list[i][1])
			} else { //last elem
				sch_temp = sch(candidate_list[i][0], syl_txt.length, candidate_list[i][1])
			}
      if(sch_temp !=null){
				schedule_list.push(sch_temp)
			} 
		}
  }
            
	function refine_schedule_list(){
		var start_pos = 0;
    var end_pos   = 0;
    var start_pos_final = 0;
    var end_pos_final   = 0;
    var max_len = 0;
		var curr_day = null;
		var next_day = null;
    for (i=0;i<schedule_list.length-1;i++) {
			/** 
			 * some suckers will write their syls as " Quizzes: EVERY Thursday: 1/25, 2/1, 
			 * 2/8, 2/15, 3/1, 3/8, 3/22, 4/5, 4/12, 4/19 ", which will total screw the 
			 * smoothing process.
			 */
			if (schedule_list[i].content.length < 3) {
				continue 
      }

			curr_day = schedule_list[i].date.getOrdinalNumber();
			next_day = schedule_list[i+1].date.getOrdinalNumber();
                            
      if( (next_day-curr_day) > 0 && (next_day-curr_day) < avg_inc*4 ){
				end_pos = i+1;
      } else {
        if ((end_pos-start_pos) > max_len) {
          max_len = end_pos-start_pos
          start_pos_final = start_pos
					end_pos_final = end_pos
        }
        start_pos = i+1;
				end_pos = i+2;
			}
    }
		if( (end_pos-start_pos) > max_len ) {
			max_len = end_pos-start_pos; 
			start_pos_final = start_pos; 
			end_pos_final = end_pos;
		}
    /* filtration */ 
    for (var i = end_pos_final; i < schedule_list.length-1; i++) {
      for (var j = i + 1; j < schedule_list.length; j++) {
        start_day = schedule_list[i].date.getOrdinalNumber()
        curr_day = schedule_list[j].date.getOrdinalNumber()
        if ((curr_day-start_day) >= 0 && (curr_day-start_day) < 15) {
          i = j;
					continue
        } else {
          schedule_list[j].date_label_deleted = true
					schedule_list[j-1].next = true
				}
			}
    }
    for (var i = start_pos_final; i > 0; i--) {
      for (j=i-1; j>=0; j--) {
        start_day = schedule_list[i].date.getOrdinalNumber();
        curr_day = schedule_list[j].date.getOrdinalNumber();
        if ((start_day-curr_day) >= 0 && (start_day-curr_day) < 15 ) {
          i = j;
					continue;
        } else {
          schedule_list[j].date_label_deleted = true
					if (j>0) {schedule_list[j-1].next = true}
				}
			}
		}
  }
	/**
	 * Display list of processed tasks
	 */
  function show_task(){
     task_list_html = "";
     i_idx = 0;
     for(i = 0; i < schedule_list.length; i++){
       i_idx = i;
			 content_temp = schedule_list[i].content;
     if(schedule_list[i].next==true){
       do {
         if (i < schedule_list.length-1) {
             content_temp += schedule_list[i+1].match_str + schedule_list[i+1].content;
					 i++;
				 }
			 } while (schedule_list[i].next == true) ;
     }
     var month = schedule_list[i_idx].date.getMonth() + 1; // month numbers are 0-11
     var day = schedule_list[i_idx].date.getDate();
     var date_str = month + "/" + day;
		 task_list_html += get_task_html(schedule_list[i_idx].id, date_str, content_temp);
   }
	 $(".task-wrap").html(task_list_html);
 }

 /**
  *  update opacity info of schedule_elem based on 'deleted'
  */ 
 function update_task(){ 
   for (i = 0; i < schedule_list.length; i++) {
		 var sid = schedule_list[i].id
     if( schedule_list[i].deleted == true ){
       $(".task[sid='" + sid + "']").fadeTo("fast", 0.2);
			 $(".remove_task[sid='" + sid + "']").text("add");
     } else {
       $(".task[sid='" + sid + "']").fadeTo("fast", 1);
			 $(".remove_task[sid='" + sid + "']").text("remove");
		 }
	 } 
 }
            
 function update_date_label(){
    for (i = 0; i < schedule_list.length; i++) {
      if(schedule_list[i].date_label_deleted == false){
        $(".del_date_label[sid='" + schedule_list[i].id + "']").html("X")
        $(".del_date_label[sid='" + schedule_list[i].id + "']").fadeTo("fast", 1)
				$(".date_label[sid='" + schedule_list[i].id + "']").fadeTo("fast", 1)
      } else {
        $(".del_date_label[sid='" + schedule_list[i].id + "']").html("O")
        $(".del_date_label[sid='" + schedule_list[i].id + "']").fadeTo("fast", 0.2)
				$(".date_label[sid='" + schedule_list[i].id + "']").fadeTo("fast", 0.2)
			}
		}
  }

  /**
   *  adjust vertical position of #parsed_data
	function adjust_parsed_data_pos(){
		var sch_div = $(".task[sid]")
		var date_label_span = $(".date_label[sid]")
		var min_dist = 99999999
		var min_idx = ""
		var offset = -160;
		debug($(window).scrollTop())
		for (i=0; i < schedule_list.length; i++) {
			temp_id = $(sch_div[i]).attr("sid");
			if( $(".date_label[sid='"+temp_id+"']").position() == null){
				break;
			}
			temp_dist = $(".date_label[sid='"+temp_id+"']").offset().top - $(window).scrollTop() ;
                            
			if (temp_dist>0  && (Math.abs(temp_dist) < min_dist)) {
        min_idx = temp_id;
				min_dist = Math.abs(temp_dist);
			}
    }

    if($(".task[sid='"+min_idx+"']").offset() != null){
      dist =  offset + $(".date_label[sid='"+min_idx+"']").offset().top - $(".task[sid='" + min_idx+ "']").position().top
      debug("date_label")
      debug($(".date_label[sid='"+min_idx+"']").offset().top)
      debug("task")
      debug($(".task[sid='" + min_idx+ "']").position().top)
			$(".task-wrap").animate({'top': dist+"px"},{ duration:600 , queue:false })
		}
  }
             
  $(window).scroll(function(){
		adjust_parsed_data_pos();
  });
   */

  $(".task").live("mouseenter", function(e){
    var sid = $(this).attr("sid")
    $(".date_label[sid='" + sid + "']").addClass("highlight")
		$(".orig_text_block[sid='" + sid + "']").addClass("highlight")
  });
            
  $(".task").live("mouseleave", function(e){
    var sid = $(this).attr("sid")
    $(".date_label[sid='" + sid + "']").removeClass("highlight")
		$(".orig_text_block[sid='" + sid + "']").removeClass("highlight")
  });

  $(".edit_task").live("click", function(e){
    e.preventDefault();
    sid = $(this).attr("sid")
    var idx = get_sch_idx_by_id(sid);
    var orig_task =html2txt(schedule_list[idx].content)
    $(".task_content[sid='" + sid + "']").html(get_task_content_html_editing(sid, orig_task));
    curr_task = get_sch_by_id(sid);
    orig_date = (curr_task.date.getMonth() + 1) + "/" +(curr_task.date.getDate());
		$(".task_date[sid='" + sid + "']").html(get_task_date_html_editing(sid, orig_date));
  });

  /**
   *  save schedule
   */
	$(".save_task").live("click",function(e){
     e.preventDefault();
     var sid = $(this).attr("sid")
     var task_idx = get_sch_idx_by_id(sid)
     var new_date = $(".task_date_editing[sid='" + sid + "']").val() 
      if(Date.parse(new_date) == null){
       alert("invalid date");
			 return ;
     } else { // if valid date
      schedule_list[task_idx].date = Date.parse(new_date);
      new_date_str = (schedule_list[task_idx].date.getMonth() + 1) + "/" + schedule_list[task_idx].date.getDate();
      $(".task_date[sid='" + sid  + "']").html(new_date_str);
      /* saving schedule content*/
      content_temp = txt2html($(".task_content_editing[sid='" + sid + "']").val());
      $(".task_content[sid='"+$(this).attr("sid")+"']").html(content_temp);
			schedule_list[task_idx].content = content_temp;
    }
		save_history();
  });
        
  $(".cancel_task").live("click",function(e){
    e.preventDefault();
    var sid = $(this).attr("sid")
    idx = get_sch_idx_by_id(sid)
    orig_content = txt2html(schedule_list[idx].content);
    orig_date = (schedule_list[idx].date.getMonth() + 1) + "/" + schedule_list[idx].date.getDate()
    $(".task_date[sid='" + sid + "']").html(orig_date)
		$(".task_content[sid='" + sid + "']").html(orig_content)
  });  
            
  /**
   *  delete schedule_elem (toggle)
   */
  $(".remove_task").live("click",function(e){
    e.preventDefault();
    sid = $(this).attr("sid")
    sch_idx = get_sch_idx_by_id(sid)
    schedule_list[sch_idx].deleted = !schedule_list[sch_idx].deleted
    update_task();                   
		save_history();
  });
            

	/**
   * toggle date lable
   */
  $(".del_date_label").live("click",function(e){
    e.preventDefault();
    var sid = $(this).attr("sid")
    var idx =get_sch_idx_by_id(sid) 
    schedule_list[idx].date_label_deleted = !(schedule_list[idx].date_label_deleted);
    if (idx>0){
			schedule_list[idx-1].next = schedule_list[idx].date_label_deleted
    }
    save_history();
    show_task();
    update_date_label();
		update_task();
  });
            

	/**
	 * Bind editor actions to menu
	 *
	 * list of actions:
	 *  - undo
	 *  - redo
	 *  - add
	 *  - submit
	 */
	$('#editor-action-menu').delegate('a.button', 'click', function(e) {
		e.preventDefault();
		var target = $(this);

		/**
		 * Undo editor action
		 */
		if (target.hasClass('undo')) {
			if(history_stack.length >1){
				redo_stack.push(history_stack.pop())
			}
			var schedule_temp = history_stack[history_stack.length-1];
			schedule_list = $.extend(true, [], schedule_temp);
			show_task();
			// adjust_parsed_data_pos();
			update_date_label();
			update_task();

		/**
		 * Redo editor action
		 */
		} else if (target.hasClass('redo')) {
			if(redo_stack.length>0){
				history_stack.push(redo_stack.pop())
			}
			var schedule_temp = history_stack[history_stack.length-1]									
			schedule_list = $.extend(true, [], schedule_temp);
			show_task();
			// adjust_parsed_data_pos();
			update_date_label();
			update_task();                   

		/**
		 * Create task manually
		 */        
		} else if (target.hasClass('add')) {
			$(".task-wrap").html("");
			s = sch(-1,-1,"1/1"); 
			schedule_list.push(s);
			sid = s.id;
			/* replace schedule content with textarea*/;
			$(".task_content[sid='" + sid + "']").html(get_task_content_html_editing(sid, "new assignment"));
			/* replace date with textbox*/
			$(".task_date[sid='" + sid + "']").html(get_task_date_html_editing(sid, "1/1"));
			show_task();
			update_task();
			$(".task-wrap").animate({'top': "0px"},{ duration:600 , queue:false });
			page_end_pos = $(".task[sid='" + sid + "']").position().top;
			// adjust_parsed_data_pos();
			$("html,body").animate({ scrollTop:  page_end_pos}, 600);       
			save_history();

		/**
		 * Submit schedule
		 */
		} else if (target.hasClass('submit')) {
			if(flag_week_format == 1){
				// dialog.open("alert_box", "please enter the date of your first assignment")
				// return
			}

			editorFlow.promptTaskCreation(schedule_list);
		}
	});

	// fetch processed result from server
	editorProcessor.processResult(function(response) {

		// begin editor flow by asking user to confirm class info
		editorFlow.promptClassConfirmation();
		syl_txt  = response.content.replace(/PAGE_BREAK/gi, '');
		syl_html = txt2html(response.content);
             
		reg_idx = get_reg_idx(syl_txt);
                            
		parse_text_by_reg(reg_idx); // generate initial schedule_list 
                                                        
		refine_schedule_list();
                            
		pre_text = syl_txt.slice(0, schedule_list[0].start_pos)
                            
		// paginate content
		orig_syl_content += "<div class='content'>" +
			syl_html.replace(/PAGE_BREAK/gi, '</div><div class="content">') +
		'</div>';

		// tag task found in the original document
		var match   = null;
		var replace = null;
		for (var i = 0; i < schedule_list.length; i++) {
			match   = schedule_list[i].match_str;
			replace = get_date_label_html(schedule_list[i].id, schedule_list[i].match_str);
			orig_syl_content = orig_syl_content.replace(match, replace);
		}

		$('.content-wrap').html(orig_syl_content);


		show_task(); 
		update_date_label();
		update_task();
		save_history();

		// auto scroll to first task
		var offset = $('.date_label:first').offset().top - $('.task-wrap').offset().top;
		$('.task:first').css({marginTop : offset});
		$('body').animate({scrollTop: offset}, 1000);
  });


});
