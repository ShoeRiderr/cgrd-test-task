<?php

// Place for global helper functions

function isUserLoggedIn(): bool
{
    return isset($_SESSION['id']);
}
