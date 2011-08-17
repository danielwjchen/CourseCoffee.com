$P.ready(function(){
            /*    global variables    */
            history_stack = [];
            redo_stack = [];
            history_pointer = 0;
            result_g = "";
            avg = 0 //average increasement
            reg_idx = -1;
            flag_week_format = 0;
            
            
            pre_text_orig = "";
            pre_text_trunc = "";
            flag_show_orig_pre_text = 0

            curr_date = new Date();
            curr_year = curr_date.getFullYear();
            curr_year = 2000
            month=["january","february","march","april","may","june","july","august","september","october","november","december","jan","feb","mar",  "apr",  "may","jun", "jul", "aug","sep","sept","oct",       "nov",     "dec","week"];
            reg=[]
            reg[0]=/((0?[1-9])|(1[0-2])){1}\/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}\/(\d{4}|\d{2})/gi;	// mm/dd/yy
            reg[1]=/(0?[1-9]|1[0-2]){1}\/(1[0-9]|2[0-9]|3[0-1]|0?[1-9]){1}(?=[^\/0-9])/g;	// mm/dd(/2011 2012)
            reg[2]=/(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}( *|,|.){0,2}((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}(?=\W)(\W+\d{4})?/gi
            reg[3]=/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}-(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}(\W+\d{4})?/gi
            reg[4]=/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1} (january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}(\W+\d{4})?/gi
            reg[5]=/^[^0-9](0?[1-9]|1[0-2]){1}-(1[0-9]|2[0-9]|3[0-1]|0?[1-9]){1}(?=[^0-9])/gi
            reg[6]=/week\W+\d{1,2}/gi
            reg[7]=/((0?[1-9])|(1[0-2])){1}-((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}(-(\d{4}|\d{2}))?(?=[^\/0-9])/gi;	// mm-dd-yy or mm-dd

            fil_list = [/mon/gi, /tue/gi, /wed/gi, /thu/gi, /thur/gi, /fri/gi, /sat/gi, /sun/gi,/week/gi, /m/gi , /t/gi, /w/gi, /r/gi, /f/gi]
            
            
            
            schedule_list = []
            schedule_list_orig_len = 0
            word = [];
            delim = [];
            orig_html = "";
	    pre_text = ""; // text before real date

            
            /* html elements e.g. span, tables for dynamically loading*/
            date_label_string =  "<br><span class='date_label' sid='SID_STR'><span>DATE_CONTENT_STR</span><a href='#' sid='SID_STR' class='del_date_label' >x</a></span>" ;
            schedule_elem_string = "<div sid='SID_STR' class='schedule_elem'>\
                                    <table width='450' border='0' cellpadding='0' cellspacing='0'>\
                                    <tr>\
                                        <td width='150'><span class='schedule_elem_date' sid='SID_STR'>DATE_STR</span></td>\
                                        <td width='250' id='editing_btn'></td>\
                                        <td width='50'><a href='#' class='sch_btn' sid='SID_STR' id='toggle_del_sch'>x</a></td>\
                                    </tr>\
                                    <tr>\
                                        <td colspan='3' class='sch_content' sid='SID_STR' >CONTENT_STR</td>\
                                    </tr>\
                                    </table></div><div class='schedule_elem_space'></div>";
            schedule_edit_string = "<textarea sid='SID_STR' class='schedule_elem_edit'>CONTENT_STR</textarea>\
                                    <input type='button' id='save_sch' sid='SID_STR' value='save'>\
                                    <input type='button' id='cancel_sch' sid='SID_STR' value='cancel'>"           
            
            /*       flags            */
            editing_flag = 0;


            /*    data stuctures      */
            
            /**
             *  create schedule element, need to be pushed into schedule_list
             */
            function sch(start_pos, next_start_pos, match_str){
                    if(start_pos ==  -1 && next_start_pos == -1){
                        content = "New assignment"
                        id_temp = Math.random()
                        date_label_deleted = false
                    }
                    else{ 
                            id_temp=start_pos
                            content = result_g.slice(start_pos+match_str.length, next_start_pos)
                            if(content.lastIndexOf('\n') !=-1 && content.search(/[a-z]/i) !=-1){
                                last_oc_idx = content.lastIndexOf('\n')
                                first_oc_idx =  content.search(/[a-z]/i)
                                last_line = content.slice(last_oc_idx, content.length)
                                
                                hit_flag = 0
                                for (j = 0; j < fil_list.length; j++){
                                        if(last_line.match(fil_list[j]) != null) { hit_flag = 1; break; }
                                }
                                
                                if(last_oc_idx > first_oc_idx && hit_flag == 1){
                                        content = content.slice(first_oc_idx, last_oc_idx)
                                }
                            }

                            content = content.replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;")

                            if(content.length < 4){
                                        date_label_deleted=true
                            }
                            else{
                                        date_label_deleted=false	
                            }
                    
                    } 
                    
                    if(parse_date(match_str) == true){ date_out = Date.parse(match_str) }
                    var temp_sch = { 'id' : id_temp, 'start_pos' : start_pos, 'end_pos': next_start_pos, 'match_str' : match_str,'content': content , 'modified': 0, 'catergory': '', 'date': date_out, 'next': false, 'deleted': false, 'date_label_deleted': date_label_deleted};
                    return temp_sch;

            };

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
                    
                    history_stack.push( $.extend(true, [], schedule_list) );
                    show_schedule();
                    adjust_spacing();
                    update_schedule();
                    update_date_label();
            });
            

            /**
             *  indicate which schedule_elem is being pointed 
             */
            $(".date_label").live("mouseenter",function(){
                    temp_id = $(this).attr("sid")
                    $(".schedule_elem[sid='"+temp_id+"']").css("background-color","#99CCFF");
                        
            });

            $(".date_label").live("mouseleave",function(){
                    temp_id = $(this).attr("sid")
                    $(".schedule_elem[sid='"+temp_id+"']").css("background-color","");									 
            });


            /**
             *  edit schedule content
             */
            $("a[id='edit_sch']").live("click",function(e){
                    e.preventDefault();
                    if(editing_flag == 1) return;
                    
                    orig_sid=$(this).attr("sid")
                    orig_html = $(".sch_content[sid='"+orig_sid+"']").html().replace(/<br>/gi,"\n").replace(/&nbsp;/g, " ")
                    orig_height = $(".sch_content[sid='"+orig_sid+"']").height()*0.8
                    orig_width = $(".sch_content[sid='"+orig_sid+"']").width()
                    $(".sch_content[sid='" + orig_sid + "']").html(schedule_edit_string.replace(/SID_STR/gi, orig_sid).replace(/CONTENT_STR/gi, orig_html))
                    editing_flag=1
                    adjust_spacing();
            });


            /**
             *  delete schedule_elem (toggle)
             */
            $("a[id='toggle_del_sch']").live("click",function(e){
                    e.preventDefault();
                    sid = $(this).attr("sid")
                    sch_idx = get_sch_idx_by_id(sid)
                    schedule_list[sch_idx].deleted = !schedule_list[sch_idx].deleted
                    update_schedule();                   
                    history_stack.push( $.extend(true, [], schedule_list) );
            });


            /**
             *  showing edit buttons when mouse entered schedule_elem
             */

            $(".schedule_elem").live("mouseenter",function(){
                    id_temp = $(this).attr("sid")
                    $("td[id]", this).html("<a href='#' class='sch_btn_2'  sid='"+id_temp+"' id='edit_date'>change date</a> <a href='#' class='sch_btn_2'  sid='"+id_temp+"' id='edit_sch'>edit</a>")
                    adjust_spacing();
                    adjust_parsed_data_pos();
            });


            $(".schedule_elem").live("mouseleave",function(){					
                    $("td[id]", this).html("")
                    adjust_spacing();
                    adjust_parsed_data_pos();
            });

            /**
             *  save schedule
             */

            $("#save_sch").live("click",function(){
                    id_temp = $(this).attr("sid")
                    content_temp = $("textarea[sid='"+id_temp+"']").val().replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;")
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


            /**
             *  cancel editing schedule
             */

            $("#cancel_sch").live("click",function(){
                    id_temp = $(this).attr("sid")
                    content_temp = $("textarea[sid='"+id_temp+"']").text().replace(/(\n|\r)/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;")
                    $(".sch_content[sid='"+$(this).attr("sid")+"']").html(content_temp);
                    editing_flag=0
            });

            /**
             *  undo, get back to the previous schedule_list elem
             */

            $("#undo").click(function (){
                    if(history_stack.length >1){
                            redo_stack.push(history_stack.pop())
                    }        

                    schedule_temp = history_stack[history_stack.length-1]									
                    schedule_list = $.extend(true, [], schedule_temp);
                    show_schedule();
                    adjust_spacing();
                    adjust_parsed_data_pos();
                    update_date_label();
                    update_schedule();                   
                    editing_flag = 0 
            });

            /**
             * redo
             */
            $("#redo").click(function(){
                    if(redo_stack.length>0){
                            history_stack.push(redo_stack.pop())
                    }
                    
                    schedule_temp = history_stack[history_stack.length-1]									
                    schedule_list = $.extend(true, [], schedule_temp);
                    show_schedule();
                    adjust_spacing();
                    adjust_parsed_data_pos();
                    update_date_label();
                    editing_flag = 0
            });
	
            /**
             *  add new assignment
             */        
            $("#new_assignment").click(function(){
                    $("#parsed_data").html("")
                    s = sch(-1,-1,"1/1")  
                    schedule_list.push(s)
                    show_schedule()
                    adjust_spacing()
                    adjust_parsed_data_pos()
                    
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
            
            date_string = "<input type='text' class='date_container' sid='SID_STR' size=10 value='ORIG_DATE_STR'><a href='#' id='confirm_date_change' sid='SID_STR' class='sch_btn_3'>O</a><a href='#' id='cancel_date_change' sid='SID_STR' class='sch_btn_3'>X</a>"


            /**
             *  edit date
             */
            $("a[id='edit_date']").live("click",function(e){
                    e.preventDefault();
                    sid = $(this).attr("sid")
                    curr_sch = get_sch_by_id(sid)
                    orig_date = (curr_sch.date.getMonth() + 1) + "/" +(curr_sch.date.getDate())
                    $(".schedule_elem_date[sid='" + sid + "']").html(date_string.replace(/SID_STR/gi, sid).replace(/ORIG_DATE_STR/gi, orig_date))
            });

            $("a[id='confirm_date_change']").live("click", function(e){
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
            
            $("a[id='cancel_date_change']").live("click", function(e){
                    e.preventDefault();
                    sid = $(this).attr("sid")
                    sch_idx = get_sch_idx_by_id(sid)
                    new_date_str = (schedule_list[sch_idx].date.getMonth() + 1) + "/" + schedule_list[sch_idx].date.getDate()
                    $(".schedule_elem_date[sid='" + sid  + "']").html(new_date_str)
            });

            $(window).scroll(function(){
                    adjust_parsed_data_pos();
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
                    //$("#parsed_data").css("left" , $("#table_syl").position().left + $("#table_syl").width()+20  );
                    //$("#tool_box").css("left",  $("#parsed_data").position().left);
                    $("#orig_syl").text("Loading Syllabus...");  
            }
            
            /**
             *  filter out dates previous to current year
             */
            function parse_date(str){
                    date_out = Date.parse(str)
                    if(date_out == null) return false
                    year_temp = date_out.getFullYear()

                    if(year_temp<curr_year){
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
                                    $(".date_label[sid='"+schedule_list[i].id+"'] > a[sid='"+ schedule_list[i].id +"']").text("x")
                                    $(".date_label[sid='"+schedule_list[i].id+"']").fadeTo("fast", 1)
                                    $("span.orig_text_block[sid='"+schedule_list[i].id+"']").css({"text-decoration": "none", "color": "#000000"})
                            }
                            else{ // date_label_deleted
                                    $(".date_label[sid='"+schedule_list[i].id+"'] > a[sid='"+ schedule_list[i].id +"']").text("O")
                                    $(".date_label[sid='"+schedule_list[i].id+"']").fadeTo("fast", 0.5)
                                    $("span.orig_text_block[sid='"+schedule_list[i].id+"']").css({"text-decoration": "line-through", "color": "#F60"})
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
                     
            }

            /**
             *  update opacity info of schedule_elem based on 'deleted'
             */ 
            function update_schedule(){ 
                    for(i=0; i<schedule_list.length; i++){
                            sid = schedule_list[i].id
                            if( schedule_list[i].deleted == true ){
                                    $(".schedule_elem[sid='" + sid + "']").fadeTo("fast", 0.2)
                            }
                            else{
                                    $(".schedule_elem[sid='" + sid + "']").fadeTo("fast", 1)
                            }

                    } 
            }
            
            /**
             *  adjust spacing between scheduel_elem
             */
            function adjust_spacing(){
                return
                    if(schedule_list_orig_len == 0) return
                    sch_div = $(".schedule_elem[sid]")
                    curr_sid = $(sch_div[0]).attr("sid")
                    curr_pos = $(".date_label[sid='"+curr_sid+"']").position().top
                    $(sch_div[0]).css("position", "absolute")
                    $(sch_div[0]).css("top", curr_pos)

                    for(i=1; i<sch_div.length; i++){
                            curr_sid = $(sch_div[i]).attr("sid")
                            temp_curr = $(".date_label[sid='"+curr_sid+"']").position()
                            if(temp_curr == null){
                                curr_pos = 0
                            }
                            else{
                                curr_pos = $(".date_label[sid='"+curr_sid+"']").position().top
                            }
                            prev_sid = $(sch_div[i-1]).attr("sid")
                            prev_pos = $(".schedule_elem[sid='"+prev_sid+"']").position().top + $(".schedule_elem[sid='"+prev_sid+"']").height()
                            $(sch_div[i]).css("position", "absolute")
                            $(sch_div[i]).css("top", Math.max(curr_pos, prev_pos))
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
                    for(i=0; i<schedule_list_orig_len; i++){
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

                    for(m=0;m<reg.length;m++){
                            hits = result_g.match(reg[m]);
                            
                            if(hits != null){
                                    //console.log(hits)
                                    filtered_hits=[]
                                    for(i=0;i<hits.length;i++){
                                            if(Date.parse(hits[i])!= null && Date.parse(hits[i]).getFullYear() >= curr_year){
                                                    filtered_hits.push(hits[i])
                                            }
                                    }
                                    //console.log(filtered_hits)
    
                                    inc_val = []
                                    for(n=0;n<filtered_hits.length-2;n++){
                                            inc_val.push(  (Date.parse(filtered_hits[n+1]).getOrdinalNumber()-Date.parse(filtered_hits[n]).getOrdinalNumber())  )												
                                    }
                                    //console.log(m, inc_val)
                                    if(variance(inc_val) < min_var && inc_val.length > 2){ // min number of dates allowed
                                            min_var = variance(inc_val)
                                            reg_idx =m
                                            find_average(inc_val)
                                            
                                    }
                            }
                    }
                    //reg_idx = 6
                    if(reg_idx == 6) {flag_week_format = 1;}
                    return reg_idx
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
                    
                    /* build scheduel list, each elem in schedule_list is an sch object */
                    for(i=0;i<pos_list.length-1;i++){
                            sch_temp = sch(pos_list[i][0], pos_list[i+1][0], pos_list[i][1])
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
                            //console.log(start_pos,end_pos)
                            curr_day = schedule_list[i].date.getOrdinalNumber()
                            next_day = schedule_list[i+1].date.getOrdinalNumber()
                            
                            if( (next_day-curr_day) > 0 && (next_day-curr_day) < avg*3 ){
                                    end_pos = i+1
                            }
                            else{
                                    if( (end_pos-start_pos) > max_len ){
                                            max_len = end_pos-start_pos
                                            start_pos_final = start_pos
                                            end_pos_final = end_pos
                                            //console.log("break", start_pos_final, end_pos_final, max_len)
                                    }
                                    start_pos = i+1
                                    end_pos = i+2
                            }
                    }
                    
                    if( (end_pos-start_pos) > max_len ) { max_len = end_pos-start_pos; start_pos_final = start_pos; end_pos_final = end_pos }
                    
                    //console.log("smooth",start_pos_final, end_pos_final)
                    
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
                     
                    content = pre_text
                    
                    for(i=0;i<schedule_list.length;i++){
                            content = content + get_date_label_html(schedule_list[i].id, schedule_list[i].match_str)
                            content = content + "<span class='orig_text_block' sid='" + schedule_list[i].id + "'>" + result_g.slice(schedule_list[i].start_pos+schedule_list[i].match_str.length, schedule_list[i].end_pos) + "</span>"
                    }
                    
                    $("#orig_syl").html(content.replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;"))
                    $("#pre_text").fadeTo("fast", 0.2);

                    show_schedule();
                    schedule_list_orig_len = schedule_list.length
                    adjust_spacing();
                    /*t=$.extend(true, [], schedule_list);
                    history_stack.push(t)*/
                    save_history();
                    update_date_label()
						   
                   }});        
        /*****************      end of main     *********************/  

	/**
	 * Create tasks from document
	 */
	$('#create-task').click(function(e) {
		e.preventDefault();
		var form = $('#task-creation-form');
		var taskEle = $('.schedule_elem');
		form.append('<input type="hidden" name="task_count" value="' + taskEle.length + '" />');

		$('.dialog-close').live('click', function(e) {
			e.preventDefault();
			dialog.close()
		});

		taskEle.each(function(index, value) {
			date = $('.schedule_elem_title', value).text().replace('Date:', '') + '/2011';
			objective = $('.sch_content', value).text();
			form.append('<input type="hidden" name="date_' + index + '" value="' + date + '" />');
			form.append('<input type="hidden" name="objective_' + index + '" value="' + objective + '" />');
		});

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
		 */
		$('.confirm', selectionForm).live('click', function(e) {
			e.preventDefault();
			var taskCreationForm = $('#task-creation-form');
			var processState = $('input[name=process_state]', taskCreationForm).val();
			var content = '';

			$.ajax({
				url: '/task-add-from-doc',
				type: 'post',
				cache: false,
				data: 'section_id=' + $('#section-id', selectionForm).val(),
				success: function(response) {
					content = '<h3>' + response.message + '</h3>' + 
					'<hr />' +
					'<div class="suggested-reading">' +
						'<div id="enroll-book-list" class="book-list">' + 
						'</div>' +
					'</div>';

					// in this case, the user comes from /welcome and should be greeted with 
					// option to sign up
					if (processState == 'sign-up') {
						content += '<a href="/sign-up?section_id=' + response.section_id + '" class="button sign-up">sign up</a>';

					// otherwise, the user is an existing user and needs to be added to the
					// class
					} else if (response.section_id) {
						$.ajax({
							url: '/college-class-enroll',
							type: 'post',
							data: 'section_id=' + response.section_id,
							success: function(response) {
								$('.dialog-close', $P).live('click', function(e) {
									e.preventDefault();
									window.location = response.redirect;
									dialog.close()
								});
							}
						});
					}

					$('.dialog-inner .dialog-content').html(content);
					bookList = new BookSuggest('.book-list');
					bookList.getBookList(response.section_id);
				}
			});
		});
	});

});
