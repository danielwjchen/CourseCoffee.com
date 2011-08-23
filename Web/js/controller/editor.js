/**
 * @file
 * Automatically tags assignments in a document based on occurances of dates. 
 * It also provides an interactive editor to correct the result.
 *
 * On submit, the editor asks for confirmation of the information gathered,
 * and the process flow takes two different route based on where the user comes
 * from.
 */ 
$P.ready(function(){
            /*    global variables    */
            history_stack = [];
            redo_stack = [];
            history_pointer = 0;
            result_g = "";
            avg = 0 //average increasement
            min_var = -1
            reg_idx = -1;
            auto_fill_start_idx = 0 
                
            pre_text_orig = "";
            pre_text_trunc = "";
            flag_show_orig_pre_text = 0

            curr_date = new Date();
            curr_year = curr_date.getFullYear();
            curr_year = 2000
            month=["january","february","march","april","may","june","july","august","september","october","november","december","jan","feb","mar",  "apr",  "may","jun", "jul", "aug","sep","sept","oct",       "nov",     "dec","week"];
            reg=[]
            reg[0]=/((0?[1-9])|(1[0-2])){1}\/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}(\/(\d{4}|\d{2}))?(?=[^\/0-9])/gi;	// mm/dd/yy
            //reg[1]=/(0?[1-9]|1[0-2]){1}\/(1[0-9]|2[0-9]|3[0-1]|0?[1-9]){1}(?=[^\/0-9])/g;	// mm/dd(/2011 2012)
            reg[2]=/(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}( *|,|.){0,2}((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}(?=[^0-9])(\W+\d{4})?/gi
            reg[3]=/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}-(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}(\W+\d{4})?/gi
            reg[4]=/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1} (january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}(\W+\d{4})?/gi
            reg[5]=/^[^0-9](0?[1-9]|1[0-2]){1}-(1[0-9]|2[0-9]|3[0-1]|0?[1-9]){1}(?=[^0-9])/gi
            reg[6]=/week\W{0,2}\d{1,2}/gi
            reg[1]=/((0?[1-9])|(1[0-2])){1}-((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}(-(\d{4}|\d{2}))?(?=[^\/0-9])/gi;	// mm-dd-yy or mm-dd

            fil_list = [/mon/gi, /tue/gi, /wed/gi, /thu/gi, /thur/gi, /fri/gi, /sat/gi, /sun/gi,/week/gi, /m/gi , /t/gi, /w/gi, /r/gi, /f/gi]
            
            
            valid_month_min= 0;
            valid_month_max = 11;
            //valid_month_min= 3;
            //valid_month_max = 8;
     
            schedule_list = []
            schedule_list_orig_len = 0
            orig_syl_content = ""
            orig_html = "";
	    pre_text = ""; // text before real date

            
            /* html elements e.g. span, tables for dynamically loading*/
            date_label_string =  "<br><span class='date_label' sid='SID_STR'><span>DATE_CONTENT_STR</span><a href='#' sid='SID_STR' class='del_date_label' >x</a></span>" ;
            schedule_elem_string = "<div sid='SID_STR' class='schedule_elem'>\
                                    <div class='hint'></div>\
                                    <table width='450' border='0' cellpadding='0' cellspacing='0'>\
                                    <tr>\
                                        <td width='350'><span class='schedule_elem_date' sid='SID_STR'>DATE_STR</span></td>\
                                        <td width='50' id='editing_btn'></td>\
                                        <td width='50'><a href='#' class='sch_btn toggle_del_sch' sid='SID_STR'>x</a></td>\
                                    </tr>\
                                    <tr>\
                                        <td colspan='3'><div class='sch_content' sid='SID_STR' >CONTENT_STR</div></td>\
                                    </tr>\
                                    </table></div><div class='schedule_elem_space'></div>";
            schedule_edit_string = "<textarea sid='SID_STR' class='schedule_elem_edit'>CONTENT_STR</textarea>\
                                    <input type='button' id='save_sch' sid='SID_STR' value='save'>\
                                    <input type='button' id='cancel_sch' sid='SID_STR' value='cancel'>"           
            
            info_box_left_string = "<div id='info_box_left'></span>"        
            info_box_right_string = "<div id='info_box_right'></span>"        
            date_string = "<input type='text' class='date_container' sid='SID_STR' size=10 value='ORIG_DATE_STR'>"
            /*       flags            */
            editing_flag = 0;
            flag_week_format = 0;
            flag_delete_all = 0
            /*    data stuctures      */
            
            /**
             *  create schedule element, need to be pushed into schedule_list
             */
            function sch(start_pos, next_start_pos, match_str){
                    if(start_pos ==  -1 && next_start_pos == -1){ // assignment created by user
                        content = "New assignment"
                        id_temp = Math.random()
                        date_label_deleted = false
                    }
                    else{ // auto assignment
                            id_temp=start_pos
                            content = result_g.slice(start_pos+match_str.length, next_start_pos)
                            if(content.lastIndexOf('\n') !=-1 && content.search(/[a-z]/i) !=-1){
                                last_oc_idx = content.lastIndexOf('\n')
                                first_oc_idx =  content.search(/[a-z]/i)
                                last_line = $.trim(content.slice(last_oc_idx, content.length))
                                
                                hit_flag = 0
                                for (j = 0; j < fil_list.length; j++){
                                        if(last_line.match(fil_list[j]) != null && last_line.length < 12) { hit_flag = 1; break; }
                                }
                                
                                if(last_oc_idx > first_oc_idx && hit_flag == 1){
                                        content = content.slice(first_oc_idx, last_oc_idx)
                                }
                            }
                            
                            if($.trim(content).length < 2)
                                return null

                            if(content.length < 4){
                                        date_label_deleted=true
                            }
                            else{
                                        date_label_deleted=false	
                            }
                    
                            content = $.trim(content.replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;"))
                    } 

                    if(reg_idx == 6){
                            match_str = match_str.replace(/\W/gi, " ")    
                    }
                    if(parse_date(match_str) == true){ date_out = Date.parse(match_str) }
                    var temp_sch = { 'id' : id_temp, 'start_pos' : start_pos, 'end_pos': next_start_pos, 'match_str' : match_str,'content': content , 'modified': 0, 'catergory': '', 'date': date_out, 'next': false, 'deleted': false, 'date_label_deleted': date_label_deleted, 'content_being_edited': false, 'date_being_edited': false};
                    return temp_sch;

            };

            /* mouse  */

            $("#tool_box").live("mouseenter",function(){
                    $(this).fadeTo("slow", 1.0)
            });
            
            $("#tool_box").live("mouseleave",function(){
                    $(this).fadeTo("fast", 0.9)
            });

            /**
             *  alert message
             */
            
            $(".dialog-close").live("click", function(e){
                dialog.close('alert_box')
            });

            /**
             * toggle date lable
             */
            $(".del_date_label").live("click",function(e){
                    e.preventDefault();
                    id_temp = $(this).attr("sid")
                    for(i=0; i<schedule_list.length;i++){
                            if(schedule_list[i].id==id_temp) break;
                    }
                    
                    schedule_list[i].date_label_deleted = !(schedule_list[i].date_label_deleted);
                    
                    if (i>0){
                            schedule_list[i-1].next = schedule_list[i].date_label_deleted
                    }
                    
                    save_history();
                    show_schedule();
                    update_date_label();
                    update_schedule();
            });
            

            /**
             *  indicate which schedule_elem is being pointed 
             */
            $(".date_label").live("mouseenter",function(){
                    sid = $(this).attr("sid")
                    this_height =  $(this).position().top

                    $(".schedule_elem[sid='"+sid+"']").css("background-color","#99CCFF");                   
		    
		    idx = get_sch_idx_by_id(sid);		    
		    if(idx > 0){
			sid_prev = schedule_list[idx-1].id
		        $(".orig_text_block[sid='" + sid + "']").css("background-color", "#DDDDDD")
		        $(".orig_text_block[sid='" + sid_prev + "']").css("background-color", "#DDDDDD")

		    }		    

                    $("#info_box_left").css("top", this_height)
                    $("#info_box_left").text("Click X to merge up")
		    
                    $("#info_box_left").show()
                    $("#info_box_left").fadeTo("slow", 0.8)    
            });

            $(".date_label").live("mouseleave",function(){
                    sid = $(this).attr("sid")
                    idx = get_sch_idx_by_id(sid);		    
		    if(idx > 1){
			sid_prev = schedule_list[idx-1].id
		        $(".orig_text_block[sid='" + sid + "']").css("background-color", "")
		        $(".orig_text_block[sid='" + sid_prev + "']").css("background-color", "")

		    }

                    $(".schedule_elem[sid='"+sid+"']").css("background-color","");									 
                    $("#info_box_left").fadeOut()
            });


            /**
             *  edit schedule content
             */
            $(".edit_sch").live("click",function(e){
                    e.preventDefault();
                    
                    /* replace schedule content with textarea*/ 
                    sid=$(this).attr("sid")
                    idx = get_sch_idx_by_id(sid)
                    orig_html = schedule_list[idx].content.replace(/<br>/gi,"\n").replace(/&nbsp;/g, " ")

                    $(".sch_content[sid='" + sid + "']").html(schedule_edit_string.replace(/SID_STR/gi, sid).replace(/CONTENT_STR/gi, orig_html))
                    
                    /* replace date with textbox*/
                    curr_sch = get_sch_by_id(sid)
                    orig_date = (curr_sch.date.getMonth() + 1) + "/" +(curr_sch.date.getDate())
                    $(".schedule_elem_date[sid='" + sid + "']").html(date_string.replace(/SID_STR/gi, sid).replace(/ORIG_DATE_STR/gi, orig_date))

                    editing_flag=1
            });


            /**
             *  delete schedule_elem (toggle)
             */
            $(".toggle_del_sch").live("click",function(e){
                    e.preventDefault();
                    sid = $(this).attr("sid")
                    sch_idx = get_sch_idx_by_id(sid)
                    schedule_list[sch_idx].deleted = !schedule_list[sch_idx].deleted
                    update_schedule();                   
                    save_history()
            });

            $(".toggle_del_sch").live("mouseenter",function(e){
                    this_height =  $(this).position().top
                    $("#info_box_right").css("top", this_height)
                    $("#info_box_right").text("Click X tasdffado merge up")
		    
                    $("#info_box_right").show()
                    $("#info_box_right").fadeTo("slow", 0.8)    

            });

            $(".toggle_del_sch").live("mouseleave",function(e){
                    $("#info_box_right").fadeOut()
            });

            /**
             *  showing edit buttons when mouse entered schedule_elem
             */

            $(".schedule_elem").live("mouseenter",function(){
                    id_temp = $(this).attr("sid")
                    $("td[id]", this).html("<a href='#' class='sch_btn_2 edit_sch'  sid='"+id_temp+"'>edit</a>")
                    adjust_parsed_data_pos();
            });


            $(".schedule_elem").live("mouseleave",function(){					
                    $("td[id]", this).html("")
                    adjust_parsed_data_pos();
            });

            /**
             *  save schedule
             */

            $("#save_sch").live("click",function(){
                    sid = $(this).attr("sid")
                    
                    sch_idx = get_sch_idx_by_id(sid)
                    orig_first_day = schedule_list[auto_fill_start_idx].date.getOrdinalNumber() 
                    new_date = $(".date_container[sid='" + sid + "']").val() 
                    if(Date.parse(new_date) == null){
                            dialog.open("alert_box", "invalid date")
                            return
                    }
                    else{ // if valid date
                            schedule_list[sch_idx].date = Date.parse(new_date)
                            if(sch_idx == auto_fill_start_idx  && flag_week_format == 1){ // auto fill
                                    day_offest = schedule_list[sch_idx].date.getOrdinalNumber() - orig_first_day
                                    for(i = 0; i < schedule_list_orig_len; i++ ){
                                            if( i == auto_fill_start_idx ) continue
                                            schedule_list[i].date.addDays(day_offest)
                                    }
                                    
                                    flag_week_format = 0
                            }
                            new_date_str = (schedule_list[sch_idx].date.getMonth() + 1) + "/" + schedule_list[sch_idx].date.getDate()
                            $(".schedule_elem_date[sid='" + sid  + "']").html(new_date_str)
                            
                            /* saving schedule content*/
                            console.log(sid)
                            content_temp = $(".schedule_elem_edit[sid='" + sid + "']").val().replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;")
                            $(".sch_content[sid='"+$(this).attr("sid")+"']").html(content_temp);
                            for(i=0;i<schedule_list.length;i++){
                                    if(schedule_list[i].id == sid){
                                            schedule_list[i].content = content_temp
                                            break;
                                    }
                            }
                            editing_flag=0


                    }
                    show_schedule()
                    update_schedule()
                    save_history()
            });


            /**
             *  cancel editing schedule
             */

            $("#cancel_sch").live("click",function(){
                    sid = $(this).attr("sid")
                    sch_idx = get_sch_idx_by_id(sid)
                    $(".sch_content[sid='" + sid + "']").html(schedule_list[sch_idx].content);
                    
                    new_date_str = (schedule_list[sch_idx].date.getMonth() + 1) + "/" + schedule_list[sch_idx].date.getDate()
                    $(".schedule_elem_date[sid='" + sid  + "']").html(new_date_str)

                    editing_flag=0
            });

            /**
             *  undo, get back to the previous schedule_list elem
             */

            $("#undo").click(function (e){
                    e.preventDefault();
                    if(flag_delete_all == 1){
                            $("#orig_syl").html(orig_syl_content.replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;"))
                            flag_delete_all = 0
                    }

                    if(history_stack.length >1){
                            redo_stack.push(history_stack.pop())
                    }        

                    schedule_temp = history_stack[history_stack.length-1]									
                    schedule_list = $.extend(true, [], schedule_temp);
                    show_schedule();
                    adjust_parsed_data_pos();
                    update_date_label();
                    update_schedule();                   
                    editing_flag = 0 
            });

            /**
             * redo
             */
            $("#redo").click(function(e){
                    e.preventDefault();
                    if(redo_stack.length>0){
                            history_stack.push(redo_stack.pop())
                    }
                    schedule_temp = history_stack[history_stack.length-1]									
                    schedule_list = $.extend(true, [], schedule_temp);
                    show_schedule();
                    adjust_parsed_data_pos();
                    update_date_label();
                    update_schedule();                   
                    editing_flag = 0
            });
	
            /**
             *  add new assignment
             */        
            $("#new_assignment").click(function(e){
                    e.preventDefault();
                    $("#parsed_data").html("")
                    s = sch(-1,-1,"1/1") 
                    schedule_list.push(s)
                    sid = s.id
                    show_schedule()
                    sch_top = $(".schedule_elem[sid='" + sid  + "']").position().top
                    /* replace schedule content with textarea*/ 
                    orig_html = $(".sch_content[sid='" + sid + "']").html().replace(/<br>/gi,"\n").replace(/&nbsp;/g, " ")
                    $(".sch_content[sid='" + sid + "']").html(schedule_edit_string.replace(/SID_STR/gi, sid).replace(/CONTENT_STR/gi, orig_html))
                    
                    /* replace date with textbox*/
                    curr_sch = get_sch_by_id(sid)
                    orig_date = (curr_sch.date.getMonth() + 1) + "/" +(curr_sch.date.getDate())
                    $(".schedule_elem_date[sid='" + sid + "']").html(date_string.replace(/SID_STR/gi, sid).replace(/ORIG_DATE_STR/gi, orig_date))


                    
                    adjust_parsed_data_pos()
                    $("html,body").animate({ scrollTop:  sch_top}, 600);       
            });

            /**
             *  show/hide text at the front with no dates
             */
            $("#toggle_pre_text").live("click", function(){
                    if (flag_show_orig_pre_text == 0){ // currently showing truncated text
                        $("#pre_text").html(pre_text_orig.replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;"))
                        $("#pre_text").fadeTo("fast", 1);
                        $(this).text("hide text")
                    }
                    else{
                        $("#pre_text").html(pre_text_trunc.replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;"))
                        $("#pre_text").fadeTo("fast", 0.2);
                        $(this).text("show text")
                    }
                    flag_show_orig_pre_text = !flag_show_orig_pre_text ;

            });
            


            /**
             *  edit date
             */
            $(".edit_date").live("click",function(e){
                    e.preventDefault();
                    sid = $(this).attr("sid")
                    curr_sch = get_sch_by_id(sid)
                    orig_date = (curr_sch.date.getMonth() + 1) + "/" +(curr_sch.date.getDate())
                    $(".schedule_elem_date[sid='" + sid + "']").html(date_string.replace(/SID_STR/gi, sid).replace(/ORIG_DATE_STR/gi, orig_date))
            });

            $(".confirm_date_change").live("click", function(e){
                    e.preventDefault();
                    sid = $(this).attr("sid")
                    sch_idx = get_sch_idx_by_id(sid)
                    orig_first_day = schedule_list[0].date.getOrdinalNumber() 
                    new_date = $(".date_container[sid='" + sid + "']").val() 
                    if(Date.parse(new_date) == null){
                            alert("invalid date")
                    }
                    else{
                            schedule_list[sch_idx].date = Date.parse(new_date)
                            if(sch_idx == 0 && flag_week_format == 1){ // auto fill
                                    day_offest = schedule_list[sch_idx].date.getOrdinalNumber() - orig_first_day
                                    for(i = 1; i < schedule_list_orig_len; i++ ){
                                            schedule_list[i].date.addDays(day_offest)
                                    }
                                    
                                    flag_week_format = 0
                                    show_schedule()
                            }
                            new_date_str = (schedule_list[sch_idx].date.getMonth() + 1) + "/" + schedule_list[sch_idx].date.getDate()
                            $(".schedule_elem_date[sid='" + sid  + "']").html(new_date_str)
                    }
            });
            
            $(".cancel_date_change").live("click", function(e){
                    e.preventDefault();
                    sid = $(this).attr("sid")
                    sch_idx = get_sch_idx_by_id(sid)
                    new_date_str = (schedule_list[sch_idx].date.getMonth() + 1) + "/" + schedule_list[sch_idx].date.getDate()
                    $(".schedule_elem_date[sid='" + sid  + "']").html(new_date_str)
            });


            $(".delete_all").live("click", function(e){
                    schedule_list = []
                    show_schedule();
                    flag_delete_all = 1
                    $("#orig_syl").html(result_g.replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;"))
            });

            $(window).scroll(function(){
                    adjust_parsed_data_pos();
                    $("#tool_box").animate({"top": ($(window).scrollTop()-125)+"px"}, 10)
                    $("#msg_board").animate({"top": ($(window).scrollTop()-125)+"px"}, 10)
            });


            /*    functions           */	
            
            
            /**
             *  
             */
            function get_sch_by_id(sid){
                    for(i=0;i<schedule_list.length;i++){
                                if(schedule_list[i].id==sid) return schedule_list[i];		
                    }
            }
            
            /**
             *  get schedule element idx by sid
             */

            function get_sch_idx_by_id(sid){
                    for(i=0;i<schedule_list.length;i++){
                                if(schedule_list[i].id==sid) return i;		
                    }
            }

            /**
             *  initialize the editor
             */ 

            function init(){
                    $("#tool_box").fadeTo("slow", 0.9);
                    
                    $("#orig_syl").text("Loading Syllabus...");  
                    
                    $("#msg_board").html("<li>Please double check your assignment dates as some mistakes may occur.</li>")
                    $("#msg_board").fadeTo("fast", 0.8)                    

            }

            function update_msg_board(){
                    if(min_var > 2000){ // parsing is too bad
                            $("#msg_board").html($("#msg_board").html() + "<li class='emph'>If the document has been inccorrectly processed, click <a href='#' class='delete_all'>here</a> to delete all assignments and edit manually!</li>")
                    }
            }
            
            /**
             *  filter out dates previous to current year
             */
            function parse_date(str){
                    if(str.search(/week/gi) != -1){
                            return true
                    }
                    date_out = Date.parse(str)
                    if(date_out == null) return false
                    year_temp = date_out.getFullYear()
                    month_temp = date_out.getMonth() 
                    if(year_temp < curr_year || ( month_temp < valid_month_min || month_temp > valid_month_max ) ){
                        return false;
                    }
                    return true;
            }

            /**
             *  return html for date label
             */
            function get_date_label_html(date_id, date_content){
                    return date_label_string.replace(/SID_STR/gi, date_id).replace(/DATE_CONTENT_STR/gi, date_content)
            }
            
            /**
             *  update date labels, make sure they're turn on/off based on the 'date_label_deleted' attribute
             */
            function update_date_label(){
                    for(i=0;i<schedule_list.length;i++){
                            if(schedule_list[i].date_label_deleted == false){ //NOT date_label_deleted
                                    schedule_list[i].deleted = false
                                    $(".date_label[sid='"+schedule_list[i].id+"'] > a[sid='"+ schedule_list[i].id +"']").text("x")
                                    $(".date_label[sid='"+schedule_list[i].id+"']").fadeTo("fast", 1)
                                    $("span.orig_text_block[sid='"+schedule_list[i].id+"']").fadeTo("fast", 1)
                                    //$("span.orig_text_block[sid='"+schedule_list[i].id+"']").css({"text-decoration": "none", "color": "#000000"})
                            }
                            else{ // date_label_deleted
                                    schedule_list[i].deleted = true
                                    $(".date_label[sid='"+schedule_list[i].id+"'] > a[sid='"+ schedule_list[i].id +"']").text("O")
                                    $(".date_label[sid='"+schedule_list[i].id+"']").fadeTo("fast", 0.5)
                                    $("span.orig_text_block[sid='"+schedule_list[i].id+"']").fadeTo("fast", 0.2)
                                    //$("span.orig_text_block[sid='"+schedule_list[i].id+"']").css({"text-decoration": "line-through", "color": "#F60"})
                            }
                    }
            }
            
            /**
             *  return schedule_elem html
             */
            function get_sch_html(date_id, date_t, content){
                    curr_sch = get_sch_by_id(date_id)
                    month = curr_sch.date.getMonth() + 1
                    day = curr_sch.date.getDate()
                    if(flag_week_format != 1){  // not week format, i.e. not week 1,2,3,4,5
                        return schedule_elem_string.replace(/SID_STR/gi, date_id).replace(/DATE_STR/gi, month + '/' + day).replace(/CONTENT_STR/gi, content)
                    }
                    else{ // week format
                        return schedule_elem_string.replace(/SID_STR/gi, date_id).replace(/DATE_STR/gi, curr_sch.match_str).replace(/CONTENT_STR/gi, content)
                    }
            }
            
            /**
             *  generate html for parsed data based on schedule_list
             */
            function show_schedule(){
                    schedule_lc = "" //create schedule html
                    i_idx=0

                    for(i=0; i<schedule_list.length; i++){
                            i_idx=i
                            content_temp=schedule_list[i].content
                            if(schedule_list[i].next==true){
                                    do{
                                            if(i<schedule_list.length-1){
                                                    content_temp = content_temp+ schedule_list[i+1].match_str + schedule_list[i+1].content;
                                                    i=i+1;
                                            }
                                    }while(schedule_list[i].next == true)
                            }
                            schedule_lc = schedule_lc + get_sch_html(schedule_list[i_idx].id, schedule_list[i_idx].date, content_temp) 
                    }
                    $("#parsed_data").html(schedule_lc)

                    if(flag_week_format == 1){  // if doc can be parsed by week X
                            start_idx =  -1
                            for(i = 0; i < schedule_list.length; i++ ){
                                    if( schedule_list[i].match_str.search(/week 1/gi) != -1 && schedule_list[i].deleted == false ){
                                            start_idx = i
                                            break
                                    }
                            }
                            if(start_idx == -1){
                                    auto_fill_start_idx = 0
                            }
                            else{
                                    auto_fill_start_idx = start_idx
                            }
                            $(".hint:eq(" + auto_fill_start_idx + ")").html("Please enter the date of your first assignment by pressing <span class='sch_btn_2 edit_sch' sid='" + schedule_list[auto_fill_start_idx].id  + "' >edit</span>")
                    } 
            }

            /**
             *  update opacity info of schedule_elem based on 'deleted'
             */ 
            function update_schedule(){ 
                    for(i=0; i<schedule_list.length; i++){
                            sid = schedule_list[i].id
                            if( schedule_list[i].deleted == true ){
                                    $(".schedule_elem[sid='" + sid + "']").fadeTo("fast", 0.2)
                                    $(".toggle_del_sch[sid='" + sid + "']").text("O")
                            }
                            else{
                                    $(".schedule_elem[sid='" + sid + "']").fadeTo("fast", 1)
                                    $(".toggle_del_sch[sid='" + sid + "']").text("x")
                            }

                    } 
            }
            

            /**
             *  adjust vertical position of #parsed_data
             */
            function adjust_parsed_data_pos(){
                    sch_div = $(".schedule_elem[sid]")
                    date_label_span = $(".date_label[sid]")
                    min_dist = 99999999
                    min_idx = ""
                    scorll_val = 0

                    //for(i=0; i<sch_div.length; i++){
                    for(i=0; i<schedule_list.length; i++){
                            temp_id = $(sch_div[i]).attr("sid");
                            if( $(".date_label[sid='"+temp_id+"']").position() == null){
                                    break
                            }
                            temp_dist = $(".date_label[sid='"+temp_id+"']").position().top - $(window).scrollTop() ;
                            
                            if (temp_dist>0  && (Math.abs(temp_dist) < min_dist) ){
                                    min_idx = temp_id
                                    scroll_val = $(".date_label[sid='"+temp_id+"']").position().top
                                    min_dist = Math.abs(temp_dist)
                            }
                    }

                    if($(".schedule_elem[sid='"+min_idx+"']").position() != null){
                            dist = - $(".schedule_elem[sid='"+min_idx+"']").position().top +  $(".date_label[sid='"+min_idx+"']").position().top// - $(".date_label[sid='"+min_idx+"']").position().top 
                            $("div#parsed_data").animate({'margin-top': dist+"px"},{ duration:600 , queue:false })
                    }
            }

            function save_history(){
                    t=$.extend(true, [], schedule_list);
                    history_stack.push(t)
            }

            function variance(val_list){
                    mean = 0

                    for(i=0;i<val_list.length;i++){
                            mean = mean +val_list[i]
                    }
                    mean = mean/val_list.length
                    variance_ = 0

                    for(i=0;i<val_list.length;i++){
                            variance_ = variance_ + (val_list[i]-mean) * (val_list[i]-mean)
                    }
                    
                    variance_ = variance_/val_list.length
                    return variance_
            }

            function sort_function(a,b){
                    return a - b;
            }

            function find_average(val_list){
                    val_list.sort(sort_function)
                    avg = val_list[Math.floor(val_list.length/2)]
            }

            function get_reg_idx(result){
                    max_hit_number=0
                    max_hit_idx=0
                    min_var=999999999
                    best_guess_idx = -1
                    for(m = 0; m < reg.length; m++){ // loop thru all regexp
                            hits = result_g.match(reg[m]);
                            
                            if(hits != null){
                                    //console.log(hits)
                                    filtered_hits=[]
                                    for(i=0;i<hits.length;i++){
                                            if(parse_date(hits[i]) == true){
                                                    filtered_hits.push(hits[i])
                                            }
                                    }
                                    //alert(filtered_hits)
                                    inc_val = []
                                    for(n=0;n<filtered_hits.length-2;n++){
                                            if(Date.parse(filtered_hits[n+1]) == null || Date.parse(filtered_hits[n]) == null) continue
                                            inc_val.push(  (Date.parse(filtered_hits[n+1]).getOrdinalNumber()-Date.parse(filtered_hits[n]).getOrdinalNumber())  )												
                                    }
                                    //alert( inc_val)
                                    if(variance(inc_val) < 1 && inc_val.length > 10){ // min number of dates allowed
                                            console.log("best")
                                            best_guess_idx = m
                                    }
                                    if(variance(inc_val) < min_var && inc_val.length > 2){ // min number of dates allowed
                                            min_var = variance(inc_val)
                                            reg_idx = m
                                            find_average(inc_val)
                                            
                                    }
                            }
                    }
                    console.log(min_var)
                    if(best_guess_idx != -1) {reg_idx = best_guess_idx;}
                    if(reg_idx == 6) {flag_week_format = 1;}
                    
                    return reg_idx
            }
            
            function init_schedule(){
                    for(i = 0; i < schedule_list.length; i++){
                            if(schedule_list[i].date_label_deleted == true){
                                    schedule_list[i].deleted = true
                            }
                    }
            }

            /************   main **************/
            init();
            var processorData = $('#processor-form').serialize();
            $.ajax({

									url: '?q=doc-process',
									type: 'Post',
									cache: false,
									data: processorData,
									success: function(response){
										form = $('#class-selection-form-skeleton');
										$('#institution-id', form).val(response.institution_id);
										$('#year-id', form).val(response.year_id);
										$('#term-id', form).val(response.term_id);
										$('#section-id', form).val(response.section_id);
										$('#suggest-input', form).val(response.course_code);

                    result = response.content; 
										// console.log(result);
                    result = $.trim(result)                                 // result: raw syllabus
                    result = result.replace(/\r\n/gi, "\n")                 // replace \r\n with \n: \r\n is new line in window
                    result = result.replace(/\r/gi, "\n")                   // replace \r with \n: \r is new line in Mac OS 
                    result = result.replace(/\n{3,}/gi, "\n\n")             // all multiple empty lines (>3) will become 2 empty lines
                    result_g = result
                    result_g = result_g.replace(/&nbsp;/gi, " ").replace(/<br>/gi, "\n") 


                    reg_idx = get_reg_idx(result);
                    if(reg_idx == -1){ //no valid pattern found
                            alert("No date assignment found!");
                            schedule_list_orig_len = 0
                            $("#orig_syl").html(result_g.replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;"))
                            return;
                    }
                    pos_list=[]
                    start_idx = 0
                    end_idx = result.length
                    idx = 0
                    while(1){
                            result = result_g.slice(idx, end_idx)
                            match_idx = result.search(reg[reg_idx])
                            if(match_idx == -1) {break}
                            match_str = result.match(reg[reg_idx])[0]
                            if(parse_date(match_str) == true)  {pos_list.push([idx+match_idx, match_str])}
                            
                            idx = idx + match_idx + match_str.length
                    }
                    
                    /* build schedule list, each elem in schedule_list is an sch object */
                    for(i=0; i < pos_list.length;i++){
                            if(i < pos_list.length -1){
                                    sch_temp = sch(pos_list[i][0], pos_list[i+1][0], pos_list[i][1])
                            }
                            else{ //last elem
                                    sch_temp = sch(pos_list[i][0], result_g.length, pos_list[i][1])
                            }
                            if(sch_temp !=null){
                                    schedule_list.push(sch_temp)
                            }
                    }

                    
                    start_pos = 0
                    end_pos = 0
                    start_pos_final = 0
                    end_pos_final = 0
                    max_len = 0
                    for(i=0;i<schedule_list.length-1;i++){
                            if(schedule_list[i].content.length < 3){
                                    continue // some suckers will write their syls as " Quizzes: EVERY Thursday: 1/25, 2/1, 2/8, 2/15, 3/1, 3/8, 3/22, 4/5, 4/12, 4/19 ", which will total screw the smoothing process
                            }
                            console.log(i,i+1)
                            curr_day = schedule_list[i].date.getOrdinalNumber()
                            next_day = schedule_list[i+1].date.getOrdinalNumber()
                            
                            if( (next_day-curr_day) > 0 && (next_day-curr_day) < avg*4 ){
                                    end_pos = i+1
                            }
                            else{
                                    if( (end_pos-start_pos) > max_len ){
                                            max_len = end_pos-start_pos
                                            start_pos_final = start_pos
                                            end_pos_final = end_pos
                                            console.log("break", start_pos_final, end_pos_final, max_len)
                                    }
                                    start_pos = i+1
                                    end_pos = i+2
                            }
                    }
                    
                    if( (end_pos-start_pos) > max_len ) { max_len = end_pos-start_pos; start_pos_final = start_pos; end_pos_final = end_pos }
                    
                    console.log("smooth",start_pos_final, end_pos_final)
                    
                    /* filtration */ 
                    for(i=end_pos_final; i<schedule_list.length-1; i++){
                            for(j=i+1; j<schedule_list.length; j++){
                                    start_day = schedule_list[i].date.getOrdinalNumber()
                                    curr_day = schedule_list[j].date.getOrdinalNumber()
                                    if( (curr_day-start_day) >= 0 && (curr_day-start_day) < 15){ 
                                            i=j
                                            continue
                                    }
                                    else{
                                            
                                            schedule_list[j].date_label_deleted = true
                                            schedule_list[j-1].next = true
                                    }
                            }
                    }
                    
                    for(i=start_pos_final; i>0; i--){
                            for(j=i-1; j>=0; j--){
                                    start_day = schedule_list[i].date.getOrdinalNumber()
                                    curr_day = schedule_list[j].date.getOrdinalNumber()
                                    if( (start_day-curr_day) >= 0 && (start_day-curr_day) < 15 ){
                                            i=j
                                            continue
                                    }
                                    else{
                                            schedule_list[j].date_label_deleted = true
                                            if (j>0) {schedule_list[j-1].next = true}
                                    }
                            }
                    }
   		    pre_text_orig = result_g.slice(0, pos_list[0][0])
                    pre_text_trunc_size = pre_text_orig.length > 200 ? 200 : pre_text_orig.length
                    pre_text_trunc = pre_text_orig.slice(0, pre_text_trunc_size) + "...";
                    pre_text = "<a href='#' class='button' id='toggle_pre_text' value='off'  >show text</a><div id='pre_text' >" + pre_text_trunc+ "</div>"
                     
                    orig_syl_content = pre_text
                    
                    for(i=0;i<schedule_list.length;i++){
                            orig_syl_content = orig_syl_content + get_date_label_html(schedule_list[i].id, schedule_list[i].match_str)
                            orig_syl_content = orig_syl_content + "<span class='orig_text_block' sid='" + schedule_list[i].id + "'>" + result_g.slice(schedule_list[i].start_pos+schedule_list[i].match_str.length, schedule_list[i].end_pos) + "</span>"
                    }
                    
                    orig_syl_content = orig_syl_content + result_g.slice(schedule_list[i-1].end_pos, result_g.length)
                    $("#orig_syl").html(orig_syl_content.replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;"))
                    $("#pre_text").fadeTo("fast", 0.2);
                     
                    
                   
                    init_schedule();
                    show_schedule();
                    schedule_list_orig_len = schedule_list.length
                    update_date_label()
	            update_schedule()	
                    update_msg_board()			   
                    save_history();
                    


                   }});        
        /*****************      end of main     *********************/  

	/**
	 * Create tasks from document
	 *
	 * This process goes through the elements by the class attribute, so it's 
	 * important to keep consistency.
	 */
	$('#create-task').click(function(e) {
                if(flag_week_format == 1){
                        dialog.open("alert_box", "please enter the date of your first assignment")
                        return
                }
		e.preventDefault();

		var selectionForm = $('#class-selection-form-skeleton').clone();
		selectionForm.attr('id', 'class-selection-form');
		var content = '<div class="dialog-content">' +
			'<div class="confirm-message">' +
				"<h2>Before we submit everything, let's take a final look... </h2>" + 
				'<ul>' +
					'<li>Are all the dates correct?</li>' +
					'<li>Are there missing assignments?</li>' +
					'<li>Do the assignments make sense?</li>' +
				'</ul>' +
				'<h3>If everything works, congrats! Fill out the the form below and hit submit!</h3>' +
			'</div>' +
		'</div>';
		dialog.open('confirm-class', content);
		$('.confirm-message').after(selectionForm);
		selectionForm.removeClass('hidden');

		var classEdit = new ClassEdit('#class-selection-form', '#suggest-input');

		/**
		 * Confirm task creation
		 *
		 * This action has two different flow depend on where user comes from.
		 *  - /welcome: this is a first-time user and goes on to register for an 
		 *    account
		 *  - /home, /calendar, and /class: the user already has an account,
		 *    proceeds to the enroll in the class, and then go to the class page
		 */
		$('.confirm', selectionForm).live('click', function(e) {
			e.preventDefault();
			var taskCreationForm = $('#task-creation-form');
			var processState = $('input[name=process_state]', taskCreationForm).val();
			var creationForm = $('#task-creation-form');
			var taskEle = $('.schedule_elem');
			creationForm.append('<input type="hidden" name="task_count" value="' + taskEle.length + '" />');

			$('.dialog-close').live('click', function(e) {
				e.preventDefault();
				dialog.close()
			});
                       /* 
			taskEle.each(function(index, value) {
				date = $('.schedule_elem_date', value).text().replace('Date:', '') + '/2011';
				objective = $('.sch_content', value).text();
				creationForm.append('<input type="hidden" name="date_' + index + '" value="' + date + '" />');
				creationForm.append('<input type="hidden" name="objective_' + index + '" value="' + objective + '" />');
			});*/
                        
                        cnt = 0; 
                        for(i=0; i<schedule_list.length; i++){
                                if(schedule_list[i].deleted == false){
                                        curr_date =  schedule_list[i].date
                                        sch_date = (curr_date.getMonth() + 1) + "/" + (curr_date.getDate()) + "/" + "2011"
                                        sch_content = schedule_list[i].content
				        creationForm.append('<input type="hidden" name="date_' + cnt + '" value="' + sch_date + '" />');
				        creationForm.append('<input type="hidden" name="objective_' + cnt + '" value="' + sch_content + '" />');
                                        cnt = cnt + 1
                                }
                        }
			var content = '';

			$.ajax({
				url: '/task-add-from-doc',
				type: 'post',
				cache: false,
				data: creationForm.serialize() + '&section_id=' + $('#section-id', selectionForm).val(),
				success: function(response) {
					var section_id = $('#section-id', selectionForm).val();
					content = '<h3 class="title">' + response.message + '</h3>' + 
					'<hr />' +
					'<div class="suggested-reading">' +
						'<div id="enroll-book-list" class="book-list">' + 
						'</div>' +
					'</div>';

					/**
					 * In this case, the user comes from /welcome and should be greeted with 
					 * option to sign up.
					 *
					 * @see js/model/register.js
					 */
					if (processState == 'sign-up') {
						var signUpOption = SignUp.getOptions();
						var fbUid = '';
						$FB(function() {
							FB.getLoginStatus(function(response) {
								if (response.authResponse) {
									fbUid = response.authResponse.userID;
								}
							});
						});

						content += '<div class="sign-up-option">' + SignUp.getOptions() + '</div>';
						$('.dialog-inner').delegate('a.sign-up', 'click', function(e) {
							e.preventDefault();
							var target = $(this);
							var url = target.attr('href') + '?section_id=' + section_id;
							if (target.hasClass('facebook')) {
								window.location = url + '&fb=true&fb_uid=' + fbUid;
							} else {
								window.location = url;
							}
						});

					// otherwise, the user is an existing user and needs to be added to the
					// class
					} else if (processState == 'redirect') {
						content += '<a href="#" class="go-to-class-page button">go to class page</a>';
						$.ajax({
							url: '/college-class-enroll',
							type: 'post',
							data: 'section_id=' + $('#section-id', selectionForm).val(),
							success: function(response) {
								if (response.error) {
									$('.suggested-reading').after('<h3 class="warning">' +
										'It appears you have more than 6 classes. This class will not be added to your list' +
									'</h3>');
									$('.dialog-content a.button').text('back to home page');
									response.redirect = '/home';
								}

								// binding redirect actions to class page
								if (response.redirect) {

									// a helper function to redirect page
									var goToClassPage = function(url) {
										window.location = response.redirect;
									};

									$('.dialog-close', $P).live('click', function(e) {
										goToClassPage(response.redirect);
									});
									$('.go-to-class-page', $P).live('click', function(e) {
										goToClassPage(response.redirect);
									});
								}

							}
						});
					}

					$('.dialog-inner .dialog-content').html(content);
					bookList = new BookSuggest('.book-list');
					bookList.getBookList($('#section-id', selectionForm).val())
				}
			});
		});
	});

});
