<?php
if ($modx->event->name == 'OnHandleRequest') {
    $modx->lexicon->load('sprout:default');
}