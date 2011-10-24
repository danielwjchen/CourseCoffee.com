#!/usr/bin/env python
"""

Query Neebo Affiliate's (formally known as CampusHub) website for class reading 
list. 

Neebo.com seems to be the reincarnation of Nebraska Book Company (nebook.com), 
which files for bankrupcywhich in early 2011. CampusHub (thecampushub.com) is 
the software that hosts affiliates of Nebraska Book Company's online store. 

Neebo.com now hosts its own online bookstore. It is unknown how long it would
take for all Neebo's affiliate to switch, but it could be soon.

"""

import Bookstore
import xml.dom.minidom

class NeeboAffiliate(Bookstore.BaseClass):
    """Query neebo affiliate for class reading list"""
    def __init__(self):
        """Extend bookstore::__init__"""
        super(NeeboAffiliate, self).__init__()
        """Neebo affiliates each  have their own website"""
        self.AffiliateURL = ""
        self.referer = "http://www.textbookunderground.com"

    def setInstitution(self):
        """Set institution info

        Affiliate url should be changed accordingly, e.g. 
        http://www.spartanbook.com/

        """
        """MSU Fall 2011"""
        #self.AffiliateURL = "http://http://www.spartanbook.com/"
        #self.setParams('term', '81')
        """Wisconsin Fall 2011"""
        self.AffiliateURL = "http://www.textbookunderground.com"
        super(NeeboAffiliate, self).setParam('term', '3108')
        super(NeeboAffiliate, self).setParam('campus', '73')
        """
        Neebo affiliates all use the same software for their website, and 
        /textbooks_xml.asp seems to be the request handler
        """
        self.requestHandler = self.AffiliateURL + "/" + "textbooks_xml.asp"

    def getSubjectList(self):
        """Get subject list."""
        request = self.requestHandler + "?" + "control=campus" + "&" + self.encodeRequest()
        text = super(NeeboAffiliate, self).sendRequest(request)
        if not text:
            return

        dom = xml.dom.minidom.parseString(text)
        subjects = dom.getElementsByTagName("department")
        result = {}
        for node in subjects:
            result[node.getAttribute('abrev').strip()] = node.getAttribute('id').strip()

        return result

    def getCourseList(self, subjectID):
        """Get course list for a given subject."""
        super(NeeboAffiliate, self).setParam('dept', subjectID)
        request = self.requestHandler + "?" + "control=department" + "&" + self.encodeRequest()
        text = super(NeeboAffiliate, self).sendRequest(request)
        if not text:
            return

        dom = xml.dom.minidom.parseString(text)
        courses = dom.getElementsByTagName("course")
        result = {}
        for node in courses:
            result[node.getAttribute('name').strip()] = node.getAttribute('id').strip()

        return result

    def getSectionList(self, courseID):
        """Get section list for a given course."""
        super(NeeboAffiliate, self).setParam('course', courseID)
        request = self.requestHandler + "?" + "control=course" + "&" + self.encodeRequest()
        text = super(NeeboAffiliate, self).sendRequest(request)
        if not text:
            return

        dom = xml.dom.minidom.parseString(text)
        sections = dom.getElementsByTagName("section")
        result = {}
        for node in sections:
            result[node.getAttribute('name').strip()] = node.getAttribute('id').strip()

        return result

    def getItemList(self, sectionID):
        """Get item list for a given section."""
        super(NeeboAffiliate, self).setParam('section', sectionID)
        request = self.requestHandler + "?" + "control=section" + "&" + self.encodeRequest()
        text = super(NeeboAffiliate, self).sendRequest(request)
        if not text:
            return

        """This section of code is copy&paste from Cheng's spartanbook.py."""
        result = []
        b = text.split()
        for elem3 in b:
            if "Key" in elem3 and "src" in elem3:
                elem31 = elem3.split("=")
                elem311 = elem31[2].split("&")
                isbn = elem311[0]
                result.append(isbn)


        return result

bookstore  = NeeboAffiliate()
listWriter = Bookstore.ListWriter('WISC_Neebo')
bookstore.setInstitution()
subjectList = bookstore.getSubjectList()
for sub, subjectID in subjectList.iteritems():
    courseList = bookstore.getCourseList(subjectID)
    for crs, courseID in courseList.iteritems():
        sectionList = bookstore.getSectionList(courseID)
        for sec, sectionID in sectionList.iteritems():
            itemList = bookstore.getItemList(sectionID)
            if not itemList:
                listWriter.write(sub, crs, sec, 'NULL', 'NULL', 'NULL')
            else:
                for isbn in itemList:
                    listWriter.write(sub, crs, sec, isbn, 'NULL', 'NULL')
