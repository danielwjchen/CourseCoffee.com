/**
 *  editor version 2.0
 *  automatically process documents, tag dates and assignments
 **/
$P.ready(function(){
            /*    global variables    */
            var schedule_list = [];
            var history_stack = [];
            var redo_stack = [];
            var history_pointer = 0;
            var result_g = "";
            
            var avg = 0; //average increasement
            var min_var = -1;
            var reg_idx = -1;
            var auto_fill_start_idx = 0; 
                
            var curr_date = new Date();
            var curr_year = curr_date.getFullYear();
            var curr_year = 2000;
            var month=["january","february","march","april","may","june","july","august","september","october","november","december","jan","feb","mar",  "apr",  "may","jun", "jul", "aug","sep","sept","oct",       "nov",     "dec","week"];
            var reg=[];
                reg[0]=/((0?[1-9])|(1[0-2])){1}\/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}(\/(\d{4}|\d{2}))?(?=[^\/0-9])/gi;	// mm/dd/yy
                reg[1]=/((0?[1-9])|(1[0-2])){1}-((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}(-(\d{4}|\d{2}))?(?=[^\/0-9])/gi;	// mm-dd-yy or mm-dd
                reg[2]=/(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}( *|,|.){0,2}((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}(?=[^0-9])(\W+\d{4})?/gi
                reg[3]=/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1}-(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}(\W+\d{4})?/gi
                reg[4]=/((1[0-9])|(2[0-9])|(3[0-1])|(0?[1-9])){1} (january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec){1}(\W+\d{4})?/gi
                reg[5]=/^[^0-9](0?[1-9]|1[0-2]){1}-(1[0-9]|2[0-9]|3[0-1]|0?[1-9]){1}(?=[^0-9])/gi;
                reg[6]=/week\W{0,2}\d{1,2}/gi;

            var fil_list = [/mon/gi, /tue/gi, /wed/gi, /thu/gi, /thur/gi, /fri/gi, /sat/gi, /sun/gi,/week/gi, /m/gi , /t/gi, /w/gi, /r/gi, /f/gi];
            
            
            var valid_month_min= 0;
            var valid_month_max = 11;
            //var valid_month_min= 3;
            //var valid_month_max = 8;
     
            var schedule_list_orig_len = 0
            var orig_syl_content = ""
            var orig_html = "";
	    var pre_text = ""; // text before real date

            
            /* html elements e.g. span, tables for dynamically loading*/
            function get_date_label(sid_str, date_content_str){
                    var date_label_string =  "<span class='date_label' sid='" + sid_str + "'><span>" + date_content_str + "</span><a href='#' sid='SID_STR' class='del_date_label' >x</a></span>" ;
                    return date_label_string;
            }
            function get_schedule(){
                    var schedule_elem_string = "<div sid='SID_STR' class='schedule_elem'>\
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
                    return schedule_elem_string;
            }
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
                            result = $.trim(result);                                 // result: raw syllabus
                            result = result.replace(/\r\n/gi, "\n");                 // replace \r\n with \n: \r\n is new line in window
                            result = result.replace(/\r/gi, "\n");                   // replace \r with \n: \r is new line in Mac OS 
                            result = result.replace(/\n{3,}/gi, "\n\n");             // all multiple empty lines (>3) will become 2 empty lines
                            result_g = result;
                            result_g = result_g.replace(/&nbsp;/gi, " ").replace(/<br>/gi, "\n"); 
                            $(".content").html();
                            

            }}); //$.ajax({


}); //$P.ready(function() 
