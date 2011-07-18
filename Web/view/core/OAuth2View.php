<?php
/**
 * @file
 * Generate forms and pages for OAuth2
 */
class OAuth2View extends View {

	public function listClient($client_id, $secret, $code) {
		$html = <<<HTML
		<p>{$client_id}</p>
		<p>{$secret}</p>
		<form method="post" id="client" action="http://hub.plnnr.dev/oauth2/issueToken">
			<input type="hidden" name="grant_type" value="authorization_code" />
			<input type="hidden" name="code" value="{$code}" />
			<input type="hidden" name="redirect_uri" value="http://hub.plnnr.dev/oauth2/listClient" />
			<input type="hidden" name="aassertion_type" value="agafdgrtastr" />
			<input type="hidden" name="assertion" value="gasfdgafdgdfg" />
			<input type="hidden" name="refreshtoren" value="asdfagrfrtrtrtr" />
			<input type="hidden" name="client_id" value="{$client_id}" />
			<input type="hidden" name="client_secret" value="{$secret}" />
			<input type="submit" value="Issue Token"</a>
		</form>
HTML;
	
		print $this->toHTMLPage($html);
	}

	public function addClient() {
		$form = <<<HTML
<form method="post" action="http://hub.plnnr.dev/oauth2/add">
	<p>
		<label for="client_id">Client ID:</label>
		<input type="text" name="client_id" id="client_id" />
	</p>
	<p>
		<label for="client_secret">Client Secret (password/key):</label>
		<input type="text" name="client_secret" id="client_secret" />
	</p>
	<input type="submit" value="Submit" />
</form>
HTML;
	
		print $this->toHTMLPage($form);
	}

}
