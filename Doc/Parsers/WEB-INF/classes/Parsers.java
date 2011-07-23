/*
 * Parser.java
 *
 * Author: Xin Feng
 * Date: 6/4/2011
 */

import java.util.*;
import java.io.*;
import java.net.*;

import javax.servlet.*;
import javax.servlet.http.*;

import java.util.concurrent.*;

import gate.*;
import gate.creole.*;
import gate.util.*;
import gate.corpora.RepositioningInfo;


import org.apache.commons.fileupload.*;
import org.apache.commons.fileupload.disk.*;
import org.apache.commons.fileupload.servlet.*;

public class Parsers extends HttpServlet {
	private static boolean gateInited = false;
	private static int POOL_SIZE = 5;

	private BlockingQueue<SerialAnalyserController> pool;
 
	public void init() throws ServletException {
		if(!gateInited) {
			try {
				ServletContext ctx = getServletContext();  
				// use /path/to/your/webapp/WEB-INF as gate.home  
				File gateHome = new File(ctx.getRealPath("/WEB-INF"));  
				
				Gate.setGateHome(gateHome);  
				// thus webapp/WEB-INF/plugins is the plugins directory, and  
				// webapp/WEB-INF/gate.xml is the site config file.  
				// Use webapp/WEB-INF/user-gate.xml as the user config file, to avoid  
				// confusion with your own user config.  
				Gate.setUserConfigFile(new File(gateHome, "user-gate.xml"));  
				
				Gate.init();  

        			// load plugins
        			Gate.getCreoleRegister().registerDirectories(ctx.getResource("/WEB-INF/plugins/ANNIE"));

				gateInited = true;  
			}
			catch(Exception ex) {
				throw new ServletException("Exception initialising GATE", ex);
			}
		}

		if(gateInited) {
			try {
				FeatureMap params = Factory.newFeatureMap();

				//Create PRs
				//doc annotdelete
				ProcessingResource annotDelete = (ProcessingResource) Factory.createResource(
						"gate.creole.annotdelete.AnnotationDeletePR", params);

				//Annie tokeniser
				ProcessingResource tokeniser = (ProcessingResource) Factory.createResource(
						"gate.creole.tokeniser.DefaultTokeniser", params);

				//Annie gazetteer
				ProcessingResource gazetteer = (ProcessingResource) Factory.createResource(
						"gate.creole.gazetteer.DefaultGazetteer", params);

				//sharable gazetteer
				FeatureMap paramsSharedGazetteer = Factory.newFeatureMap();
				paramsSharedGazetteer.put("bootstrapGazetteer", gazetteer);
				ProcessingResource sharedGazetteer = (ProcessingResource) Factory.createResource(
						"gate.creole.gazetteer.SharedDefaultGazetteer",paramsSharedGazetteer);

				//Annie splitter
				ProcessingResource splitter = (ProcessingResource) Factory.createResource(
						"gate.creole.splitter.SentenceSplitter", params);

				//Annie POStagger
				ProcessingResource POStagger = (ProcessingResource) Factory.createResource(
						"gate.creole.POSTagger", params);

				//Annie transducer
				ProcessingResource transducer = (ProcessingResource) Factory.createResource(
						"gate.creole.ANNIETransducer", params);

				//Annie orthomatcher
				ProcessingResource orthomatcher = (ProcessingResource) Factory.createResource(
						"gate.creole.orthomatcher.OrthoMatcher", params);

				//load PRs to first Controller
				SerialAnalyserController firstController;
				firstController = (SerialAnalyserController) Factory.createResource(
						"gate.creole.SerialAnalyserController", Factory.newFeatureMap(),
						Factory.newFeatureMap(), "ANNIE_" + Gate.genSym());
				firstController.add(annotDelete);
				firstController.add(tokeniser);
				firstController.add(gazetteer);
				firstController.add(splitter);
				firstController.add(POStagger);
				firstController.add(transducer);
				firstController.add(orthomatcher);

				//load PRs to second Controller. it use a shared gazetteer
				SerialAnalyserController secondController;
				secondController = (SerialAnalyserController) Factory.createResource(
						"gate.creole.SerialAnalyserController", Factory.newFeatureMap(),
						Factory.newFeatureMap(), "ANNIE_" + Gate.genSym());
				secondController.add(annotDelete);
				secondController.add(tokeniser);
				secondController.add(sharedGazetteer); // use shared gazetteer
				secondController.add(splitter);
				secondController.add(POStagger);
				secondController.add(transducer);
				secondController.add(orthomatcher);

				//Create threadpool for Controllers
				pool = new LinkedBlockingQueue<SerialAnalyserController>();

				//add first and second Controller into pool
				pool.add(firstController);
				pool.add(secondController);

				//duplicate the second Controllers then add into pool
				if(POOL_SIZE>2){
					for(int i=2; i<POOL_SIZE;i++){
						pool.add((SerialAnalyserController) Factory.duplicate(secondController));
					}
				}
			}

			catch(Exception ex) {
				throw new ServletException("Exception initialising ANNIE", ex);
			}
		}
	}
	
