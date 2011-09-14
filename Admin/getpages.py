import pycurl
import re
import StringIO
import urllib
import traceback

resultFile = open('UM_book', 'w')
resultFile.write("subject course_id session_id isbn required title\n")

catalogId = '10001'
storeId = '28052'
campusId = '26266164'
termId = '46957025'
term = 'F11'
prefix = 'umichigan'

user_agent = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.220 Safari/535.1"

TBProcessDropdown = 'http://' + prefix + '.bncollege.com/webapp/wcs/stores/servlet/TextBookProcessDropdownsCmd?campusId='

count = 24
num = 0
request = 'http://' + prefix + '.bncollege.com/webapp/wcs/stores/servlet/TBListView?catalogId=' + catalogId+ '&storeId=' + storeId + '&termMapping=Y&langId=-1&courseXml=%3C?xml%20version=%221.0%22%20encoding=%22UTF-8%22?%3E%3Ctextbookorder%3E%3Cschool%20id=%22' + campusId + '%22%20/%3E%3Ccourses%3E'
page_num = 0

try:
    url = TBProcessDropdown + campusId + '&termId=' + termId + '&deptId=&courseId=&sectionId=&storeId=' + storeId + '&catalogId=' + catalogId + '&langId=-1&dojo.transport=xmlhttp&dojo.preventCache=0'
    b = StringIO.StringIO()
    c = pycurl.Curl()
    c.setopt(pycurl.URL, url)
    c.setopt(pycurl.WRITEFUNCTION, b.write)
    c.setopt(pycurl.USERAGENT,user_agent)
    c.setopt(pycurl.FOLLOWLOCATION, 1)
    c.setopt(pycurl.MAXREDIRS, 500)
    c.perform()
    c.close()
    text= b.getvalue()
    b.close()

    text = text[text.find('</option>'):]

    deptIdList = re.findall(r"'.*'",text)
    for x in range(len(deptIdList)):
        deptIdList[x] = deptIdList[x][1:-1]

    deptList = re.findall(r">.*</",text)
    for x in range(len(deptList)):
        deptList[x] = deptList[x][1:-2]

    if(len(deptIdList) != len(deptList)):
        print text


    #request example http://umichigan.bncollege.com/webapp/wcs/stores/servlet/TBListView?catalogId=10001&storeId=28052&termMapping=Y&langId=-1&courseXml=<?xml version="1.0" encoding="UTF-8"?><textbookorder><school id="26266164" /><courses><course dept="AAPTIS" num="150" sect="006" term="F11" /></courses></textbookorder>

    for i in range(len(deptIdList)):
        deptId = deptIdList[i]
        dept = deptList[i]

        url = TBProcessDropdown + campusId + '&termId=' + termId + '&deptId=' + deptId + '&courseId=&sectionId=&storeId=' + storeId + '&catalogId=' + catalogId + '&langId=-1&dojo.transport=xmlhttp&dojo.preventCache=0'
        b = StringIO.StringIO()
        c = pycurl.Curl()
        c.setopt(pycurl.URL, url)
        c.setopt(pycurl.WRITEFUNCTION, b.write)
        c.setopt(pycurl.USERAGENT, user_agent)
        c.setopt(pycurl.FOLLOWLOCATION, 1)
        c.setopt(pycurl.MAXREDIRS, 500)
        c.perform()
        c.close()
        text= b.getvalue()
        b.close()

        text = text[text.find('</option>'):]

        courseIdList = re.findall(r"'.*'",text)
        for x in range(len(courseIdList)):
            courseIdList[x] = courseIdList[x][1:-1]

        courseList = re.findall(r">.*</",text)
        for x in range(len(courseList)):
            courseList[x] = courseList[x][1:-2]

        if(len(courseIdList) != len(courseList)):
            print text


        for j in range(len(courseIdList)):
            courseId = courseIdList[j]
            course = courseList[j]

            url = TBProcessDropdown + campusId + '&termId=' + termId + '&deptId=' + deptId + '&courseId=' + courseId + '&sectionId=&storeId=' + storeId + '&catalogId=' + catalogId + '&langId=-1&dojo.transport=xmlhttp&dojo.preventCache=0'
            b = StringIO.StringIO()
            c = pycurl.Curl()
            c.setopt(pycurl.URL, url)
            c.setopt(pycurl.WRITEFUNCTION, b.write)
            c.setopt(pycurl.USERAGENT, user_agent)
            c.setopt(pycurl.FOLLOWLOCATION, 1)
            c.setopt(pycurl.MAXREDIRS, 500)
            c.perform()
            c.close()
            text= b.getvalue()
            b.close()
            
            text = text[text.find('</option>'):]

            sectionIdList = re.findall(r"'.*'",text)
            for x in range(len(sectionIdList)):
                sectionIdList[x] = sectionIdList[x][1:-1]

            sectionList = re.findall(r">.*</",text)
            for x in range(len(sectionList)):
                sectionList[x] = sectionList[x][1:-2]

            if(len(sectionIdList) != len(sectionList)):
                print text
            
            for k in range(len(sectionList)):
                section = sectionList[k]
                addStr = '%3Ccourse%20dept=%22'+ dept + '%22%20num=%22' + course + '%22%20sect=%22' + section + '%22%20term=%22' + term +'%22%20/%3E' 
                request = request + addStr
                num = num+1
                if num >= count:                 
                    request = request + '%3C/courses%3E%3C/textbookorder%3E'
                    url = request
                    b = StringIO.StringIO()
                    c = pycurl.Curl()
                    c.setopt(pycurl.URL, url)
                    c.setopt(pycurl.WRITEFUNCTION, b.write)
                    c.setopt(pycurl.USERAGENT, user_agent)
                    c.setopt(pycurl.FOLLOWLOCATION, 1)
                    c.setopt(pycurl.MAXREDIRS, 500)
                    if ((page_num == 39) or (page_num == 72)):
                        
                        c.perform()
                        c.close()
                        text= b.getvalue()
                        b.close()
    
                        pageName = str(page_num) + '.htm'
                        pageWrite = open(pageName, 'w')
                        pageWrite.write(text)
                        pageWrite.close()
                        print request

                    num = 0
                    page_num = page_num+1
                    request = 'http://' + prefix + '.bncollege.com/webapp/wcs/stores/servlet/TBListView?catalogId=' + catalogId+ '&storeId=' + storeId + '&termMapping=Y&langId=-1&courseXml=%3C?xml%20version=%221.0%22%20encoding=%22UTF-8%22?%3E%3Ctextbookorder%3E%3Cschool%20id=%22' + campusId + '%22%20/%3E%3Ccourses%3E'

    request = request + '%3C/courses%3E%3C/textbookorder%3E'
    url = request
    b = StringIO.StringIO()
    c = pycurl.Curl()
    c.setopt(pycurl.USERAGENT, user_agent)
    c.setopt(pycurl.URL, url)
    c.setopt(pycurl.WRITEFUNCTION, b.write)

    c.setopt(pycurl.FOLLOWLOCATION, 1)
    c.setopt(pycurl.MAXREDIRS, 500)
    c.perform()
    c.close()
    text= b.getvalue()
    b.close()

    pageName = str(page_num) + '.htm'
    pageWrite = open(pageName, 'w')
    pageWrite.write(text)
    pageWrite.close()

    num = 0
    page_num = page_num+1
    request = 'http://' + prefix + '.bncollege.com/webapp/wcs/stores/servlet/TBListView?catalogId=' + catalogId+ '&storeId=' + storeId + '&termMapping=Y&langId=-1&courseXml=%3C?xml%20version=%221.0%22%20encoding=%22UTF-8%22?%3E%3Ctextbookorder%3E%3Cschool%20id=%22' + campusId + '%22%20/%3E%3Ccourses%3E'

except:
    print dept
    print request
    traceback.print_exc()
    pass
resultFile.close()
