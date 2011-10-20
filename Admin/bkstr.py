# @file
# Fetch item/book information for curriculums offered by an institution
#
# @author 
# Daniel Chen <daniel@coursecoffee.com>
import pycurl
import re
import StringIO
import urllib
import traceback
import json

BookListFile = 'EMICH_BOOK'
resultFile = open(BookListFile, 'w')
resultFile.write("subject course session isbn required title\n")

requestType  = 'DEPARTMENTS'
storeId      = '15453'
demoKey      = 'd'
programId    = '1607'
termId       = '100019123'
divisionName = ''

#
# Query data from bkstr.com
#
# @to-do
# Implement a generic parent class
#
class BKSTR:
    def __init__(self):
        self.params = {
                'requestType': '',
                'storeId': '',
                'demoKey': '',
                'programId': '',
                'termId': '',
                'divisionName': '',
                'courseName': '',
                '_': ''}
        self.params = {}
        self.userAgent = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.220 Safari/535.1"
        self.referer = 'http://www.bkstr.com/CategoryDisplay/10001-9604-15453-1?demoKey=d'
        self.url = 'http://www.bkstr.com/webapp/wcs/stores/servlet/LocateCourseMaterialsServlet'
        self.cookieFile = 'cookies.txt'
        self.cookieValue = 'cmTPSet=Y; WC_SESSION_ESTABLISHED=true; JSESSIONID=0000Bg70PUl1gxYIvX4jxG1g0YJ:15fkes6rv; TSde3575=14c11e8b4a0e563173ffa6fd94e20c5557be28f2f8c96af54e9f9987'

    #
    # Get the value of parameter passed as part of the query
    #
    # @param string name
    #
    def getParam(self, name):
        return self.params[name]

    #
    # Set the value of parameter passed as part of the query
    #
    # @param string name
    # @param string value
    #
    def setParam(self, name, value):
        self.params[name] = value

    def encodeRequest(self):
        return urllib.urlencode(self.params)

    #
    # Send request to site for data
    #
    # @param string url
    #
    # @return string result
    #   It could be in XML, HTML, or JSON depending on the site
    #
    def sendRequest(self, url):
        curl = pycurl.Curl()
        curl.setopt(pycurl.USERAGENT, self.userAgent)
        curl.setopt(pycurl.URL, url)
        curl.setopt(pycurl.FOLLOWLOCATION, 1)
        curl.setopt(pycurl.MAXREDIRS, 500)
        curl.setopt(pycurl.COOKIEFILE, self.cookieFile) 
        curl.setopt(pycurl.COOKIEJAR, self.cookieFile) 
        curl.setopt(pycurl.COOKIE, self.cookieValue)
        curl.setopt(pycurl.REFERER, self.referer)
        stringIO = StringIO.StringIO()
        curl.setopt(pycurl.WRITEFUNCTION, stringIO.write)
        curl.perform()
        curl.close()
        result = stringIO.getvalue()
        stringIO.close()
        return result

    #
    # Get subject list
    #
    # @return list result
    #   returns a list of subject abbreviation
    #   
    def getSubjectList(self):
        self.setParam('requestType', 'DEPARTMENTS')
        self.setParam('storeId', '15453')
        self.setParam('demoKey', 'd')
        self.setParam('programId', '1607')
        self.setParam('termId', '100019123')
        self.setParam('divisionName', ' ')
        self.setParam('_', '')
        request = self.url + '?' + self.encodeRequest()
        text= self.sendRequest(request)
        text = text.replace("<script>parent.doneLoaded('", '')
        text = text.replace("')</script>", '')
        jsonResult = json.loads(text)
        result = []
        for key, value in jsonResult['data'][0].iteritems():
            result.append(key)

        return result

    #
    # Get course list
    #
    # @return list result
    #   returns a list of course code
    #   
    def getCourseList(self):
        return

    #
    # Get section list
    #
    # @return list result
    #   returns a list of section code
    #   
    def getSectionList(self):
        return

    #
    # Get item list
    #
    # @return list result
    #   returns a list of item
    #   
    def getItemList(self):
        return

    #
    # Query site for data
    #
    def querySite():
        return

bookStore = BKSTR()
result = bookStore.getSubjectList()
print result
