<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     eval
 * Purpose:  evaluate a template variable as a template
 * -------------------------------------------------------------
 */
function smarty_function_eval($params, &$smarty)
{
    extract($params);

    if (!isset($var)) {
        $smarty->trigger_error("eval: missing 'var' parameter");
        return;
    }
	if($var == '') {
		return;
	}

	$smarty->_compile_template("evaluated template", $var, $source);
	
    ob_start();
	eval('?>' . $source);
	$contents = ob_get_contents();
    ob_end_clean();

    if (!empty($assign)) {
    	$smarty->assign($assign, $contents);
    } else {
		return $contents;
    }
}

/* vim: set expandtab: */

?>
