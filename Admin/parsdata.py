import pycurl
import re
import StringIO
import traceback
import os
import fnmatch


resultFile = open('UM_book', 'w')

resultFile.write("subject course_id session_id isbn required title\n")

os.chdir(r'C:\Python25\umich'); #change working directory to this directory


for file in os.listdir('.'):
    if fnmatch.fnmatch(file, '*.htm'): # read each file that has the name *.htm
        f = open(file, 'r'); # open the file and read
        text = f.read();
        
        beg = []
        end = []
        for match in re.finditer('<div class="sectionHeading', text):
            beg.append(match.start())
        beg = beg[:-1]
        for match in re.finditer('AS  check if required at end', text):
            end.append(match.start())

        #split segment text contain section
        sectionText = []
        for i in range(len(beg)):
            sectionText.append(text[beg[i]-5:end[i]])

        #deal with each section
        for i in range(len(sectionText)):
            text = sectionText[i]
            text = re.sub(r'</?\w+[^>]*>',' ', text)
            text = re.sub(r'\s',' ', text)
            text = text.strip()
            text = re.split(r'[\s]{3,}', text)
            subject = text[1]
            course = text[2]
            section = text[3]
            
            require = []
            isbn = []
            for j in range(len(text)):
                if text[j] == 'ISBN:':
                    isbn.append(text[j+1])
                if text[j] == 'REQUIRED':
                    require.append('Yes')
                if text[j] == 'REQUIRED PACKAGE':
                    require.append('Yes')
                if text[j] == 'PACKAGE COMPONENT':
                    require.append('No')
                if text[j] == 'RECOMMENDED':
                    require.append('No')
                if text[j] == 'BOOKSTORE RECOMMENDED':
                    require.append('No')
                if text[j] == 'RECOMMENDED PACKAGE':
                    require.append('No')

            if len(isbn) == 0:
                pass
            else:
                for j in range(len(isbn)):
                    record = subject + ' ' + course + ' '+ section + ' ' + isbn[j] + ' ' + require[j] + ' NULL\n'
                    resultFile.write(record)

            #test isbn and require order
            #if len(isbn) != len(require):
                #print subject, course, section, len(isbn), len(require)

             
    if 0:
        break

resultFile.close( )
