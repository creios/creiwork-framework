<?php
/** @var \Psr\Http\Message\ServerRequestInterface $request */
/** @var string $data */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Creiwork</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h1>Creiwork</h1>
    <ol class="breadcrumb">
        <li>
            <a href="http://<?= $request->getUri()->getHost() ?>">
                <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
            </a>
        </li>
        <li>
            <a href="http://<?= $request->getUri() ?>"><?= trim($request->getUri()->getPath(), '/'); ?></a>
        </li>
    </ol>
    <pre><?php echo $this->e($data) ?></pre>
</div>
</body>
</html>
