<?php

    function validate_password($password) {
        if (validate_string_not_less($password, 8)) {
            return true;
        }
        else {
            return false;
        }
    }
