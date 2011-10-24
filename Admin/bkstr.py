#!/usr/bin/env python
"""

Query bkstr.com's website for class reading list. 

bkstr.com has the most strict security measure yet. It uses cookies to control
each session and limits the number and frequency of queries for each session. 

"""
import pycurl
import re
import StringIO
import urllib
import traceback
import json
import time
import Bookstore


class BKSTR:
    """Query data from bkstr.com
    
    to-do - Implement a generic parent class
    
    """

    def __init__(self):
        """default constructor"""
        self.params    = {}
        """firefox 7"""
        self.userAgent = "Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1"
        self.referer   = 'http://www.bkstr.com/CategoryDisplay/10001-9604-15453-1?demoKey=d'
        """URL to the store page for the requested college"""
        self.storeURL = 'http://www.bkstr.com/webapp/wcs/stores/servlet/StoreCatalogDisplay'
        """URL to page that conatins curriculum (subject, course, section) information"""
        self.curriculumURL = 'http://www.bkstr.com/webapp/wcs/stores/servlet/LocateCourseMaterialsServlet'
        """URL to page that conatins item (book title) information"""
        self.itemURL = 'http://www.bkstr.com/webapp/wcs/stores/servlet/CourseMaterialsResultsView'
        self.cookieFile  = 'cookies.txt'
        """Create log file for error handling"""
        self.errorLogFile = 'log'

    def getParam(self, name):
        """Get the value of parameter passed as part of the query"""
        return self.params[name]

    def setParam(self, name, value):
        """Set the value of parameter passed as part of the query"""
        self.params[name] = value

    def handleError(self, message):
        """Log error in file"""
        timestring = time.strftime("%y/%m/%d %H:%M", time.gmtime())
        log = timestring + " - " + message
        print log
        errorLog = open(self.errorLogFile, 'w+')
        errorLog.write(log)
        errorLog.close()

    def encodeRequest(self):
        """Encode params to url string"""
        return urllib.urlencode(self.params)

    def beginSession(self):
        """begin session with bkstr.com

        bkstr.com uses cookie to control session. This method is used to at the
        beginning and when session is revoked.
        
        """
        cookieFile = open(self.cookieFile, 'w+')
        request = self.storeURL + "?storeId=" + self.getParam('storeId')
        self.sendRequest(request)
        return
        

    def sendRequest(self, url):
        """Send request to site for data

        param string url

        return string result
            It could be in XML, HTML, or JSON depending on the site
        """
        retried  = 0
        retryMax = 5
        success  = False
        while retried < retryMax and success is False:
            try:
                curl = pycurl.Curl()
                curl.setopt(pycurl.USERAGENT, self.userAgent)
                curl.setopt(pycurl.URL, url)
                curl.setopt(pycurl.FOLLOWLOCATION, 1)
                curl.setopt(pycurl.MAXREDIRS, 500)
                curl.setopt(pycurl.COOKIEFILE, self.cookieFile) 
                curl.setopt(pycurl.COOKIEJAR, self.cookieFile) 
                curl.setopt(pycurl.REFERER, self.referer)
                stringIO = StringIO.StringIO()
                curl.setopt(pycurl.WRITEFUNCTION, stringIO.write)
                curl.perform()
                curl.close()
                result = stringIO.getvalue()
                stringIO.close()
                success = True
                """pause execution so we don't get caught"""
                time.sleep(5)
            except:
                retried = retried + 1
                self.handleError('curl failed')
                continue
            break

        if retried == retryMax and success is False:
            self.handleError('curl could not be completed for request - ' + url)
            return

        return result

    def setInstitution(self):
        """Set institution ids"""
        self.setParam('storeId', '15453')
        self.setParam('programId', '1607')
        self.setParam('termId', '100019123')
        self.setParam('divisionName', ' ')
        self.setParam('demoKey', 'd')
        self.setParam('_', '')

    def getSubjectList(self):
        """Get subject list"""
        self.setParam('requestType', 'DEPARTMENTS')
        request = self.curriculumURL + '?' + self.encodeRequest()
        text= self.sendRequest(request)
        text = text.replace("<script>parent.doneLoaded('", '')
        text = text.replace("')</script>", '')
        jsonResult = json.loads(text)
        result = []
        for key, value in jsonResult['data'][0].iteritems():
            result.append(key)

        return result

    def getCourseList(self, subject):
        """Get course list for a given subject."""
        self.setParam('requestType', 'COURSES')
        self.setParam('departmentName', subject)
        request = self.curriculumURL + '?' + self.encodeRequest()
        text= self.sendRequest(request)
        text = text.replace("<script>parent.doneLoaded('", '')
        text = text.replace("')</script>", '')
        jsonResult = json.loads(text)
        result = []
        for key, value in jsonResult['data'][0].iteritems():
            result.append(key)

        return result

    def getSectionList(self, subject, course):
        """Get section list for a given course."""
        self.setParam('requestType', 'SECTIONS')
        self.setParam('departmentName', subject)
        self.setParam('courseName', course)
        request = self.curriculumURL + '?' + self.encodeRequest()
        text= self.sendRequest(request)
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
        while tried < retryMax and success is not True:
            self.setParam('purpose', 'browse')
            self.setParam('catalogId', '10001')
            self.setParam('categoryId', '9604')
            self.setParam('requestType', 'SECTIONS')
            self.setParam('divisionDisplayName', ' ')
            self.setParam('departmentDisplayName', subject)
            self.setParam('courseDisplayName', course)
            self.setParam('sectionDisplayName', section)
            request =  self.itemURL + '?' + self.encodeRequest()
            text = self.sendRequest(request)
            if re.findall("cmCreateErrorTag", text):
                self.beginSession()
                tried += 1
            else:
                success = True


        if tried == retryMax and success is not True:
            self.handleError(subjct + " " + course + " " + section + " " + "FAILED")
                

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


    def querySite(self):
        """Query site for data"""
        return

bookStore  = BKSTR()
listWriter = Bookstore.ListWriter('EMICH_bkstr')
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
