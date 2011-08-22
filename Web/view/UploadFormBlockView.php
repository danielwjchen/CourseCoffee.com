<?php
/**
 * @file
 * Define upload form block
 */
class UploadFormBlockView extends BlockView implements BlockViewInterface {

	/**
	 * Implement BlockViewInterface::gEtcontentlear()
	 */
	public function getContent() {
		$this->addJS('model/doc.js');
		return <<<HTML
<div class="upload-form">
	<form class="hidden" id="doc-upload-form-skeleton" enctype="multipart/form-data" name="doc-upload" action="?q=doc-upload" method="post">
		<input type="hidden" name="token" />
		<input type="file" name="document" />
		<div class="error hidden"></div>
		<a class="button submit" href="#">upload</a>
	</form>
	<a class="button upload" href="#">upload syllabus</a>
</div>
HTML;
	}
}
