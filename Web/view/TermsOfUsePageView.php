<?php
/**
 * @file
 * Generate terms of use page
 */
class TermsOfUsePageView extends PageView implements PageViewInterface {

	/**
	 * Extend PageView::__construct().
	 */
	function __construct($data = null) {
		parent::__construct($data);
		$this->setPageTitle('terms of use');
		$this->addCSS('terms-of-use.css');
	}

	/**
	 * Implement PageViewInterface::getBlocks()
	 */
	public function getBlocks() {
		return array(
			'header' => array(
				'LogoHeaderBlockView',
			),
			'footer' => array(
				'FooterBlockView',
			),
		);
	}

	/**
	 * Implement PageViewInterface::getContent()
	 */
	public function getContent() {
		extract($this->data);
		return <<<HTML
<div class="container">
	<div class="container-inner">
		<div class="header">
			<div class="header-inner">
				{$header}
			</div>
		</div>
		<div class="terms-of-use body">
			<div class="body-inner">
				<div class="content">
					<h2>Conditions of Use</h2>
					<p>Welcome to coursecoffee.com!  Coursecoffee.com and its associates provide their services to you subject to the following conditions. If you visit or use this website, you accept these conditions. Please read them carefully.</p>
					<h3>ELECTRONIC COMMUNICATIONS</h3>
					<p>When you visit coursecoffee.com or send e-mails to us, you are communicating with us electronically. You consent to receive communications from us electronically. We will communicate with you by e-mail or by posting notices on this site. You agree that all agreements, notices, disclosures and other communications that we provide to you electronically satisfy any legal requirement that such communications be in writing.</p>
					<h3>COPYRIGHT</h3>
					<p>All content included on this site, such as text, graphics, logos, button icons, images, audio clips, digital downloads, data compilations, and software, is the property of Top Scholar Technologies, LLC, which retains all ownership of coursecoffee.com, or its content suppliers and is protected by international copyright laws. The compilation of all content on this site is the exclusive property of Top Scholar Technologies, with copyright authorship for this collection by Top Scholar Technologies, and protected by international copyright laws.</p>
					<h3>TRADE MARKS</h3>
					<p>Top Scholar Technologies trademarks and trade dress may not be used in connection with any product or service that is not Top Scholar Technologies', in any manner that is likely to cause confusion among customers, or in any manner that disparages or discredits Top Scholar Technologies. All other trademarks not owned by Top Scholar Technologies or its subsidiaries that appear on this site are the property of their respective owners, who may or may not be affiliated with, connected to, or sponsored by Top Scholar Technologies or its subsidiaries.</p>
					<h3>INTELLECTUAL PROPERTY</h3>
					<p>Top Scholar Technologies maintains ownership over all intellectual property present on its websites, including coursecoffee.com. By signing this agreement, you agree not to develop any duplicate websites or derivative websites of any kind.</p>
					<h3>LICENSE AND SITE ACCESS</h3>
					<p>Top Scholar Technologies grants you a limited license to access and make personal use of this site and not to download (other than page caching) or modify it, or any portion of it, except with express written consent of Top Scholar Technologies. This license does not include any resale or commercial use of this site or its contents: any collection and use of any product listings, descriptions, or prices: any derivative use of this site or its contents: any downloading or copying of account information for the benefit of another merchant: or any use of data mining, robots, or similar data gathering and extraction tools. This site or any portion of this site may not be reproduced, duplicated, copied, sold, resold, visited, or otherwise exploited for any commercial purpose without express written consent of Top Scholar Technologies. You may not frame or utilize framing techniques to enclose any trademark, logo, or other proprietary information (including images, text, page layout, or form) of Top Scholar Technologies and our associates without express written consent. You may not use any meta tags or any other "hidden text" utilizing Top Scholar Technologies' name or trademarks without the express written consent of Top Scholar Technologies. Any unauthorized use terminates the permission or license granted by Top Scholar Technologies. You are granted a limited, revocable, and nonexclusive right to create a hyperlink to the home page of Top Scholar Technologies so long as the link does not portray Top Scholar Technologies, its associates, or their products or services in a false, misleading, derogatory, or otherwise offensive matter. You may not use any Top Scholar Technologies logo or other proprietary graphic or trademark as part of the link without express written permission.</p>
					<h3>YOUR MEMBERSHIP ACCOUNT</h3>
					<p>If you use this site, you are responsible for maintaining the confidentiality of your account and password and for restricting access to your computer, and you agree to accept responsibility for all activities that occur under your account or password. If you are under 18, you may use our website only with involvement of a parent or guardian. Top Scholar Technologies and its associates reserve the right to refuse service, terminate accounts, remove or edit content, or cancel orders in their sole discretion.</p>
					<h3>REVIEWS, COMMENTS, EMAILS, AND OTHER CONTENT</h3>
					<p>Visitors may post reviews, comments, and other content: and submit suggestions, ideas, comments, questions, or other information, so long as the content is not illegal, obscene, threatening, defamatory, invasive of privacy, infringing of intellectual property rights, or otherwise injurious to third parties or objectionable and does not consist of or contain software viruses, political campaigning, commercial solicitation, chain letters, mass mailings, or any form of "spam." You may not use a false e-mail address, impersonate any person or entity, or otherwise mislead as to the origin of a card or other content. Top Scholar Technologies reserves the right (but not the obligation) to remove or edit such content, but does not regularly review posted content. If you do post content or submit material, and unless we indicate otherwise, you grant Top Scholar Technologies and its associates a nonexclusive, royalty-free, perpetual, irrevocable, and fully sublicensable right to use, reproduce, modify, adapt, publish, translate, create derivative works from, distribute, and display such content throughout the world in any media. You grant Top Scholar Technologies and its associates and sublicensees the right to use the name that you submit in connection with such content, if they choose. You represent and warrant that you own or otherwise control all of the rights to the content that you post: that the content is accurate: that use of the content you supply does not violate this policy and will not cause injury to any person or entity: and that you will indemnify Top Scholar Technologies or its associates for all claims resulting from content you supply. Top Scholar Technologies has the right but not the obligation to monitor and edit or remove any activity or content. Top Scholar Technologies takes no responsibility and assumes no liability for any content posted by you or any third party.</p>
					<h3>RISK OF LOSS</h3>
					<p>All items purchased from Top Scholar Technologies are made pursuant to a shipment contract. This basically means that the risk of loss and title for such items pass to you upon our delivery to the carrier.</p>
					<h3>PRODUCT DESCRIPTIONS</h3>
					<p>Top Scholar Technologies and its associates attempt to be as accurate as possible. However, Top Scholar Technologies does not warrant that product descriptions or other content of this site is accurate, complete, reliable, current, or error-free. </p>
					<h3>DISCLAIMER OF WARRANTIES AND LIMITATION OF LIABILITY</h3>
					<p> THIS SITE IS PROVIDED BY TOP SCHOLAR TECHNOLOGIES ON AN "AS IS" AND "AS AVAILABLE" BASIS. TOP SCHOLAR TECHNOLOGIES MAKES NO REPRESENTATIONS OR WARRANTIES OF ANY KIND, EXPRESS OR IMPLIED, AS TO THE OPERATION OF THIS SITE OR THE INFORMATION, CONTENT, MATERIALS, OR PRODUCTS INCLUDED ON THIS SITE. YOU EXPRESSLY AGREE THAT YOUR USE OF THIS SITE IS AT YOUR SOLE RISK. TO THE FULL EXTENT PERMISSIBLE BY APPLICABLE LAW, TOP SCHOLAR TECHNOLOGIES DISCLAIMS ALL WARRANTIES, EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. TOP SCHOLAR TECHNOLOGIES DOES NOT WARRANT THAT THIS SITE, ITS SERVERS, OR E-MAIL SENT FROM TOP SCHOLAR TECHNOLOGIES ARE FREE OF VIRUSES OR OTHER HARMFUL COMPONENTS. TOP SCHOLAR TECHNOLOGIES WILL NOT BE LIABLE FOR ANY DAMAGES OF ANY KIND ARISING FROM THE USE OF THIS SITE, INCLUDING, BUT NOT LIMITED TO DIRECT, INDIRECT, INCIDENTAL, PUNITIVE, AND CONSEQUENTIAL DAMAGES. CERTAIN STATE LAWS DO NOT ALLOW LIMITATIONS ON IMPLIED WARRANTIES OR THE EXCLUSION OR LIMITATION OF CERTAIN DAMAGES. IF THESE LAWS APPLY TO YOU, SOME OR ALL OF THE ABOVE DISCLAIMERS, EXCLUSIONS, OR LIMITATIONS MAY NOT APPLY TO YOU, AND YOU MIGHT HAVE ADDITIONAL RIGHTS.</p>
					<h3>APPLICABLE LAW</h3>
					<p>By visiting coursecoffee.com, you agree that the laws of the state of Michigan, USA, without regard to principles of conflict of laws, will govern these Conditions of Use and any dispute of any sort that might arise between you and Top Scholar Technologies or its associates.</p>
					<h3>DISPUTES</h3>
					<p>Any dispute relating in any way to your visit to Top Scholar Technologies or to products you purchase through Top Scholar Technologies shall be submitted to confidential arbitration in Michigan, USA, except that, to the extent you have in any manner violated or threatened to violate Top Scholar Technologies intellectual property rights, Top Scholar Technologies may seek injunctive or other appropriate relief in any state or federal court in the state of Michigan, USA, and you consent to exclusive jurisdiction and venue in such courts. Arbitration under this agreement shall be conducted under the rules then prevailing of the American Arbitration Association. The arbitrators award shall be binding and may be entered as a judgment in any court of competent jurisdiction. To the fullest extent permitted by applicable law, no arbitration under this Agreement shall be joined to an arbitration involving any other party subject to this Agreement, whether through class arbitration proceedings or otherwise.</p>
					<h3>SITE POLICIES, MODIFICATION, AND SEVERABILITY</h3>
					<p>We reserve the right to make changes to our site, policies, and these Conditions of Use at any time. If any of these conditions shall be deemed invalid, void, or for any reason unenforceable, that condition shall be deemed severable and shall not affect the validity and enforceability of any remaining condition.</p>
					<h3>QUESTIONS:</h3>
					<p>Questions regarding our Conditions of Usage, Privacy Policy, or other policy related material can be directed to our support staff by clicking on the "Contact Us" link in the side menu. Or you can email us at: <a href="mailto:contact@coursecoffee.com">contact@coursecoffee.com</a>.</p>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="footer">
	<div class="footer-inner">
		{$footer}
	</div>
</div>
HTML;
	}
}
