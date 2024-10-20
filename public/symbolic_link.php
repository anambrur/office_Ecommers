<?php $target = '/home/kzinternational/public_html/kz-international.com/emall/storage/app/public/';

$shortcut = '/home/kzinternational/public_html/kz-international.com/emall/public/storage';
var_dump(symlink($target, $shortcut));
exit;
?>