	public void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		this.doPost(request, response);
	}
       	

	public void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		//upload file full path and file prefix name. used when process the file
		String uploadFileURL = "";
		String uploadFilePrefix = "";

		//set max upload file size
		final long MAX_SIZE = 5*1024*1024;
		//set accept upload file type list
		final String[] allowedExt = new String[] {"txt","htm","html"};
		response.setContentType("text/html");
		//set return page encoding
		response.setCharacterEncoding("UTF-8");
		
		//set a disk file factory
		DiskFileItemFactory factory = new DiskFileItemFactory();

		//set memory to store tmp file when upload large file
		factory.setSizeThreshold(4096);
		//set the tmp directory to store file
		factory.setRepository(new File(this.getServletContext().getRealPath("/") + "UploadTemp"));

		/**
		 * use the Factory to instantiating upload component
		 **/
		ServletFileUpload sfu = new ServletFileUpload(factory);
		//set max upload size
		sfu.setSizeMax(MAX_SIZE);
	
		PrintWriter backWriter = response.getWriter();

		//get list with all upload form data from request
		List fileList = null;
		try{
			fileList = sfu.parseRequest(request);
		}
		catch(Exception e){
			// the total size of the 
			if (e instanceof FileUploadBase.SizeLimitExceededException) {
				backWriter.println("<br>Total file size exceeds. Must be less than " + MAX_SIZE + " Bytes");
				backWriter.println("<br><a href=\"index.jsp\">retry upload file</a>");
				return;
			}

			//process large file size exception
			if(e instanceof FileUploadBase.FileSizeLimitExceededException){
				backWriter.println("<br>Single file size exceeds. Must be less than " + MAX_SIZE + " Bytes");
				backWriter.println("<br><a href=\"index.jsp\">retry upload file</a>");
				return;
			}
			System.out.println(e.getMessage());
		}

		//user didnot upload files
		if (fileList == null||fileList.size()==0){
			backWriter.println("<br>no file been uploaded. please choose a file to upload");
			backWriter.println("<br><a href=\"index.jsp\">retry upload file</a>");
			return;
		}

		//get all the upload files
		Iterator fileItr = fileList.iterator();

		//process each file
		while (fileItr.hasNext()) {
			FileItem fileItem = null;
			String path = "";
			long size = 0;
			// get current file
			fileItem = (FileItem) fileItr.next();
			// ignore simple form field that are not upload filed. eg.<input type="text" />
			if (fileItem == null || fileItem.isFormField()) {
				continue;
			}

			// get file full path
			path = fileItem.getName();
			// get file size
			size = fileItem.getSize();
			if ("".equals(path) || size == 0) {
				backWriter.println("<br>The file size you upload is 0 Bytes. Please upload a file again.");
				backWriter.println("<br><a href=\"index.jsp\">retry upload a file</a>");
				return;
			}

			// get file name (no path)
			String realName = path.substring(path.lastIndexOf("\\") + 1);
			// get file extension name. if the file donot have a extension, use the full name
			String extName = realName.substring(realName.lastIndexOf(".") + 1);
			// reject the file type not in the predefined list

			boolean allowedTag = false;
			for (int i = 0; i < allowedExt.length; i++) {
				if (allowedExt[i].equals(extName)) {
					allowedTag = true;
				}
			}

			if (!allowedTag) {
				backWriter.println("<br>please upload following file type<br>");
				for (int i = 0; i < allowedExt.length; i++) {
					backWriter.println("*." + allowedExt[i] + "<br>");
				}
				backWriter.println("<br><a href=\"index.jsp\">retry upload a file</a>");
				return;
			}


			// generate unique file names to save file
			long now = System.currentTimeMillis();
			String prefix = String.valueOf(now);

			// saved file full path. save it under /UploadFile/
			String finalName = this.getServletContext().getRealPath("/") + "/UploadFile/" + prefix + "." + extName;
			// replace "\" with "/", replace "//" with "/"
			finalName = finalName.replace("\\", "/");
			finalName = finalName.replace("//", "/");

			try {
				fileItem.write(new File(finalName));

/*
 *check whether file upload succesfully
 *
 *				backWriter.println("file upload succesfully under Path:"
						+this.getServletContext().getRealPath("/")+"/UploadFile ."
						+"  saved as£º"+ prefix + "." + extName + ";file size" + size + "bytes<br>");
*/

				//store the file URL, file prefix name to use them when do parsing
				uploadFileURL = finalName;
				uploadFilePrefix = prefix;
			}
			catch (Exception e) {
				System.out.println(e.getMessage());
			}

		}

		backWriter.println("<br><a href=\"index.jsp\">upload another file</a><br><br>");


		//begin to process the doc and output the tagged file
		SerialAnalyserController Annie;
		try{
			//take a controller from the pool
			Annie = pool.take();
		}
		catch(Exception ex) {
			throw new ServletException("Exception taking controller", ex);
		} 


		// create GATE corpus
		try
		{
			Corpus corpus;
		       	corpus	= (Corpus) Factory.createResource("gate.corpora.CorpusImpl");
			//for(int i = 0; i < args.length; i++) {
		 	URL u = new URL("file:"+uploadFileURL);
		  	FeatureMap params = Factory.newFeatureMap();
		  	params.put("sourceUrl", u);
		  	params.put("preserveOriginalContent", new Boolean(true));
		  	params.put("collectRepositioningInfo", new Boolean(true));
		  	Out.prln("Creating doc for " + u);
		  	Document document = (Document) Factory.createResource("gate.corpora.DocumentImpl", params);
		  	corpus.add(document);
		    	//} // for each of args

			// tell the pipeline about the corpus and run it
		    	Annie.setCorpus(corpus);
		   	Annie.execute();

			// for each document, get an XML document with the person and location names added
			Iterator iter = corpus.iterator();
			int count = 0;
			String startTagPart_1 = "<span GateID=\"";
			String startTagPart_2 = "\" title=\"";
			String startTagPart_3 = "\" style=\"background:Blue;\">"; // for type Person
			String startTagPart_4 = "\" style=\"background:Green;\">"; // for type Location
			String startTagPart_5 = "\" style=\"background:Red;\">"; // for type Date
			String endTag = "</span>";
			while(iter.hasNext()) {
				Document doc = (Document) iter.next();
				AnnotationSet defaultAnnotSet = doc.getAnnotations();
				Set annotTypesRequired = new HashSet();
				annotTypesRequired.add("Person");
				annotTypesRequired.add("Location");
				annotTypesRequired.add("Date");
				Set<Annotation> personLocationDate = new HashSet<Annotation>(defaultAnnotSet.get(annotTypesRequired));

				FeatureMap features = doc.getFeatures();
				String originalContent = (String) features.get(GateConstants.ORIGINAL_DOCUMENT_CONTENT_FEATURE_NAME);
				RepositioningInfo info = (RepositioningInfo) features.get(GateConstants.DOCUMENT_REPOSITIONING_INFO_FEATURE_NAME);
				
				++count;

				//set parsed html file URL
				String parsedFileHtmlURL = this.getServletContext().getRealPath("/") + "/ParsedFile/" + uploadFilePrefix + "." + "html";
				// replace "\" in path with "/"
				parsedFileHtmlURL = parsedFileHtmlURL.replace("\\", "/");

				File file = new File(parsedFileHtmlURL);
				Out.prln("File name: '"+file.getAbsolutePath()+"'");
				if(originalContent != null && info != null) {
					Out.prln("OrigContent and reposInfo existing. Generate file...");
					Iterator it = personLocationDate.iterator();
					Annotation currAnnot;
					SortedAnnotationList sortedAnnotations = new SortedAnnotationList();
					
					while(it.hasNext()) {
						currAnnot = (Annotation) it.next();
						sortedAnnotations.addSortedExclusive(currAnnot);
					} // while

					StringBuffer editableContent = new StringBuffer(originalContent);
					long insertPositionEnd;
					long insertPositionStart;
					// insert anotation tags backward
					Out.prln("Unsorted annotations count: "+personLocationDate.size());
					Out.prln("Sorted annotations count: "+sortedAnnotations.size());
					for(int i=sortedAnnotations.size()-1; i>=0; --i) {
						currAnnot = (Annotation) sortedAnnotations.get(i);
						insertPositionStart = currAnnot.getStartNode().getOffset().longValue();
						insertPositionStart = info.getOriginalPos(insertPositionStart);
						insertPositionEnd = currAnnot.getEndNode().getOffset().longValue();
						insertPositionEnd = info.getOriginalPos(insertPositionEnd, true);
						if(insertPositionEnd != -1 && insertPositionStart != -1) {
							editableContent.insert((int)insertPositionEnd, endTag);
							if(currAnnot.getType()=="Person"){
								editableContent.insert((int)insertPositionStart, startTagPart_3);
							}
							else if(currAnnot.getType()=="Location"){
								editableContent.insert((int)insertPositionStart, startTagPart_4);
							}
							else if(currAnnot.getType()=="Date"){
								editableContent.insert((int)insertPositionStart, startTagPart_5);
							}
							else{}
							
							editableContent.insert((int)insertPositionStart, currAnnot.getType());
							editableContent.insert((int)insertPositionStart, startTagPart_2);
							editableContent.insert((int)insertPositionStart, currAnnot.getId().toString());
							editableContent.insert((int)insertPositionStart, startTagPart_1);
						} // if
					} // for
					
					FileWriter writer = new FileWriter(file);
					writer.write(editableContent.toString());
					writer.close();


					//back the parsed html result to user
					backWriter.write(editableContent.toString());
					backWriter.close();

				} // if - should generate
				
				else if (originalContent != null) {
					Out.prln("OrigContent existing. Generate file...");
					Iterator it = personLocationDate.iterator();
					Annotation currAnnot;
					SortedAnnotationList sortedAnnotations = new SortedAnnotationList();
					
					while(it.hasNext()) {
						currAnnot = (Annotation) it.next();
						sortedAnnotations.addSortedExclusive(currAnnot);
					} // while

					StringBuffer editableContent = new StringBuffer(originalContent);
					long insertPositionEnd;
					long insertPositionStart;
					// insert anotation tags backward
					Out.prln("Unsorted annotations count: "+personLocationDate.size());
					Out.prln("Sorted annotations count: "+sortedAnnotations.size());
					for(int i=sortedAnnotations.size()-1; i>=0; --i) {
						currAnnot = (Annotation) sortedAnnotations.get(i);
						insertPositionStart = currAnnot.getStartNode().getOffset().longValue();
						insertPositionEnd = currAnnot.getEndNode().getOffset().longValue();
						if(insertPositionEnd != -1 && insertPositionStart != -1) {
							editableContent.insert((int)insertPositionEnd, endTag);
							if(currAnnot.getType()=="Person"){
								editableContent.insert((int)insertPositionStart, startTagPart_3);
							}
							else if(currAnnot.getType()=="Location"){
								editableContent.insert((int)insertPositionStart, startTagPart_4);
							}
							else if(currAnnot.getType()=="Date"){
								editableContent.insert((int)insertPositionStart, startTagPart_5);
							}
							else{}

							editableContent.insert((int)insertPositionStart, currAnnot.getType());
							editableContent.insert((int)insertPositionStart, startTagPart_2);
							editableContent.insert((int)insertPositionStart, currAnnot.getId().toString());
							editableContent.insert((int)insertPositionStart, startTagPart_1);
						} // if
					} // for

			    		FileWriter writer = new FileWriter(file);
					writer.write(editableContent.toString());
					writer.close();

					//back the parsed html result to user
					backWriter.write(editableContent.toString());
					backWriter.close();
				}
				else {
					Out.prln("Content : "+originalContent);
					Out.prln("Repositioning: "+info);
				}

				String xmlDocument = doc.toXml(personLocationDate, false);

				//set parsed xml file URL
				String parsedFileXMLURL = this.getServletContext().getRealPath("/") + "/ParsedFile/" + uploadFilePrefix + "." + "xml";
				// replace "\" in path with "/"
				parsedFileXMLURL = parsedFileXMLURL.replace("\\", "/");

				FileWriter writer = new FileWriter(parsedFileXMLURL);
				writer.write(xmlDocument);
				writer.close();
				
				// do something usefull with the XML here!
				//Out.prln("'"+xmlDocument+"'");
			} // for each doc


			Factory.deleteResource(document);
			Factory.deleteResource(corpus);
		}
		catch(Exception ex) {
			throw new ServletException("Exception processing corpus", ex);
		}


		//add controller back into pool
		Annie.setCorpus(null);
		pool.add(Annie);
	}

		
	public void destroy() {
		for(SerialAnalyserController c : pool) Factory.deleteResource(c);
	}



public static class SortedAnnotationList extends Vector {
	public SortedAnnotationList() {
		super();
	} // SortedAnnotationList

	public boolean addSortedExclusive(Annotation annot) {
		Annotation currAnot = null;

		// overlapping check
		for (int i=0; i<size(); ++i) {
			currAnot = (Annotation) get(i);
			if(annot.overlaps(currAnot)) {
				return false;
			} // if
		} // for

		long annotStart = annot.getStartNode().getOffset().longValue();
		long currStart;
		
		// insert
		for (int i=0; i < size(); ++i) {
			currAnot = (Annotation) get(i);
			currStart = currAnot.getStartNode().getOffset().longValue();
			if(annotStart < currStart) {
				insertElementAt(annot, i);
				
				/*
				Out.prln("Insert start: "+annotStart+" at position: "+i+" size="+size());
				Out.prln("Current start: "+currStart);
				*/
				return true;
			} // if
		} // for

		int size = size();
		insertElementAt(annot, size);
		//Out.prln("Insert start: "+annotStart+" at size position: "+size);
		return true;
	} // addSorted
} // SortedAnnotationList
}
