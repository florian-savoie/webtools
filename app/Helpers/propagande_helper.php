<?php

function getPropagandeData() {
    $existingContent = file_get_contents("assets/json/propagande/propagande.json");
    $existingData = json_decode($existingContent, true);
    return $existingData['Propagande'];
}