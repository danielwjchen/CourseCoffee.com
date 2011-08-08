$P.ready(function(){
            /*    global variables    */
            history_stack = [];
            redo_stack = [];
            history_pointer = 0;
            result_g = "";
            avg = 0 //average increasement
            reg_idx = -1;
            flag_week_format = 0;

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
            
            fil_list = [/mon/gi, /tue/gi, /wed/gi, /thu/gi, /thur/gi, /fri/gi, /sat/gi, /sun/gi,/week/gi, / m /gi , / t /gi, / w /gi, / r /gi, / f /gi, / u /gi, / s /gi]


            
            schedule_list = []
            word = [];
            delim = [];
            orig_html="";

            /*       flags            */
            editing_flag = 0;


            /*    data stuctures      */
            function sch(start_pos, next_start_pos, match_str){
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

                    if(parse_date(match_str)==true){ date_out = Date.parse(match_str) }
                    var temp_sch = { 'id' : id_temp, 'start_pos' : start_pos, 'end_pos': next_start_pos, 'match_str' : match_str,'content': content , 'modified': 0, 'catergory': '', 'date': date_out, 'next': false, 'deleted': false, 'date_label_deleted': date_label_deleted};
                    return temp_sch;

            };

            $(".del_date_label").live("click",function(){
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
                    update_date_label();
            });


            $(".date_label").live("mouseenter",function(){
                    temp_id = $(this).attr("sid")
                    $(".schedule_elem[sid='"+temp_id+"']").css("background-color","#99CCFF");
                        
            });

            $(".date_label").live("mouseleave",function(){
                    temp_id = $(this).attr("sid")
                    $(".schedule_elem[sid='"+temp_id+"']").css("background-color","");									 
            });


            $(".btn[id='edit_sch']").live("click",function(){
                    if(editing_flag == 1) return;
                    
                    orig_sid=$(this).attr("sid")
                    orig_html = $(".sch_content[sid='"+orig_sid+"']").html().replace(/<br>/gi,"\n").replace(/&nbsp;/g, " ")
                    orig_height = $(".sch_content[sid='"+orig_sid+"']").height()*0.8
                    orig_width = $(".sch_content[sid='"+orig_sid+"']").width()
                    $(".sch_content[sid='"+orig_sid+"']").html("<textarea sid='"+orig_sid+"' style='width:"+"500"+"px; height:"+orig_height+"px'>"+orig_html+"</textarea>\
                                             <input type='button' id='save_sch' sid='"+orig_sid+"' value='save'/><input type='button' id='cancel_sch' sid='"+orig_sid+"' value='cancel'/>")
                    editing_flag=1
                    adjust_spacing();
            });

            $(".btn[id='del_sch']").live("click",function(){
                    sid = $(this).attr("sid")
                    sch_idx = get_sch_idx(sid)
                    schedule_list[sch_idx].date_label_deleted == true
                    adjust_spacing();
            });


            $(".schedule_elem").live("mouseenter",function(){
                    id_temp = $(this).attr("sid")
                    $("td[id]", this).html("<span class='btn'  sid='"+id_temp+"' id='edit_sch'>edit</span> <span class='btn'  sid='"+id_temp+"' id='del_sch'>delete</span>")
                    adjust_spacing();
                    adjust_parsed_data_pos();
            });


            $(".schedule_elem").live("mouseleave",function(){					
                    $("td[id]", this).html("")
                    adjust_spacing();
                    adjust_parsed_data_pos();
            });

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

            $("#cancel_sch").live("click",function(){
                    id_temp = $(this).attr("sid")
                    content_temp = $("textarea[sid='"+id_temp+"']").text().replace(/(\n|\r)/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;")
                    $(".sch_content[sid='"+$(this).attr("sid")+"']").html(content_temp);
                    editing_flag=0
            });


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
                    editing_flag = 0 
            });

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


            $(window).scroll(function(){
                    adjust_parsed_data_pos();
                    $("#tool_box").css("top", $(window).scrollTop() +"px")
            });


            /*    functions           */	

            function get_sch_idx(sid){
                    for(i=0;i<schedule_list.length;i++){
                                if(schedule_list[i].id==sid) return i;		
                    }
            }

            function get_sch_by_idx(sid){
                    for(i=0;i<schedule_list.length;i++){
                                if(schedule_list[i].id==sid) return schedule_list[i];		
                    }
            }
                                  
            function init(){
                    $("#parsed_data").css("left" , $("#table_syl").position().left + $("#table_syl").width() + 20  );
                    $("#tool_box").css("left",  $("#parsed_data").position().left);
                    $("#orig_syl").text("Loading Syllabus...");  
            }

            function parse_date(str){
                    date_out = Date.parse(match_str)
                    //console.log(match_str, date_out)
                    year_temp = date_out.getFullYear()

                    if(year_temp<curr_year){
                        return false;
                    }
                    return true;
            }

            function get_date_label_html(date_id, date_content){
                    return "<br><span class='date_label' sid='"+date_id+"'>" + "<span>" + date_content + "</span><span sid='"+date_id+"' class='del_date_label'> X</span></span>" 
            }

            function update_date_label(){
                    for(i=0;i<schedule_list.length;i++){
                            if(schedule_list[i].date_label_deleted == false){ //NOT date_label_deleted
                                    $(".date_label[sid='"+schedule_list[i].id+"'] > span[sid='"+ schedule_list[i].id +"']").text("x")
                                    $(".date_label[sid='"+schedule_list[i].id+"']").fadeTo("fast", 1)
                            }
                            else{ // date_label_deleted
                                    $(".date_label[sid='"+schedule_list[i].id+"'] > span[sid='"+ schedule_list[i].id +"']").text("O")
                                    $(".date_label[sid='"+schedule_list[i].id+"']").fadeTo("fast", 0.5)
                            }
                    }
            }

            function get_sch_html(date_id, date_t, content){
                    if(flag_week_format != 1){
                            return '<div sid="'+date_id+'" class="schedule_elem" >\
                                    <table width="500" border="0" cellpadding="0" cellspacing="0">\
                                    <tr>\
                                      <td width="135" class="schedule_elem_title">Date:'+(date_t.getMonth()+1)+'/'+date_t.getDate()+'</td>\
                                      <td width="200" id="editing_btn">&nbsp;</td>\
                                      <td width="149">&nbsp;</td>\
                                    </tr>\
                                    <tr>\
                                      <td colspan="3" class="sch_content" sid="'+date_id+'" >'+content+'</td>\
                                    </tr>\
                                    </table></div>'
                    }
                    else{
                            return '<div sid="'+date_id+'" class="schedule_elem" >\
                                    <table width="500" border="0" cellpadding="0" cellspacing="0">\
                                    <tr>\
                                      <td width="135" class="schedule_elem_title">Date:'+get_sch_by_idx(date_id).match_str+'</td>\
                                      <td width="200" id="editing_btn">&nbsp;</td>\
                                      <td width="149">&nbsp;</td>\
                                    </tr>\
                                    <tr>\
                                      <td colspan="3" class="sch_content" sid="'+date_id+'" >'+content+'</td>\
                                    </tr>\
                                    </table></div>'
                    }
            }

            function show_schedule(){
                    schedule_lc = "" //create schedule html
                    i_idx=0

                    for(i=0; i<schedule_list.length; i++){
                            if(schedule_list[i].date_label_deleted==false){
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
                    }

                    $("#parsed_data").html(schedule_lc)
            }

            function adjust_spacing(){
                    sch_div = $(".schedule_elem[sid]")
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
                    }
            }


            function adjust_parsed_data_pos(){
                    sch_div = $(".schedule_elem[sid]")
                    date_label_span = $(".date_label[sid]")
                    min_dist = 99999999
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
                                    if(variance(inc_val) < min_var && inc_val.length > 6){
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
										$('#section-id', form).val(response.year_id);
										$('#suggest-input', form).val(response.course_code);

                    result = response.content; 
										// console.log(result);
                    result = $.trim(result)                                 // result: raw syllabus
                    result = result.replace(/\r\n/gi, "\n")                 // replace \r\n with \n: \r\n is new line in window
                    result = result.replace(/\r/gi, "\n")                   // replace \r with \n: \r is new line in Mac OS 
                    result = result.replace(/\n{3,}/gi, "\n\n")             // all multiple empty lines (>3) will become 2 empty lines
                    result_g = result
                    
                    reg_idx = get_reg_idx(result);
                    //console.log(reg_idx)
                    if(reg_idx == -1){ //no valid pattern found
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
                                    //console.log(i,j,schedule_list[i].match_str,schedule_list[j].match_str, start_day, curr_day)
                                    if( (curr_day-start_day) >= 0 && (curr_day-start_day) < 15){ 
                                            i=j
                                            //console.log("con")
                                            continue
                                    }
                                    else{
                                            
                                            //console.log("brk",j)
                                            schedule_list[j].date_label_deleted = true
                                            schedule_list[j-1].next = true
                                    }
                            }
                    }
                    
                    for(i=start_pos_final; i>0; i--){
                            for(j=i-1; j>=0; j--){
                                    start_day = schedule_list[i].date.getOrdinalNumber()
                                    curr_day = schedule_list[j].date.getOrdinalNumber()
                                    //console.log(i,j,schedule_list[i].match_str,schedule_list[j].match_str, start_day, curr_day, (start_day-curr_day), avg*3)
                                    if( (start_day-curr_day) >= 0 && (start_day-curr_day) < 15 ){
                                            i=j
                                            //console.log("con")
                                            continue
                                    }
                                    else{
                                            //console.log("brk",j)
                                            schedule_list[j].date_label_deleted = true
                                            if (j>0) {schedule_list[j-1].next = true}
                                    }
                            }
                    }
    
                                    
                    content = result_g.slice(0, pos_list[0][0])
                    
                    for(i=0;i<schedule_list.length;i++){
                            content = content + get_date_label_html(schedule_list[i].id, schedule_list[i].match_str)
                            content = content + result_g.slice(schedule_list[i].start_pos+schedule_list[i].match_str.length, schedule_list[i].end_pos)
                    }
                    
                    $("#orig_syl").html(content.replace(/\n/gi,"<br>").replace(/\s{2,}/g, "&nbsp;&nbsp;&nbsp;&nbsp;"))
                    show_schedule();
                    adjust_spacing();
                    t=$.extend(true, [], schedule_list);
                    history_stack.push(t)
                    update_date_label()
						   
                   }});          

	/**
	 * Create tasks from document
	 */
	$('#create-task').click(function(e) {
		e.preventDefault();
		form = $('#task-creation-form');
		taskEle = $('.schedule_elem');
		form.append('<input type="hidden" name="task_count" value="' + taskEle.length + '" />');

		taskEle.each(function(index, value) {
			date = $('.schedule_elem_title', value).text().replace('Date:', '') + '/2011';
			objective = $('.sch_content', value).text();
			form.append('<input type="hidden" name="date_' + index + '" value="' + date + '" />');
			form.append('<input type="hidden" name="objective_' + index + '" value="' + objective + '" />');
		});

		console.log(form);
		$.ajax({
			url: '/task-bulk-add',
			type: 'post',
			cache: false,
			data: form.serialize(),
			success: function(response) {
			}
		});
	});
});
