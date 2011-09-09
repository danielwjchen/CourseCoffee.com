import pycurl
import StringIO

textbook = []
f=open("msu","w")

class Test:
    def __init__(self):
        self.content = ''
    def body_callback(self, buf):
        self.content += buf


url = "http://www.spartanbook.com/textbooks_xml.asp?control=campus&campus=46&term=81"
crl = pycurl.Curl()
t = Test()
crl.setopt(pycurl.URL, url)
crl.setopt(crl.WRITEFUNCTION, t.body_callback)
crl.perform()
temp= t.content
d =temp.split()
for elem in d:
    data=elem.split("=")
    dept_id_flag=0
    if "id" in elem:
        dept_ID = data[1].strip("\"")
        
    if "abrev" in elem:
        dept_ab = data[1].strip("\"")
    if "name" in elem:
        dept_name = data[1].strip("\"")
        
        dept_id_flag= 1
    if dept_id_flag==1:# and dept_ab=="CSE":
        #print "dept:",dept_ab,#dept_name#,dept_ID
        url="http://www.spartanbook.com/textbooks_xml.asp?control=department&dept="+dept_ID+"&term=81"
        crl = pycurl.Curl()
        t = Test()
        crl.setopt(pycurl.URL, url)
        crl.setopt(crl.WRITEFUNCTION, t.body_callback)
        crl.perform()
        temp= t.content
        c=temp.split()
        for elem1 in c:
            course_id_flag=0
            data1=elem1.split("=")
            if "id" in elem1:
                course_ID=data1[1].strip("\"")
                
            if "name" in elem1:
                course_name=data1[1].strip("\"")
                #print "---course:", course_name#,course_ID
                course_id_flag=1
            if course_id_flag==1:
                url="http://www.spartanbook.com/textbooks_xml.asp?control=course&course="+course_ID+"&term=81"
        
                crl = pycurl.Curl()
                t = Test()
                crl.setopt(pycurl.URL, url)
                crl.setopt(crl.WRITEFUNCTION, t.body_callback)
                crl.perform()
                temp= t.content
                s=temp.split()
                
                for elem2 in s:
                    section_id_flag=0
                    data2=elem2.split("=")
                    if "id" in elem2:
                        section_ID=data2[1].strip("\"")
                    if "name" in elem2:
                        section_name=data2[1].strip("\"")
                    if "instructor" in elem2:
                        section_instructor=data2[1].strip("\"")
                        #print "------section", section_name, section_instructor#,section_ID
                        section_id_flag=1
                    if section_id_flag==1:
                        url="http://www.spartanbook.com/textbooks_xml.asp?control=section&section="+section_ID
        
                        crl = pycurl.Curl()
                        t = Test()
                        crl.setopt(pycurl.URL, url)
                        crl.setopt(crl.WRITEFUNCTION, t.body_callback)
                        crl.perform()
                        temp= t.content
                        b= temp.split()
                        for elem3 in b:
                            if "Key" in elem3 and "src" in elem3:
                                elem31=elem3.split("=")
                                
                                elem311=elem31[2].split("&")
                                isbn = elem311[0]
                                #print dept_ID,dept_ab,dept_name,course_ID,course_name,section_ID,section_name, section_instructor, elem311[0]
                                #print "------isbn:",elem311[0]
                                if isbn != "":
                                    text = dept_ab+" "+course_name+" "+section_name+" "+isbn
                                    if text not in textbook:
                                        textbook.append(text)
                                        print text
                                        f.write(text+"\n")




                course_id_flag=0
        

        dept_id_flag=0
f.close()
