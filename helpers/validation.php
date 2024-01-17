<?php

    function validate_string_not_less($str = '', $length = 5) {

        if (strlen($str) >= $length) {
            return true;
        }
        else {
            return false;
        }

    }