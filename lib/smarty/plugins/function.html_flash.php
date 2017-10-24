<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {html_flash} function plugin
 * Type:     function<br>
 * Name:     html_flash<br>
 * Date:     March 3, 2016<br>
 * Purpose:  format HTML tags for the flash<br>
 * Examples: {html_flash src="/images/masthead.swf" width="200" height="300"}<br>
 * Output:   <object width="200px" height="300" data="/images/masthead.swf"  type="application/x-shockwave-flash">        <param name="quality" value="high">
 *              <param name="allowScriptAccess" value="always">
 *              <param name="wMode" value="opaque">
 *              <param name="swLiveConnect" value="true">
 *              <param name="allowFullScreen" value="true">
 *              <param name="bgColor" value="#000000">
 *            </object>
 * Params:
 * <pre>
 * - src        - (required) - src (and path) of flash
 * - height      - (optional) - flash height (default actual height)
 * - width       - (optional) - flash width (default actual width)
 * - type     - (optional) - base type for type, default is "application/x-shockwave-flash"
 * 
 * </pre>
 *
 * @link    http://www.smarty.net/manual/en/language.function.html.flash.php {html_flash}
 *          (Smarty online manual)
 * @author  Mr Jiang <monte at ohrt dot com>
 * @author  credits to Duda <duda@big.hu>
 * @version 1.0
 *
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 *
 * @throws SmartyException
 * @return string
 * @uses    smarty_function_escape_special_chars()
 */
function smarty_function_html_flash($params, $template)
{
    require_once(SMARTY_PLUGINS_DIR . 'shared.escape_special_chars.php');

    
    $src = '';
    $height = '';
    $width = '';
    $extra = '';
    $prefix = '';
    $suffix = '';
    $path_prefix = '';
    $type="application/x-shockwave-flash";
    $basedir = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'src':
            case 'height':
            case 'width':
            case 'dpi':
            case 'path_prefix':
            case 'basedir':
            case 'type':
                $$_key = $_val;
                break;

            case 'link':
            case 'href':
                $prefix = '<a href="' . $_val . '">';
                $suffix = '</a>';
                break;

            default:
                if (!is_array($_val)) {
                    $extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_val) . '"';
                } else {
                    throw new SmartyException ("html_flash: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    if (empty($src)) {
        trigger_error("html_flash: missing 'src' parameter", E_USER_NOTICE);

        return;
    }

    if ($src[0] == '/') {
        $_flash_path = $basedir . $src;
    } else {
        $_flash_path = $src;
    }

    // strip src protocol
    if (stripos($params['src'], 'file://') === 0) {
        $params['src'] = substr($params['src'], 7);
    }

    $protocol = strpos($params['src'], '://');
    if ($protocol !== false) {
        $protocol = strtolower(substr($params['src'], 0, $protocol));
    }

    if (isset($template->smarty->security_policy)) {
        if ($protocol) {
            // remote resource (or php stream, …)
            if (!$template->smarty->security_policy->isTrustedUri($params['src'])) {
                return;
            }
        } else {
            // local src
            if (!$template->smarty->security_policy->isTrustedResourceDir($_flash_path)) {
                return;
            }
        }
    }

    if (!isset($params['width']) || !isset($params['height'])) {
        // FIXME: (rodneyrehm) getimagesize() loads the complete src off a remote resource, use custom [jpg,png,gif]header reader!
        if (!$_flash_data = @getimagesize($_flash_path)) {
            if (!src_exists($_flash_path)) {
                trigger_error("html_flash: unable to find '$_flash_path'", E_USER_NOTICE);

                return;
            } elseif (!is_readable($_flash_path)) {
                trigger_error("html_flash: unable to read '$_flash_path'", E_USER_NOTICE);

                return;
            } else {
                trigger_error("html_flash: '$_flash_path' is not a valid image src", E_USER_NOTICE);

                return;
            }
        }

        if (!isset($params['width'])) {
            $width = $_flash_data[0];
        }
        if (!isset($params['height'])) {
            $height = $_flash_data[1];
        }
    }

    if (isset($params['dpi'])) {
        if (strstr($_SERVER['HTTP_USER_AGENT'], 'Mac')) {
            // FIXME: (rodneyrehm) wrong dpi assumption
            // don't know who thought this up… even if it was true in 1998, it's definitely wrong in 2011.
            $dpi_default = 72;
        } else {
            $dpi_default = 96;
        }
        $_resize = $dpi_default / $params['dpi'];
        $width = round($width * $_resize);
        $height = round($height * $_resize);
    }

   
    return $prefix .
              '<object  width="'.$width.'"  height="'.$height.'" data="'.$src.'"  type="'.$type.'"  '.$extra.' >
               <param name="quality" value="high">
               <param name="allowScriptAccess" value="always">
               <param name="wMode" value="opaque">
               <param name="swLiveConnect" value="true">
               <param name="allowFullScreen" value="true">
               <param name="bgColor" value="#000000">
               </object>'.
            $suffix;


}
