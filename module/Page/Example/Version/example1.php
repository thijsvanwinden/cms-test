<?php

$versionResolver->get('1');
$versionResolver->mark(1);
$versionResolver->revert(1);


$version->mark();
$version->__toString();


/**************************************************/

$pageProvider->getPageByVersionId();
$pageProvider->markVersionId($versionId);

/**************************************************/

$pageProvider->getPageById($pageId);
$contentProvider->link($contentId, $pageId);

/**************************************************/

$versionProvider->markVersion($contentId, $pageId);
$versionProvider->revertVersion($contentId, $pageId);