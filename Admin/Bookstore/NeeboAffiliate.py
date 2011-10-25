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

import re
import Bookstore
import xml.dom.minidom
from BeautifulSoup import BeautifulSoup

class NeeboAffiliate(Bookstore.BaseClass):
    """Query neebo affiliate for class reading list"""
    def __init__(self):
        """Extend BaseClass::__init__"""
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
        """Nebraska Fall 2011"""
        self.AffiliateURL = "http://www.nebraskabookstore.com"
        super(NeeboAffiliate, self).setParam('term', '96')
        super(NeeboAffiliate, self).setParam('campus', '54')
        """Wisconsin Fall 2011"""
        #self.AffiliateURL = "http://www.textbookunderground.com"
        #super(NeeboAffiliate, self).setParam('term', '3108')
        #super(NeeboAffiliate, self).setParam('campus', '73')
        """Central Michigan Fall 2011"""
        #self.AffiliateURL = "http://www.cmubookstore.com/"
        #super(NeeboAffiliate, self).setParam('term', '30')
        #super(NeeboAffiliate, self).setParam('campus', '28')
        """
        Neebo affiliates all use the same software for their website, and 
        /textbooks_xml.asp seems to be the request handler
        """
        self.requestHandler = self.AffiliateURL + "/" + "textbooks_xml.asp"

    def getSubjectList(self):
        """Get subject list."""
        request = self.requestHandler + "?" + "control=campus" + "&" + self.encodeRequest()
        text = super(NeeboAffiliate, self).sendRequest(request, 1)
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
        text = super(NeeboAffiliate, self).sendRequest(request, 1)
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
        text = super(NeeboAffiliate, self).sendRequest(request, 1)
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
        text = super(NeeboAffiliate, self).sendRequest(request, 1)
        if not text:
            return

        result = []
        detailMatch = re.findall(r"(<td class=\"book-desc\">.+</td>)", text)
        for detail in detailMatch:
            soup = BeautifulSoup(detail)
            isbn = str(soup.find("span", {"class": "isbn"}))
            if isbn:
                isbn = isbn.replace("<span class=\"isbn\">", "")
                isbn = isbn.replace("</span>", "")
                isbn = isbn.replace("None", "")

            title = str(soup.find("span", {"class": "book-title"}))
            if title:
                title = title.replace("<span class=\"book-title\">", "")
                title = title.replace("</span>", "")
                title = title.replace("No Text Required", "")

            result.append({"title" : title, "isbn" : isbn})

        return result

bookstore  = NeeboAffiliate()
listWriter = Bookstore.XMLWriter('NEBRASKA_Neebo')
#listWriter = Bookstore.XMLWriter('WISC_Neebo')
#listWriter = Bookstore.XMLWriter('CMU_Neebo')
bookstore.setInstitution()
subjectList = bookstore.getSubjectList()
for sub, subjectID in subjectList.iteritems():
    courseList = bookstore.getCourseList(subjectID)
    for crs, courseID in courseList.iteritems():
        sectionList = bookstore.getSectionList(courseID)
        for sec, sectionID in sectionList.iteritems():
            itemList = bookstore.getItemList(sectionID)
            if not itemList:
                listWriter.write(sub, crs, sec)
            else:
                for item in itemList:
                    listWriter.write(
                            sub, crs, sec, item["isbn"], "", item["title"])
