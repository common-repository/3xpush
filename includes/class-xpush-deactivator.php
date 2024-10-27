<?php

class xPush_Deactivator_Class
{

    public static function xpush_deactivate() {

	  delete_option('xpush_active');

    }

}
