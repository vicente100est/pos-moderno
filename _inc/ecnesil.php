<?php
/*   __________________________________________________________
    |         Secured by ITsolution24                       |
    |    Web: http://itsolution24.com, E-mail: itsolution24bd@gmail.com   |
    |__________________________________________________________|
*/
 ob_start(); session_start(); include "\x2e\56\x2f\137\151\x6e\x69\164\x2e\160\x68\160"; if (is_loggedin()) { goto QjCud; } header("\110\124\124\120\x2f\61\56\x31\40\64\62\62\40\x55\156\160\x72\157\143\145\163\x73\x61\142\x6c\145\40\x45\x6e\x74\x69\164\x79"); header("\103\157\156\164\x65\x6e\x74\55\124\171\160\145\x3a\x20\141\160\x70\x6c\x69\143\x61\164\151\x6f\156\x2f\152\163\157\156\x3b\40\x63\x68\141\x72\163\x65\164\x3d\x55\x54\106\x2d\x38"); echo json_encode(array("\x65\x72\162\x6f\162\115\163\147" => trans("\x65\x72\162\157\162\x5f\x6c\157\147\x69\x6e"))); exit; QjCud: if (!(isset($request->get["\x74\171\x70\x65"]) && $request->get["\164\171\160\x65"] == "\123\x54\117\x43\113\x43\x48\105\x43\x4b")) { goto s5nDu; } echo check_runtime(); s5nDu: