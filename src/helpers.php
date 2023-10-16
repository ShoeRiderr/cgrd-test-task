<?php

function is_user_logged_in(): bool
{
    return isset($_SESSION['id']);
}