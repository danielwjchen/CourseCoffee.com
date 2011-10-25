#!/usr/bin/env python
"""

Query bkstr.com's website for class reading list. 

bkstr.com has the most strict security measure yet. It uses cookies to control
each session and limits the number and frequency of queries for each session. 

"""
import re
import StringIO
import urllib
import traceback
import json
import time
import Bookstore


class BKSTR(Bookstore.BaseClass):
    """Query data from bkstr.com
    
    to-do - Implement a generic parent class
    
    """

    def __init__(self):
        """Extend BaseClass::__init__"""
        super(BKSTR, self).__init__()
        self.referer   = 'http://www.bkstr.com/CategoryDisplay/10001-9604-15453-1?demoKey=d'
        """URL to the store page for the requested college"""
        self.storeURL = 'http://www.bkstr.com/webapp/wcs/stores/servlet/StoreCatalogDisplay'
        """URL to page that conatins curriculum (subject, course, section) information"""
        self.curriculumURL = 'http://www.bkstr.com/webapp/wcs/stores/servlet/LocateCourseMaterialsServlet'
        """URL to page that conatins item (book title) information"""
        self.itemURL = 'http://www.bkstr.com/webapp/wcs/stores/servlet/CourseMaterialsResultsView'

    def beginSession(self):
        """begin session with bkstr.com

        bkstr.com uses cookie to control session. This method is used to at the
        beginning and when session is revoked.
        
        """
        cookieFile = open(self.cookieFile, 'w+')
        request = self.storeURL + "?storeId=" + super(BKSTR, self).getParam('storeId')
        super(BKSTR, self).sendRequest(request)
        return

    def setInstitution(self):
        """Set institution ids"""
        super(BKSTR, self).setParam('storeId', '15453')
        super(BKSTR, self).setParam('programId', '1607')
        super(BKSTR, self).setParam('termId', '100019123')
        super(BKSTR, self).setParam('divisionName', ' ')
        super(BKSTR, self).setParam('demoKey', 'd')
        super(BKSTR, self).setParam('_', '')

    def getSubjectList(self):
        """Get subject list"""
        super(BKSTR, self).setParam('requestType', 'DEPARTMENTS')
        request = self.curriculumURL + '?' + super(BKSTR, self).encodeRequest()
        text = super(BKSTR, self).sendRequest(request)
        text = text.replace("<script>parent.doneLoaded('", '')
        text = text.replace("')</script>", '')
        jsonResult = json.loads(text)
        result = []
        for key, value in jsonResult['data'][0].iteritems():
            result.append(key)

        return result

    def getCourseList(self, subject):
        """Get course list for a given subject."""
        super(BKSTR, self).setParam('requestType', 'COURSES')
        super(BKSTR, self).setParam('departmentName', subject)
        request = self.curriculumURL + '?' + super(BKSTR, self).encodeRequest()
        text = super(BKSTR, self).sendRequest(request)
        text = text.replace("<script>parent.doneLoaded('", '')
        text = text.replace("')</script>", '')
        jsonResult = json.loads(text)
        result = []
        for key, value in jsonResult['data'][0].iteritems():
            result.append(key)

        return result

    def getSectionList(self, subject, course):
        """Get section list for a given course."""
        super(BKSTR, self).setParam('requestType', 'SECTIONS')
        super(BKSTR, self).setParam('departmentName', subject)
        super(BKSTR, self).setParam('courseName', course)
        request = self.curriculumURL + '?' + super(BKSTR, self).encodeRequest()
        text = super(BKSTR, self).sendRequest(request)
        text = text.replace("<script>parent.doneLoaded('", '')
        text = text.replace("')</script>", '')
        jsonResult = json.loads(text)
        result = []
        for key, value in jsonResult['data'][0].iteritems():
            result.append(key)

        return result

    def getItemList(self, subject, course, section):
        """Get item list for a given section.

        It's unclear what some of the params stand for and what the value means.
        For example, catalogId itself is pretty straight, but the value 10001 is 
        rather crytic. My guess is they start the serial at 10000, and 1 is the 
        first entry, which is books. On the other hand, I have no clue categoryId
        works.

        To make things even worse, bkstr.com seem to use javascript to format 
        result, and the HTML output from server is so badly formated, I feel
        obligated to make fun of their interns/slaves or whoever did their site.

        """
        tried = 0
        retryMax = 5
        success = False
        super(BKSTR, self).setParam('purpose', 'browse')
        super(BKSTR, self).setParam('catalogId', '10001')
        super(BKSTR, self).setParam('categoryId', '9604')
        super(BKSTR, self).setParam('requestType', 'SECTIONS')
        super(BKSTR, self).setParam('divisionDisplayName', ' ')
        super(BKSTR, self).setParam('departmentDisplayName', subject)
        super(BKSTR, self).setParam('courseDisplayName', course)
        super(BKSTR, self).setParam('sectionDisplayName', section)
        request =  self.itemURL + '?' + super(BKSTR, self).encodeRequest()
        while tried < retryMax and success is not True:
            text = super(BKSTR, self).sendRequest(request)
            if not text or re.findall(r"cmCreateErrorTag", text):
                self.beginSession()
                tried += 1
            else:
                success = True


        if tried == retryMax and success is not True:
            super(BKSTR, self).handleError(subjct + " " + course + " " + section + " " + "FAILED")
                

        """

        bkstr.com uses javascript to poppulate their content. That's why we 
        can't use python's DOM library. Luckily, The javascript function looks 
        looks like this:

        cmCreateProductviewTag("Course Materials Results Page", "59777568","Reading & Writing: The College Experience","15453_9604","15453","N");

        We utilize this knowledge and use regular expression to get the stuff we need.

        """
        match = re.findall(
                r"cmCreateProductviewTag\(\"Course Materials Results Page\".+", 
                text)
        result = [];
        for item in match:
           item = re.sub(r"cmCreateProductviewTag\(\"Course Materials Results Page\",\s?\"[0-9]+\",\"", "", item)
           item = re.sub(r"\",\s?\"\w+\",\"\w+\",\s?\"\w?\"\s?\)\;$", "", item)
           result.append(item)
        return result


bookStore  = BKSTR()
listWriter = Bookstore.XMLWriter('EMICH_bkstr')
bookStore.setInstitution()
bookStore.beginSession()
subjectList = bookStore.getSubjectList()
for subject in subjectList:
    courseList = bookStore.getCourseList(subject)
    for course in courseList:
        sectionList = bookStore.getSectionList(subject, course)
        for section in sectionList:
            itemList = bookStore.getItemList(subject, course, section)
            if not itemList:
                listWriter.write(subject, course, section, 'NULL', 'NULL', 'NULL')
            else:
                for item in itemList:
                    """
                    Because bkstr.com only has book title.... we leave other 
                    fields blank
                    """
                    listWriter.write(subject, course, section, 'NULL', 'NULL', item)
