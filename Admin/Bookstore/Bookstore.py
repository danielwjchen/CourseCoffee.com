#!/usr/bin/env python
"""Base class for bookstore scripts

This script provides methods that can be reused to fetch item/book information 
for curriculums offered by an institution

"""
import pycurl
import re
import StringIO
import urllib
import traceback
import json
import time
from xml.sax.saxutils import escape

class BaseClass(object):
    def __init__(self):
        """default constructor"""
        self.params    = {}
        """firefox 7"""
        self.userAgent = "Mozilla/5.0 (Windows NT 6.1; rv:7.0.1) Gecko/20100101 Firefox/7.0.1"
        """Create cookie file"""
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
        errorLog = open(self.errorLogFile, 'a+')
        errorLog.write(log)
        errorLog.close()

    def encodeRequest(self):
        """Encode params to url string"""
        return urllib.urlencode(self.params)

    def sendRequest(self, url, pauseTime=5, retried=0, retryMax=5, success=False):
        """Send request to site for data

        param string url

        return string result
            It could be in XML, HTML, or JSON depending on the site
        """
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
                time.sleep(pauseTime)
            except:
                retried = retried + 1
                self.handleError('curl failed')
                traceback.print_exc()
                continue
            break

        if retried == retryMax and success is False:
            self.handleError('curl could not be completed for request - ' + url)
            return

        return result

    def setInstitution(self):
        """Set institution info"""
        raise NotImplementedError()

    def getSubjectList(self):
        """Get subject list."""
        raise NotImplementedError()

    def getCourseList(self, subject):
        """Get course list for a given subject."""
        raise NotImplementedError()

    def getSectionList(self, subject, course):
        """Get section list for a given course."""
        raise NotImplementedError()

    def getItemList(self, subject, course, section):
        """Get item list for a given section."""
        raise NotImplementedError()

class ListWriter:
    """Write item list to file in plain format

    For now, things are stored in plain text. Maybe we should write this into 
    the database directly, or store in different file format such as XML or
    JSON.
    
    """

    def __init__(self, fileName):
        """default constructor
        
        By default a file is opened for writting on instantiation with the first 
        line specifing column names.


        """
        self.listFile = open(fileName, 'w+')
        self.fileFormat = fileFormat
        self.listFile.write("subject course session isbn required title\n")

    def __del__(self):
        """default destructor"""
        self.listFile.close()

    def write(self, subject, course, section, isbn, required, title):
        """write data to file in plain format"""
        entry = subject + ' ' + course + ' ' + section + ' ' + isbn + ' ' + required + ' ' + title + "\n"
        print entry
        self.listFile.write(entry)


class XMLWriter:
    """Write item list to file in a XML format

    For now, things are stored in plain text. Maybe we should write this into 
    the database directly, or store in different file format such as XML or
    JSON.
    
    """

    def __init__(self, fileName):
        """default constructor
        
        By default a file is opened for writting on instantiation with the first 
        line specifing column names.


        """
        self.listFile = open("XML/" + fileName + '.xml', 'w+')
        self.listFile.write("<?xml version='1.0'?>\n")
        self.listFile.write("<items>\n")

    def __del__(self):
        """default destructor"""
        self.listFile.write("</items>")
        self.listFile.close()

    def write(self, subject, course, section, isbn='', required='', title=''):
        """write data to file"""
        entry = "\t<item "\
            "sub_abbr=\"" + escape(subject) + "\" "\
            "crs=\"" + escape(course) + "\" "\
            "sec=\"" + escape(section) + "\" "\
            "isbn=\"" + isbn + "\" "\
            "required=\"" + required + "\" "\
            "title=\"" + escape(title) + "\" ></item>\n"
        print entry 
        self.listFile.write(entry)
