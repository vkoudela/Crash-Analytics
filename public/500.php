<?php

if (!isset($message)) {
	// if there is no message set, then this request probably came directly to
	// this error file, which doesn't make any sense

	header('Location: /');
	exit(0);
}

if (!isset($code)) {
	$file = basename(__FILE__);
	$code = (int) substr($file, 0, strpos($file, '.'));
}

if (isset($exception) && class_exists('\Koldy\Application') && \Koldy\Application::inProduction()) {
	$message = 'Internal Server Error';
}

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Error <?= $code ?></title>

		<!-- Bootstrap -->
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="container" style="margin-top: 10px">
			<div class="col-md-12">
				<div class="jumbotron">
					<h1>Error <?= $code ?></h1>
					<p>
						Koldy framework gave you the error page <code>:(</code> Sorry about that.
					</p>
					<p>
						<code><?= $message ?></code>
					</p>
					
					<?php if (isset($exception) && class_exists('\Koldy\Application') && \Koldy\Application::inDevelopment()) : ?>
						<pre class="text-danger"><?= $exception->getTraceAsString() ?></pre>
					<?php endif; ?>
					<p>
						<a class="btn btn-success btn-lg" role="button" href="/"><span class="glyphicon glyphicon-home"></span> Go home</a>
						<button class="btn btn-success btn-lg" onclick="window.location.reload()"><span class="glyphicon glyphicon-refresh"></span> Retry</button>
					</p>
				</div>
				
			</div><!-- /row -->
		</div><!-- /container -->

		<script src="https://code.jquery.com/jquery.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	</body>
</html>