<?php

$nodeFilter->filter($nodeRegistry);
$nodeRegistry->getNodes();


$page = $storage->get();

$position = 'sidebar';
$node = ContentNode::factory(array(
            'content' => 'blabla'
        ));

NodeLayout::factory(array(
    'blocks' => array(
        'header' => array(
            'validators' => array(
            )
        ),
        'nav' => array(
            'validators' => array(
            )
        )
    )
));

$slot = NodeSlot::factory(array(
            'name' => 'head',
            'validators' => array()
                )
);
$slot->isValid($node);

$nodeFilter = $slot->toFilter();
$nodeFilter->filter();

$layout = $page->layout();
$layout->addNode($position, $node);

$result = $layout->validate();
if ($result->isValid()) {
    $storage->commit($page);
}



