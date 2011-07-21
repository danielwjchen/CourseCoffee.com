<html>
	<head>
		<title><?php echo $page['title']; ?></title>
		<?php echo $meta; ?>
		<?php echo $js; ?>
		<?php echo $css; ?>
	</head>
	<body>
	<div class="container">
		<div class="header">
			<div class="header-inner">
				<ul id="navigation-menu">
					<li class="login">
						<div class="error hidden"></div>
						<div class="login-form">
							<form id="user-login-form" name="user-login" action="user/login" method="post">
								<input type="hidden" name="token" value="<?php echo $header['block']['login_token']; ?>" />
								<input type="email" name="email" class="input" value="email" />
								<input type="password" name="password" class="input" value="password" />
								<a class="button login" href="#">login</a>
							</form>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div class="<?php echo $body['css'];?> body">
			<div class="body-inner">
				<?php echo $body['block']; ?>
			</div>
		</div>
		<div class="footer">
			<div class="footer-inner">
			</div>
		</div>
	</div>
	</body>
</html>
